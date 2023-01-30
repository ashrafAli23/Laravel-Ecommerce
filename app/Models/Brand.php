<?php

declare(strict_types=1);

namespace App\Models;

use BaseStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'slug',
        'description',
        'featured',
        'status',
        'link',
        'total_sale'
    ];

    protected $cast = [
        'featured' => 'boolean',
        'status' => BaseStatusEnum::class
    ];

    public function getId(): int
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getImage(): string
    {
        return $this->image;
    }
    public function getSlug(): string
    {
        return $this->slug;
    }
    public function getDescription(): string
    {
        return $this->description;
    }

    public function getFeatured(): bool
    {
        return $this->featured;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function getLink(): string
    {
        return $this->link;
    }

    public function getTotalSale(): int
    {
        return $this->total_sale;
    }

    public function products(): HasMany
    {
        return $this->HasMany(Product::class);
    }
}