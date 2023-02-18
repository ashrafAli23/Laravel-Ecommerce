<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttribute extends Model
{
    use HasFactory;

    protected array $fillable = [
        'attribute_set_id',
        'value',
        'color',
        'image',
    ];

    public function ProductAttrSet(): BelongsTo
    {
        return $this->belongsTo(ProductAttributeSet::class, 'attribute_set_id');
    }
}