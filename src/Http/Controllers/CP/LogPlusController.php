<?php

namespace AniketMagadum\LogPlus\Http\Controllers\CP;

use Illuminate\Http\Request;
use Route;
use Statamic\Http\Controllers\CP\CpController;
use Rap2hpoutre\LaravelLogViewer\LaravelLogViewer;

class LogPlusController extends CpController
{
    public function index(Request $request, LaravelLogViewer $laravelLogViewer)
    {
        $log_files = $laravelLogViewer->getFiles();

        $files = [];

        foreach ($log_files as $file) {
            $key = last(explode('/', $file));

            $size_in_bytes = filesize($file);

            $size_in_mb = $size_in_bytes / (1024 * 1024);

            if ($size_in_mb < 1) {
                $displayable_size = round($size_in_mb * 1024, 1) . " KB";
            } else if ($size_in_mb < 1024) {
                $displayable_size = round($size_in_mb, 1) . " MB";
            } else {
                $displayable_size = round($size_in_mb / 1024, 1) . " GB";
            }

            $files[$key] = [
                "path" => $file,
                "size" => $size_in_bytes,
                "displayable_size" => $displayable_size
            ];
        }

        $file_to_fetch = $request->file ?? array_key_first($files);

        if (empty($files)) {
            $unique_logs = collect();
        } else if (isset($files[$file_to_fetch])) {
            $currentLogFile = $files[$file_to_fetch];

            $laravelLogViewer->setFile($currentLogFile['path']);

            $unique_logs = collect($laravelLogViewer->all())->map(function ($log) {
                $log['hash'] = md5($log['text']);
                return $log;
            })->groupBy('hash')->sortByDesc(function ($group) {
                return $group->count();
            });
        } else {
            $currentRoute = Route::currentRouteName();
            return redirect()->route($currentRoute);
        }

        $color_mappings = [
            'emergency' => '#FF0000',
            // Red
            'alert' => '#FF4500',
            // Orange
            'critical' => '#FFA500',
            // Dark Orange
            'error' => '#FF6347',
            // Tomato
            'warning' => '#FFD700',
            // Gold
            'notice' => '#00BFFF',
            // Deep Sky Blue
            'info' => '#32CD32',
            // Lime Green
            'debug' => '#808080' // Gray
        ];

        return view("log-plus::index", [
            'files' => $files,
            'unique_logs' => $unique_logs ?? collect(),
            "max_file_size" => LaravelLogViewer::MAX_FILE_SIZE,
            "color_mappings" => $color_mappings
        ]);
    }

    public function delete($file, LaravelLogViewer $laravelLogViewer)
    {
        foreach ($laravelLogViewer->getFiles() as $log_file) {
            if (last(explode('/', $log_file)) == $file) {
                unlink($log_file);
            }
        }

        session()->flash('success', __('Deleted log file - ' . $file));

        return redirect()->route('statamic.cp.utilities.log-plus');
    }
}