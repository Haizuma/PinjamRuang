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
use Illuminate\Support\Str; // Tambahkan import Str

class BorrowRoomApiController extends Controller
{
    public function storeBorrowRoomWithPegawai(Request $request)
    {
        // Ambil data input
        $full_name = Str::upper($request->full_name); // Str harus di-import
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
            'borrow_at' => 'required|date|after_or_equal:' . now()->format('Y-m-d H:i'),
            'until_at' => 'required|date|after_or_equal:borrow_at',
            'room' => 'required|exists:rooms,id',
            'kepala_bidang' => 'required|exists:admin_users,id',
            'nip' => 'required|numeric',
            'unit_kerja' => 'required|string',
        ], [
            'full_name.required' => 'Kolom nama lengkap wajib diisi.',
            'borrow_at.required' => 'Kolom tgl mulai wajib diisi.',
            'borrow_at.date' => 'Kolom tgl mulai bukan tanggal yang valid.',
            'borrow_at.after_or_equal' => 'Kolom tgl mulai harus setelah atau sama dengan :date.',
            'until_at.required' => 'Kolom tgl selesai wajib diisi.',
            'until_at.date' => 'Kolom tgl selesai bukan tanggal yang valid.',
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

            // Role ID pegawai = 5 (atau sesuaikan dengan DB Anda)
            \DB::table('admin_role_users')->insert([
                'role_id' => 5, // pastikan role_id 5 adalah untuk pegawai
                'user_id' => $admin_user->id,
            ]);

            // Simpan detail user
            AdminUserDetail::create([
                'admin_user_id' => $admin_user->id,
                'data' => $data
            ]);
        }

        // Cek apakah ruangan sudah dibooking pada waktu yang sama
        $room = Room::find($request->room);
        $borrow_at = Carbon::make($request->borrow_at);
        $until_at = Carbon::make($request->until_at);
        $already_booked = false;

        foreach ($room->borrow_rooms as $borrow_room) {
            $from = Carbon::make($borrow_room->borrow_at);
            $to = Carbon::make($borrow_room->until_at);

            // Cek jika waktu baru bertabrakan dengan pinjaman lama
            if (
                $borrow_at->between($from, $to) || 
                $until_at->between($from, $to) || 
                ($borrow_at->lt($from) && $until_at->gt($to))
            ) {
                $already_booked = true;
                break;
            }
        }

        if ($already_booked) {
            return redirect(route('home'))->withInput($request->input())->withErrors([
                'Maaf, ruangan tersebut sudah dibooking pada tanggal tersebut. Silakan pilih tanggal lain.'
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
        ]);

        return redirect(route('home'))->withSuccess(true);
    }
}
