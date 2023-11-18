<?php

namespace App\Services;

use Image;
use Illuminate\Support\Str;


class FileService
{
    public function updateImage($model, $request)
    {
        $image = Image::make($request->file('image'));

        if (!empty($model->image)) {
            $currentImage = public_path() . $model->image;

            if (file_exists($currentImage) && $currentImage != public_path() . '/user-placeholder.png') {
                unlink($currentImage);
            }
        }

        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();

        $image->crop(
            $request->width,
            $request->height,
            $request->left,
            $request->top
        );

        $name = time() . '.' . $extension;
        $image->save(public_path() . '/files/' . $name);
        $model->image = '/files/' . $name;

        return $model;
    }

    public function addVideo($model, $request)
    {
        $video = $request->file('video');
        $extension = $video->getClientOriginalExtension();

        $name = str::random() . '.' . $extension;
        copy($video->path(), public_path() . '/files/' . $name);
        //$video->move(public_path() . '/files/', $name); //TODO UNCOMMENT THIS LINE AND DELETE THE LINE ABOVE
        $model->video = '/files/' . $name;

        return $model;
    }
}
