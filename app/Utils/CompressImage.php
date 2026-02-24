<?php

namespace App\Utils;

use Intervention\Image\Laravel\Facades\Image;
use Storage;
class CompressImage
{
    public static function save(string $file, $folder, $quality = 75)
    {

        try {

            $image = Image::read($file);

            // 1. Procesar la imagen
            $encoded = $image->scale(width: 800)
                ->encodeByExtension('jpg', quality: $quality);

            $path = $folder . '/' . uniqid() . '.jpg';

            Storage::disk('public')->put($path, $encoded);

            // 3. Retornar el path para guardarlo en la base de datos
            return $path;

        } catch (\Exception $e) {
            return Storage::disk('public')->putFile($folder, $file);
        }
    }

}