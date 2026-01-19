<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadController
{
    public function downloadTemplate(Request $request): BinaryFileResponse
    {
        $file = $request->query('file');

        // Security: prevent path traversal
        if (!$file || str_contains($file, '..')) {
            abort(404, 'Invalid file path');
        }

        $fullPath = storage_path('app/' . $file);

        // Check if file exists
        if (!file_exists($fullPath)) {
            abort(404, 'Template file not found: ' . $file);
        }

        $filename = basename($file);

        return response()->download($fullPath, $filename)->deleteFileAfterSend(true);
    }

    public function downloadExport(Request $request): BinaryFileResponse
    {
        $file = $request->query('file');

        // Security: prevent path traversal
        if (!$file || str_contains($file, '..')) {
            abort(404, 'Invalid file path');
        }

        $fullPath = storage_path('app/' . $file);

        // Check if file exists
        if (!file_exists($fullPath)) {
            abort(404, 'Export file not found: ' . $file);
        }

        $filename = basename($file);

        return response()->download($fullPath, $filename)->deleteFileAfterSend(true);
    }
}
