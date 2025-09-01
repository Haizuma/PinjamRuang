<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\BorrowRoom;
use App\Models\AdminUserDetail;
use App\Models\Room;
use Carbon\Carbon;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BorrowRoomApiController extends Controller
{
    public function storeBorrowRoomWithPegawai(Request $request)
    {
        // Ambil data input
        $full_name = Str::upper($request->full_name);
        $nip = $request->nip;
        $unit_kerja = $request->unit_kerja;

        // Data tambahan untuk disimpan ke tabel AdminUserDetail
        $data = json_encode([
            'full_name' => $full_name,
            'nip' => $nip,
            'unit_kerja' => $unit_kerja,
        ], true);

        // Validasi input
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string',
            'borrow_at' => 'required|string', // Diubah menjadi string untuk divalidasi manual di try-catch
            'until_at' => 'required|string|after_or_equal:borrow_at',
            'room' => 'required|exists:rooms,id',
            'kepala_bidang' => 'required|exists:admin_users,id',
            'nip' => 'required|numeric',
            'unit_kerja' => 'required|string',
            'notes' => 'nullable|string|max:500', // Validasi untuk catatan
        ], [
            'full_name.required' => 'Kolom nama lengkap wajib diisi.',
            'borrow_at.required' => 'Kolom tgl mulai wajib diisi.',
            'until_at.required' => 'Kolom tgl selesai wajib diisi.',
            'until_at.after_or_equal' => 'Kolom tgl selesai harus setelah atau sama dengan tgl mulai.',
            'room.required' => 'Kolom ruangan wajib diisi.',
            'kepala_bidang.required' => 'Kolom kepala bidang wajib diisi.',
            'nip.required' => 'Kolom NIP wajib diisi.',
            'nip.numeric' => 'Kolom NIP harus berupa angka.',
            'unit_kerja.required' => 'Kolom unit kerja wajib diisi.',
        ]);

        if ($validator->fails()) {
            return redirect(route('home'))->withInput($request->input())->withErrors($validator);
        }

        // Cek apakah pegawai (admin_user) sudah terdaftar
        $admin_user = Administrator::where('username', $nip)->first();

        if (!$admin_user) {
            $admin_user = Administrator::create([
                'username' => $nip,
                'name' => $full_name,
                'password' => Hash::make($nip)
            ]);

            // Role ID pegawai = 4 (atau sesuaikan dengan DB Anda)
            \DB::table('admin_role_users')->insert([
                'role_id' => 4, // pastikan role_id 4 adalah untuk pegawai
                'user_id' => $admin_user->id,
            ]);

            // Simpan detail user
            AdminUserDetail::create([
                'admin_user_id' => $admin_user->id,
                'data' => $data
            ]);
        }

        // Cek jadwal bentrok dengan satu query efisien
        try {
            // Pastikan format tanggal dari input sesuai (DD-MM-YYYY HH:mm)
            $borrow_at = Carbon::createFromFormat('d-m-Y H:i', $request->borrow_at);
            $until_at = Carbon::createFromFormat('d-m-Y H:i', $request->until_at);
        } catch (\Exception $e) {
            // Kembali dengan error jika format tanggal salah
            return redirect(route('home'))->withInput($request->input())->withErrors([
                'Format tanggal atau waktu tidak valid. Gunakan format DD-MM-YYYY HH:mm.'
            ]);
        }

        $isBooked = BorrowRoom::where('room_id', $request->room)
            // Abaikan booking yang ditolak (asumsi status 2 = ditolak, sesuaikan jika perlu)
            ->where('kepala_bidang_approval_status', '!=', 2)
            ->where(function ($query) use ($borrow_at, $until_at) {
                // Logika untuk memeriksa tumpang tindih waktu
                $query->where('borrow_at', '<', $until_at)
                    ->where('until_at', '>', $borrow_at);
            })
            ->exists(); // Cukup cek apakah ada satu saja yang cocok

        if ($isBooked) {
            return redirect(route('home'))->withInput($request->input())->withErrors([
                'Maaf, ruangan tersebut tidak tersedia pada rentang waktu yang dipilih. Silakan pilih waktu lain.'
            ]);
        }


        // Cek apakah pegawai masih punya pinjaman aktif
        $borrow_rooms = BorrowRoom::where('borrower_id', $admin_user->id);

        if ($borrow_rooms->exists() && $borrow_rooms->isNotFinished()->exists()) {
            return redirect(route('home'))->withInput($request->input())->withErrors([
                'Pegawai dengan NIP ' . $admin_user->username . ' masih memiliki peminjaman yang belum selesai.',
                'login_for_more_info'
            ]);
        }

        // Simpan peminjaman ruangan
        BorrowRoom::create([
            'borrower_id' => $admin_user->id,
            'room_id' => $request->room,
            'borrow_at' => $borrow_at,
            'until_at' => $until_at,
            'kepala_bidang_id' => $request->kepala_bidang,
            'notes' => $request->input('notes'), // Menyimpan data catatan
        ]);

        return redirect(route('home'))->withSuccess(true);
    }
}