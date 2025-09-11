<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $tables = 'rooms';

    /**
     * Relationship
     */
    public function room_type()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function borrow_rooms()
    {
        return $this->hasMany(BorrowRoom::class);
    }
    public function isCurrentlyBooked()
    {
        return $this->borrow_rooms()
            ->whereNull('returned_at') // Belum dikembalikan
            ->where('admin_approval_status', \App\Enums\ApprovalStatus::Disetujui) // Sudah disetujui Admin
            ->where('borrow_at', '<=', now()) // Waktu mulai sudah lewat
            ->where('until_at', '>=', now()) // Waktu selesai belum lewat
            ->exists(); // Cukup cek apakah ada satu saja yang cocok, akan mengembalikan true atau false
    }
}
