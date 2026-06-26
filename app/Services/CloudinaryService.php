<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class CloudinaryService
{
    protected $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary(
            Configuration::instance(env('CLOUDINARY_URL'))
        );
    }

    public function uploadImage($file)
    {
        $result = $this->cloudinary->uploadApi()->upload($file->getRealPath(), [
            'resource_type' => 'auto'
        ]);

        return $result['secure_url'];
    }
}