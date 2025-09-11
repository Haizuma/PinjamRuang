<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $data['rooms'] = Room::with('room_type')->get();

        // Ambil admin yang memiliki role kepala bidang
        $data['kepala_bidang'] = Administrator::whereHas('roles', function ($query) {
            $query->where('slug', 'kepala-bidang');
        })->get()->pluck('name', 'id');

        return view('index', compact('data'));
    }

    public function rooms(Request $request)
    {
        // 1. Ambil SEMUA ruangan untuk dropdown filter
        $data['all_rooms'] = Room::orderBy('name', 'asc')->get();

        // 2. Siapkan query untuk ruangan yang akan DITAMPILKAN
        $roomsQuery = Room::query();

        // Terapkan filter berdasarkan ruangan yang dipilih
        if ($request->filled('room_id')) {
            $roomsQuery->where('id', $request->room_id);
        }

        // 3. Siapkan filter tanggal untuk relasi 'borrow_rooms'
        $selectedDate = null;
        if ($request->filled('selected_date')) {
            try {
                // Konversi format tanggal dari DD-MM-YYYY ke YYYY-MM-DD
                $selectedDate = Carbon::createFromFormat('d-m-Y', $request->selected_date)->toDateString();
            } catch (\Exception $e) {
                // Abaikan jika format salah
                $selectedDate = null;
            }
        }

        // 4. Ambil data ruangan DAN filter jadwalnya sekaligus (Eager Loading with Constraints)
        $roomsQuery->with([
            'borrow_rooms' => function ($query) use ($selectedDate) {
                $query->whereNull('returned_at')
                    ->where('kepala_bidang_approval_status', '!=', 2);

                // Terapkan filter tanggal HANYA jika tanggal dipilih
                if ($selectedDate) {
                    $query->whereDate('borrow_at', '=', $selectedDate);
                }

                $query->orderBy('borrow_at', 'asc');
            }
        ]);

        // Eksekusi query utama
        $data['rooms'] = $roomsQuery->get();

        // Ambil data kepala bidang
        $data['kepala_bidang'] = Administrator::whereHas('roles', function ($query) {
            $query->where('slug', 'kepala-bidang');
        })->get()->pluck('name', 'id');

        return view('pages.rooms', compact('data'));
    }
}
