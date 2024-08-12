<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Exception;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class GalleryController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
       $this->imageService = $imageService;
    }

    public function index()
    {
        $images = Image::all();
        return view('index', [
            'images' => $images
        ]);
    }

    public function upload(Request $request)
    {
        $this->validateRequest($request);

        $title = $request->only('title');
        $image = $request->file('image');

        try {

            // $url = $this->imageService->storeImageInDisk($image);
            // $databaseImage = $this->imageService->storageImageInDatabase($title['title'], $url);

            $databaseImage = $this->imageService->storeNewImage($image, $title['title']);
            throw new Exception('...');

        } catch (Exception $error) {
            // $this->imageService->deleteDatabaseImage($databaseImage);
            // $this->imageService->deleteImageFromDisk($databaseImage->url);

            $this->imageService->rollback(); 

            return redirect()->back()->withErrors([
                'error' => 'Erro ao salvar a imagem. Tente novamente.'
            ]);
        }


        return redirect()->route('index');
    }

    public function delete($id)
    {
        $image = Image::findOrFail($id);
        // dd($image->url);
        $url = parse_url($image->url);
        $path = ltrim($url['path'], '/storage\/');

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            $image->delete();
        }
        return redirect()->route('index');
    }

    private function validateRequest(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|min:6',
            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,gif',
                'max:2048', // 2MB
                Rule::dimensions()->maxWidth(2000)->maxHeight(2000)
            ]
        ]);
    }  
}
