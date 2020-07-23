<?php

namespace Halimtuhu\ArrayFiles;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function upload(Request $request)
    {
        $disk = $request->get('disk', 'public');
        $path = $request->get('path', '/');

        $file = Storage::disk($disk)->putFile($path, $request->file('file'));

        $data = [
            'originalName' => $request->file('file')->getClientOriginalName(),
            'name' => $file,
            'url' => Storage::disk($disk)->url($file),
        ];

        return response()
            ->json($data);
    }

    public function delete(Request $request)
    {
        $file = $request->get('file');
        $disk = $request->get('disk', 'public');
        $status = true;

        if (Storage::disk($disk)->has($file)) {
            $status = Storage::disk($disk)->delete($file);
        }

        return response()
            ->json([
                'status' => $status,
            ]);
    }
}
