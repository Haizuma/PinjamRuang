<?php

namespace App\Admin\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Http\Request;
use Encore\Admin\Facades\Admin;

class AdministratorApiController extends Controller
{
    /**
     * Get all administrator where has role `pegawai`
     *
     * @param  mixed $request
     * @return void
     */
    public function getPegawai(Request $request)
    {
        $q = $request->get('q');

        return Administrator::whereHas('roles', function ($query) {
            $query->where('slug', 'pegawai');
        })->where('name', 'like', "%$q%")->paginate(null, ['id', 'name as text']);
    }

    /**
     * Get all administrator where has role `kepala-bidang`
     *
     * @param  mixed $request
     * @return void
     */
    public function getKepalaBidang(Request $request)
    {
        $q = $request->get('q');

        return Administrator::whereHas('roles', function ($query) {
            $query->where('slug', 'kepala-bidang');
        })->where('name', 'like', "%$q%")->paginate(null, ['id', 'name as text']);
    }

    /**
     * Get all administrator where has role `tata-usaha`
     *
     * @param  mixed $request
     * @return void
     */
    public function getTataUsaha(Request $request)
    {
        $q = $request->get('q');

        return Administrator::whereHas('roles', function ($query) {
            $query->where('slug', 'tata-usaha');
        })->where('name', 'like', "%$q%")->paginate(null, ['id', 'name as text']);
    }
}
