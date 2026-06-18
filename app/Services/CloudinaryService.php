<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class CloudinaryService
{
    protected $cloudinary;

    public function __construct()
    {
        // তোমার .env ফাইলে থাকা CLOUDINARY_URL ব্যবহার করে কনফিগারেশন করা
        $this->cloudinary = new Cloudinary(
            Configuration::instance(env('CLOUDINARY_URL'))
        );
    }

    public function uploadImage($file)
    {

        $result = $this->cloudinary->uploadApi()->upload($file->getRealPath());

        return $result['secure_url'];
    }
}