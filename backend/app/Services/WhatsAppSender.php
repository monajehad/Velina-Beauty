<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Sends a document (the order PDF) to a WhatsApp number from the server.
 *
 * Off until WHATSAPP_DRIVER is set in .env:
 *   WHATSAPP_DRIVER=meta      -> WhatsApp Business Cloud API (official)
 *     WHATSAPP_META_TOKEN, WHATSAPP_META_PHONE_NUMBER_ID
 *     Optional WHATSAPP_META_TEMPLATE (+ WHATSAPP_META_TEMPLATE_LANG, default "en"):
 *     an approved template with a DOCUMENT header and one body variable {{1}} = order number.
 *     Needed when the store hasn't messaged the business number in the last 24 hours.
 *   WHATSAPP_DRIVER=ultramsg  -> UltraMsg gateway
 *     WHATSAPP_ULTRAMSG_INSTANCE, WHATSAPP_ULTRAMSG_TOKEN
 *
 * Uses PHP's curl extension directly (no extra Composer packages on the host).
 */
class WhatsAppSender
{
    public static function enabled(): bool
    {
        return match (config('services.whatsapp.driver')) {
            'meta' => filled(config('services.whatsapp.meta.token')) && filled(config('services.whatsapp.meta.phone_number_id')),
            'ultramsg' => filled(config('services.whatsapp.ultramsg.instance')) && filled(config('services.whatsapp.ultramsg.token')),
            default => false,
        };
    }

    /** @param string $documentUrl public https URL the provider downloads the file from */
    public function sendDocument(string $to, string $documentUrl, string $filename, string $caption, string $orderNumber): bool
    {
        $to = preg_replace('/\D/', '', $to);

        return match (config('services.whatsapp.driver')) {
            'meta' => $this->sendViaMeta($to, $documentUrl, $filename, $caption, $orderNumber),
            'ultramsg' => $this->sendViaUltraMsg($to, $documentUrl, $filename, $caption),
            default => false,
        };
    }

    private function sendViaMeta(string $to, string $url, string $filename, string $caption, string $orderNumber): bool
    {
        $cfg = config('services.whatsapp.meta');
        $template = $cfg['template'] ?? null;

        $payload = $template
            ? [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'template',
                'template' => [
                    'name' => $template,
                    'language' => ['code' => $cfg['template_lang'] ?: 'en'],
                    'components' => [
                        ['type' => 'header', 'parameters' => [
                            ['type' => 'document', 'document' => ['link' => $url, 'filename' => $filename]],
                        ]],
                        ['type' => 'body', 'parameters' => [
                            ['type' => 'text', 'text' => $orderNumber],
                        ]],
                    ],
                ],
            ]
            : [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'document',
                'document' => ['link' => $url, 'filename' => $filename, 'caption' => $caption],
            ];

        $endpoint = sprintf('https://graph.facebook.com/%s/%s/messages', $cfg['version'] ?: 'v21.0', $cfg['phone_number_id']);

        return $this->post($endpoint, json_encode($payload), [
            'Authorization: Bearer ' . $cfg['token'],
            'Content-Type: application/json',
        ]);
    }

    private function sendViaUltraMsg(string $to, string $url, string $filename, string $caption): bool
    {
        $cfg = config('services.whatsapp.ultramsg');
        $endpoint = sprintf('https://api.ultramsg.com/%s/messages/document', $cfg['instance']);

        return $this->post($endpoint, http_build_query([
            'token' => $cfg['token'],
            'to' => '+' . $to,
            'filename' => $filename,
            'document' => $url,
            'caption' => $caption,
        ]), ['Content-Type: application/x-www-form-urlencoded']);
    }

    private function post(string $url, string $body, array $headers): bool
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
        ]);
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        // UltraMsg answers 200 with {"error": ...} on failures
        $json = is_string($response) ? json_decode($response, true) : null;
        $ok = $status >= 200 && $status < 300 && empty($json['error']);

        if (! $ok) {
            Log::warning('WhatsApp send failed', ['status' => $status, 'error' => $error, 'response' => $response]);
        }

        return $ok;
    }
}
