<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'price', 'main_image_id'];

    /**
     * Get the Main Image that have the Product
     */
    public function mainImage(): BelongsTo
    {
        return $this->belongsTo(ProductImage::class, 'main_image_id');
    }

    /**
     * Get Image Items for the Product.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }
}
