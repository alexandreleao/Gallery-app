<?php 

namespace App\interfaces;
use App\Models\Image;

interface imageServiceInterface{
   
    public function storeNewImage($image, $title): Image;
    public function deleteImageFromDisk($imageUrl) : bool;
    public function deleteDatabaseImage($DatabaseImage): bool;
}