<?php

namespace App\Services;

use App\interfaces\imageServiceInterface;
use App\Models\Image;
use Error;
use Exception;
use Illuminate\Support\Facades\Storage;

class ImageService implements imageServiceInterface
{
    private $rollbackStack =  null;

    public function storeNewImage($image, $title): Image
    {

        try {
            $url = $this->storeImageInDisk($image);
            return  $this->storageImageInDatabase($title, $url);
        } catch (Exception $e) {

            throw new Error('Erro ao salvar a imagem. Tente novamente.');
        }
    }

    public function deleteImageFromDisk($imageUrl): bool
    {

        echo '<- deleteImageFromDisk <br>';

        $imagePath = str_replace(asset('storage/', ''), '', $imageUrl);
        Storage::disk('public')->delete($imagePath);
        return true;
    }

    public function deleteDatabaseImage($databaseImage): bool
    {   
        echo '<- deleteDatabaseImage <br>';

        if ($databaseImage) {
            $databaseImage->delete();
            return true;
        }
        return false;
    }

    public function rollback()
    {

        while (!empty($this->rollbackStack)) {
            $rollbackAction = array_pop($this->rollbackStack);

            $method = $rollbackAction['method'];
            $params = $rollbackAction['params'];

            if (method_exists($this, $method)) {
                call_user_func_array([$this, $method], $params);
            }

            dd();
        }
        // if (!empty($this->rollbackQueue)) {

        //     foreach ($this->rollbackQueue as $interaction) {
        //         $method = $interaction['method'];
        //         $params = $interaction['params'];


        //         if (method_exists($this, $method)) {
        //             call_user_func_array([$this, $method], $params);
        //         }
        //     }
        //     dd();
        // }

    }

    private function storeImageInDisk($image): string
    {
        echo '-> storeImageInDisk <br>';

        $imageName = $image->storePublicly('uploads', 'public');
        $url = asset('storage/' . $imageName);
        $this->addToRollbackQueue('deleteImageFromDisk', [$url]);


        return $url;
    }

    private function storageImageInDatabase($title, $url): Image
    {

        echo '-> storageImageInDatabase <br>';
       

        $image = Image::create([
            'title' => $title,
            'url' => $url
        ]);

        $this->addToRollbackQueue('deleteDatabaseImage', [$image]);

        return $image;
    }

    private function addToRollbackQueue($method, $params = [])
    {

        if (is_null($this->rollbackStack)) {

            $this->rollbackStack[] = [
                'method' => $method,
                'params' => $params
            ];
        }
    }
}
