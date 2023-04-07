<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class UploadFileChunkJob implements ShouldQueue
{
    use Batchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private string $fileName, private string $content)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $stream = fopen(storage_path('app/uploads/') . $this->fileName, 'a');

        fwrite($stream, base64_decode($this->content));

        fclose($stream);
    }
}
