<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait UploadFile
{
    public function uploadFile(Request $request, string $path): string
    {
        $imageName = Str::replace(' ', '', $request->file("image")->getClientOriginalName());
        $path = $request->file("image")->storeAs($path, rand(1, 99999) . $imageName, 'public');

        return 'storage/' . $path;
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
