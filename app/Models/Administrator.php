<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Administrator as BaseAdministrator;

class Administrator extends BaseAdministrator
{
    /**
     * Mendapatkan detail user yang terkait.
     */
    public function detail()
    {
        return $this->hasOne(AdminUserDetail::class, 'admin_user_id');
    }

    /**
     * TAMBAHKAN INI: Mendapatkan semua data peminjaman milik user ini.
     */
    public function borrow_rooms()
    {
        // User ini 'memiliki banyak' (hasMany) data peminjaman.
        // Relasi ini terhubung melalui kolom 'borrower_id' di tabel 'borrow_rooms'.
        return $this->hasMany(BorrowRoom::class, 'borrower_id');
    }
}
