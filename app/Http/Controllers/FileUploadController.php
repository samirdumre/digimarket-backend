<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FileUploadController extends Controller
{
    public function uploadFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:102400', // 100MB max
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $file = $request->file('file');

        // Store the file in storage/app/public/uploads
        $path = $file->store('uploads', 'public');

        // Get the full URL of the file
        $url = asset('storage/' . $path);

        return response()->json(['url' => $url]);
    }
}
