<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as InterventionImage;
use App\Models\Image;


class ImageController extends Controller
{
    const IMAGE_WIDTH = 2400;
    const IMAGE_HEIGHT = 2400;
    const THUMB_IMAGE_WIDTH = 600;
    const THUMB_IMAGE_HEIGHT = 600;
    const THUMB_PREFIX = 'thumb_';

    public function storeImage(Request $request): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {

        $request_path = $request->file('image');
        $folder = $request->input('folder');
        if ($request->hasFile('image')) {
            $image_path = $folder . '/' . Str::uuid() . '.jpg';
            $thum_path = $folder . '/' . self::THUMB_PREFIX . Str::uuid() . '.jpg';

            $image = InterventionImage::make($request_path);

            $imageResized = $image->resize(self::IMAGE_WIDTH, self::IMAGE_HEIGHT, function ($const) {
                $const->aspectRatio();
                $const->upsize();
            })
                ->orientate();

            $width = $imageResized->width();
            $height = $imageResized->height();

            Storage::makeDirectory($folder);
            Storage::put($image_path, $imageResized->stream('jpg',75)->__toString());
//
            $thumResized = $image->resize(self::THUMB_IMAGE_WIDTH, self::THUMB_IMAGE_HEIGHT, function ($const) {
                $const->aspectRatio();
                $const->upsize();
            })
                ->orientate();

            Storage::put($thum_path, $thumResized->stream('jpg',75)->__toString());

            // $image_path->move(public_path('image'),$image_path);

            // thumb , src , width , height

            $data = Image::create(['thumb' => $thum_path,'src' => $image_path , 'width' => $width, 'height' => $height]);

            return response("200", 200);
        }else{
            return response("No image in request", 500);
        }

//
//        $data = image::create([
//            'image' => $image_path,
//        ]);

//        $data = image::query()->['image' => $image_path,];
//        return JsonResource::make($data);
    }





}
