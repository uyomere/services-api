<?php

namespace App\Trait;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

trait FileUploadTrait
{
    function uploadImage(Request $request, $inputName, $path = '/uploads')
    {
        if ($request->hasFile($inputName)) {

            $image = $request->{$inputName};
            $ext = $image->getClientOriginalExtension();
            $imageName = 'media_ ' . uniqid() . '.' . $ext;

            $image->move(public_path($path), $imageName);
            return $path . '/' . $imageName;
        }
    }

    //remove image 
    function removeImage($path)
    {
        if (File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
