<?php

namespace App\Http\Controllers;

use App\Jobs\UploadFileChunkJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    const CHUNK_SIZE = 1024*1024;

    public function index()
    {
        return view('upload.index');
    }

    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $chunks = ceil($file->getSize() / self::CHUNK_SIZE);
            $batches = [];

            for ($i = 0; $i < $chunks; $i++) {
                $batches[] = new UploadFileChunkJob(
                    $file->getClientOriginalName(),
                    base64_encode(file_get_contents($file->path(), false, null, $i * self::CHUNK_SIZE, self::CHUNK_SIZE))
                );
            }

            Bus::batch($batches)
                ->then(fn () => info('File Uploaded Successfully'))
                ->catch(fn () => Log::error('Unexpected Error'))
                ->dispatch();

            return back()->with('success', 'File Is Being Uploaded In Background');
        }
    }
}
