<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Meta extends Model
{
    use HasFactory;

    protected $fillable = [
        'meta_key',
        'meta_value',
        'reference_id',
        'reference_type',
    ];
}