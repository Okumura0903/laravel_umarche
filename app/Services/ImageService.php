<?php
namespace App\Services;
use Illuminate\Support\Facades\Storage;
use InterventionImage;

class ImageService{
    public static function upload($imageFile,$folderName){
        //            Storage::putFile('public/shops',$imageFile);//リサイズなしの場合
        $fileName=uniqid(rand().'_');
        $extension=$imageFile->extension();
        $fileNameToStore=$fileName.'.'.$extension;
        $resizedImage=InterventionImage::make($imageFile)->resize(1920,1080)->encode();
        Storage::put('public/'.$folderName.'/'.$fileNameToStore,$resizedImage);

        return $fileNameToStore;
    }

}