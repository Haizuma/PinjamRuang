<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class RunScheduledTasks
{
    public function handle(Request $request, Closure $next)
    {
        // Ambil waktu terakhir tugas dijalankan dari cache
        // Jika tidak ada, anggap saja sudah lama sekali (nilai 0)
        $lastRun = Cache::get('last_schedule_run', 0);

        // Cek apakah sudah lebih dari 5 menit (300 detik) sejak dijalankan terakhir
        if (Carbon::now()->timestamp > $lastRun + (1 * 60)) {
            // Jalankan command Artisan Anda
            Artisan::call('borrowings:update-status');

            // Catat waktu sekarang sebagai waktu terakhir dijalankan di cache
            Cache::put('last_schedule_run', Carbon::now()->timestamp);
        }

        return $next($request);
    }
}
