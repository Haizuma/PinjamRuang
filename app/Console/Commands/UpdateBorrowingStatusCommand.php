<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BorrowRoom;
use Carbon\Carbon;

class UpdateBorrowingStatusCommand extends Command
{
    /**
     * Nama dan signature dari console command.
     *
     * @var string
     */
    protected $signature = 'borrowings:update-status';

    /**
     * Deskripsi dari console command.
     *
     * @var string
     */
    protected $description = 'Mencari peminjaman yang sudah lewat waktunya dan menandainya sebagai selesai';

    /**
     * Jalankan console command.
     */
    public function handle()
    {
        $this->info('Memeriksa status peminjaman...');

        // 1. Cari semua peminjaman yang sudah disetujui, belum selesai,
        //    dan waktu selesainya sudah lewat.
        // Ambil nilai 'Disetujui' dari Enum terlebih dahulu
        $approvedStatus = \App\Enums\ApprovalStatus::Disetujui();

        $completedBookings = BorrowRoom::where('admin_approval_status', $approvedStatus)
            ->whereNull('returned_at')
            ->where('until_at', '<', Carbon::now())
            ->get();

        if ($completedBookings->isEmpty()) {
            $this->info('Tidak ada peminjaman yang perlu diperbarui.');
            return 0;
        }

        // 2. Perbarui statusnya satu per satu
        foreach ($completedBookings as $booking) {
            $booking->update(['returned_at' => Carbon::now()]);
            $this->info("Peminjaman ID: {$booking->id} untuk ruangan {$booking->room->name} telah ditandai selesai.");
        }

        $this->info('Pembaruan status peminjaman selesai.');
        return 0;
    }
}
