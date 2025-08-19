<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Http\Request;

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

    public function rooms()
    {
        $data['rooms'] = Room::with('room_type')->get();

        $data['kepala_bidang'] = Administrator::whereHas('roles', function ($query) {
            $query->where('slug', 'kepala-bidang');
        })->get();

        return view('pages.rooms', compact('data'));
    }
}
