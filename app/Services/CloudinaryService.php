<?php

namespace App\Services;

class CloudinaryService
{
    /**
     * Upload an image to Cloudinary and return the secure URL.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string
     */
    public function uploadImage($file)
    {

        return cloudinary()->upload($file->getRealPath())->getSecurePath();
    }
}