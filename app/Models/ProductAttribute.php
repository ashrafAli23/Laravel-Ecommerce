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

    public function getId(): int
    {
        return $this->id;
    }

    public function getAttrSetId(): int
    {
        return $this->attribute_set_id;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function ProductAttrSet(): BelongsTo
    {
        return $this->belongsTo(ProductAttributeSet::class, 'attribute_set_id');
    }
}