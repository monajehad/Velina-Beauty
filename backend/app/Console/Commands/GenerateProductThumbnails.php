<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Support\ProductThumbnail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateProductThumbnails extends Command
{
    protected $signature = 'products:thumbs {--force : Rebuild thumbnails that already exist}';

    protected $description = 'Create small catalog thumbnails for product photos';

    public function handle(): int
    {
        ini_set('memory_limit', '512M');
        $disk = Storage::disk('public');
        $made = $skipped = $failed = 0;

        Product::whereNotNull('image_path')->orderBy('id')->each(function (Product $product) use ($disk, &$made, &$skipped, &$failed) {
            $thumb = ProductThumbnail::pathFor($product->image_path);
            if (! $this->option('force') && $disk->exists($thumb)) {
                $skipped++;
                return;
            }
            if (ProductThumbnail::make($product)) {
                $made++;
            } else {
                $failed++;
                $this->warn("Could not create thumbnail for product #{$product->id} ({$product->image_path})");
            }
        });

        $this->info("Created: {$made}, already existed: {$skipped}, failed: {$failed}");

        return self::SUCCESS;
    }
}
