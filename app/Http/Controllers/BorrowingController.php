<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Room;
use App\Models\BorrowRoom;
use App\Models\AdminUserDetail;
use App\Models\Administrator;
use Carbon\Carbon;

class BorrowingController extends Controller
{
    /**
     * Menyimpan data pengajuan peminjaman dari form web.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // dd($request->all());
        // 1. Validasi Input Awal
        $validator = Validator::make($request->all(), [
            'full_name'     => 'required|string|max:255',
            'borrow_at'     => 'required|string',
            'until_at'      => 'required|string',
            'room'          => 'required|exists:rooms,id',
            'kepala_bidang' => 'required|exists:admin_users,id',
            'nip'           => 'required|numeric',
            'unit_kerja'    => 'required|string',
            'notes'         => 'nullable|string|max:1000',
        ], [
            'full_name.required'     => 'Kolom nama lengkap wajib diisi.',
            'borrow_at.required'     => 'Kolom tanggal & jam mulai wajib diisi.',
            'until_at.required'      => 'Kolom tanggal & jam selesai wajib diisi.',
            'room.required'          => 'Kolom ruangan wajib diisi.',
            'kepala_bidang.required' => 'Kolom kepala bidang wajib diisi.',
            'nip.required'           => 'Kolom NIP wajib diisi.',
            'nip.numeric'            => 'Kolom NIP harus berupa angka.',
            'unit_kerja.required'    => 'Kolom unit kerja wajib diisi.',
        ]);

        // Jika validasi dasar gagal, kembali dengan pesan error
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // 2. Validasi dan Konversi Format Tanggal
        try {
            // $borrow_at = Carbon::createFromFormat('m/d/Y g:i A', $request->borrow_at);
            // $until_at = Carbon::createFromFormat('m/d/Y g:i A', $request->until_at);
             $borrow_at = Carbon::createFromFormat('d-m-Y H:i', $request->borrow_at);
            $until_at = Carbon::createFromFormat('d-m-Y H:i', $request->until_at);
        } catch (\Exception $e) {
            return back()->withErrors(['tanggal' => 'Format tanggal atau waktu tidak valid. Gunakan format DD-MM-YYYY HH:mm.'])->withInput();
        }

        // Validasi tambahan untuk tanggal
        if ($borrow_at->isPast()) {
            return back()->withErrors(['borrow_at' => 'Waktu mulai tidak boleh di masa lalu.'])->withInput();
        }
        if ($until_at->lte($borrow_at)) {
            return back()->withErrors(['until_at' => 'Waktu selesai harus setelah waktu mulai.'])->withInput();
        }

        // 3. Cek atau Buat Akun Pengguna (Peminjam)
        $nip = $request->nip;
        $full_name = Str::upper($request->full_name);
        $admin_user = Administrator::where('username', $nip)->first();

        if (!$admin_user) {
            $admin_user = Administrator::create([
                'username' => $nip,
                'name'     => $full_name,
                'password' => Hash::make($nip)
            ]);

            // Asumsikan role_id 4 adalah untuk "Pegawai"
            DB::table('admin_role_users')->insert([
                'role_id' => 4,
                'user_id' => $admin_user->id,
            ]);

            $data = json_encode([
                'full_name'  => $full_name,
                'nip'        => $nip,
                'unit_kerja' => $request->unit_kerja,
            ], true);

            AdminUserDetail::create([
                'admin_user_id' => $admin_user->id,
                'data'          => $data
            ]);
        }

        // 4. Cek Jadwal Bentrok
        $isBooked = BorrowRoom::where('room_id', $request->room)
            ->where('kepala_bidang_approval_status', '!=', 2) // Abaikan yang ditolak
            ->where(function ($query) use ($borrow_at, $until_at) {
                $query->where('borrow_at', '<', $until_at)
                    ->where('until_at', '>', $borrow_at);
            })
            ->exists();

        if ($isBooked) {
            return back()->withErrors(['room' => 'Maaf, ruangan tidak tersedia pada rentang waktu yang dipilih.'])->withInput();
        }

        // 5. Cek Peminjaman Aktif
        if ($admin_user->borrow_rooms()->isNotFinished()->exists()) {
            return back()->withErrors(['nip' => 'Anda masih memiliki peminjaman yang belum selesai.'])->withInput();
        }

        // 6. Simpan Data Peminjaman
        BorrowRoom::create([
            'borrower_id'      => $admin_user->id,
            'room_id'          => $request->room,
            'borrow_at'        => $borrow_at,
            'until_at'         => $until_at,
            'kepala_bidang_id' => $request->kepala_bidang,
            'notes'            => $request->input('notes'),
            'kepala_bidang_approval_status' => \App\Enums\ApprovalStatus::Disetujui(),
            'admin_approval_status' => \App\Enums\ApprovalStatus::Disetujui(),
        ]);

        // 7. Kembali ke Halaman Daftar Ruangan dengan Pesan Sukses
        return redirect()->route('rooms')->with('success', 'Pengajuan peminjaman ruangan Anda telah berhasil dikirim!');
    }
}
