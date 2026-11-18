<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\ProductThumbnail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'barcode',
        'price',
        'default_qty',
        'image_path',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:3',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function imageUrl(): string
    {
        return $this->image_path
            ? asset('storage/' . $this->image_path)
            : asset('images/placeholder.png');
    }

    /** Small card image; falls back to the original if the thumbnail isn't built yet. */
    public function thumbUrl(): string
    {
        $thumb = ProductThumbnail::pathFor($this->image_path);

        return $thumb && Storage::disk('public')->exists($thumb)
            ? asset('storage/' . $thumb)
            : $this->imageUrl();
    }
}
