<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;

class ImagenControlller extends Controller
{
    public function store(Request $request)
    {
        //Leer imagenes
        $imagen = $request->file('file');
 
        $nombreImagen = Str::uuid() . "." . $imagen->extension();
 
        $manager = new ImageManager(new Driver());
        $imagenServidor = $manager::imagick()->read($imagen);
        $imagenServidor->resizeDown(1000, 1000);
 
        $imagenPath = public_path('uploads') . '/' . $nombreImagen;
        
        $imagenServidor->toPng()->save($imagenPath);
 
        return response()->json([
            'imagen' => $nombreImagen,
        ]);

    }
}
