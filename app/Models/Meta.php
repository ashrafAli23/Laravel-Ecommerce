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

    public function getId(): int
    {
        return $this->id;
    }
    public function getMetaKey(): string
    {
        return $this->meta_key;
    }

    public function getMetaValue(): string
    {
        return $this->meta_value;
    }

    public function getReferenceId(): int
    {
        return $this->reference_id;
    }

    public function getReferenceType(): string
    {
        return $this->reference_type;
    }
}