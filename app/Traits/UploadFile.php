<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait UploadFile
{
    public function uploadFile(Request $request, string $file_name = null, string $path): string
    {
        $imageName = Str::replace(' ', '', $request->file($file_name ?? "image")->getClientOriginalName());
        $path = $request->file($file_name ?? "image")->storeAs($path, rand(1, 99999) . $imageName, 'public');

        return 'storage/' . $path;
        // return asset('storage/app/public/' . $path);
    }

    public function uploadMultipleFile(Request $request, string $file_name = null, string $path): array
    {
        $image_data = [];
        foreach ($request->file($file_name ?? 'images') as $image) {
            $imageName = Str::replace(' ', '', $image->getClientOriginalName());
            $image_data[] = 'storage/' . $image->storeAs($path, rand(1, 99999) . $imageName, 'public');
        }

        return $image_data;
        // return asset('storage/app/public/' . $path);
    }

    public function deleteFile(string $path): void
    {
        $file = file_exists($path);
        if ($file) {
            unlink($path);
        }
    }
}