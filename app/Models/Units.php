<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Units extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'short_name'
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
