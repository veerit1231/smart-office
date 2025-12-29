<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BackupController extends Controller
{
    /**
     * Show backup page
     */
    public function index()
    {
        $files = Storage::disk('local')->files('laravel-backup');

        $latest = collect($files)
            ->filter(fn ($f) => str_ends_with($f, '.zip'))
            ->sortDesc()
            ->first();

        return Inertia::render('Admin/Backup/Index', [
            'latestBackup' => $latest ? [
                'name'       => basename($latest),
                'size'       => round(Storage::size($latest) / 1024 / 1024, 2),
                'created_at' => date('Y-m-d H:i:s', Storage::lastModified($latest)),
            ] : null,
        ]);
    }

    /**
     * Run backup
     */
    public function run()
{
    Artisan::call('backup:run');

    return redirect()
        ->route('admin.backup.index')
        ->with('success', 'Backup completed')
        ->setStatusCode(303); // ⭐ สำคัญมาก
}

    /**
     * Download latest backup
     */
    public function download()
    {
        $files = Storage::disk('local')->files('laravel-backup');

        $latest = collect($files)
            ->filter(fn ($f) => str_ends_with($f, '.zip'))
            ->sortDesc()
            ->first();

        abort_unless($latest, 404);

        return Storage::download($latest);
    }
}
