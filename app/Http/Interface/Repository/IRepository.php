<?php

declare(strict_types=1);

namespace App\Http\Interface\Repository;

interface IRepository
{
    public function index(): object|null;
    public function show(int $id): object|null;
    public function store(array $data): object|null;
    public function update(int $id): object|null;
    public function delete(int $id): object|null;
}
