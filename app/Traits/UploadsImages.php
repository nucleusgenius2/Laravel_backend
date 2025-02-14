<?php

namespace App\Traits;

use App\DTO\DataEmptyDto;
use App\DTO\DataStringDto;
use Illuminate\Support\Facades\File;


trait UploadsImages
{

    protected function uploadImage(?object $img, string $path=''): DataStringDto
    {
        $destinationPath = $path ==='' ? public_path('images') : public_path($path);

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $imageName = time() . '.' . $img->extension();

        if ($img->move($destinationPath, $imageName)) {
            $imgPath = $path ==='' ? '/images/' . $imageName : '/'.$path .'/'. $imageName;

            return new DataStringDto(status: true, data: $imgPath );
        } else {
            return new DataStringDto(status: false, error: 'Не удалось загрузить изображение.');
        }

    }

    protected function deleteImage(string $path): DataEmptyDto
    {
        $pathDelete = public_path($path);

        if (!File::exists($pathDelete )) {
            return new DataEmptyDto(status: false, error: 'Файл не найден' );
        }

        if (!File::isWritable($pathDelete)) {
            return new DataEmptyDto(status: false, error: 'Файл недоступен для удаления');
        }

        $removeImg = File::delete($pathDelete);

        if ( $removeImg ) {
            return new DataEmptyDto(status: true );
        }
        else{
            return new DataEmptyDto(status: false, error: 'Файл не удален');
        }
    }
}
