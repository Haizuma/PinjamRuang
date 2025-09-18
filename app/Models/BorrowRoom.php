<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BorrowRoom extends Model
{
    use SoftDeletes;

    protected $table = 'borrow_rooms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'borrower_id',
        'room_id',
        'borrow_at',
        'until_at',
        'kepala_bidang_id',
        'kepala_bidang_approval_status',
        'admin_id',
        'admin_approval_status',
        'processed_at',
        'returned_at',
        'notes',
    ];

    /**
     * Relationship
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function borrower()
    {
        return $this->belongsTo(Administrator::class);
    }

    public function kepala_bidang()
    {
        return $this->belongsTo(Administrator::class, 'kepala_bidang_id');
    }

    public function admin()
    {
        return $this->belongsTo(Administrator::class);
    }
    
}


    /**
     * Scopes
     */
    public function scopeIsNotFinished($query)
    {
        return $query
            ->where('kepala_bidang_approval_status', '!=', ApprovalStatus::Ditolak())
            ->whereNull('returned_at');
    }

    public function scopeIsKepalaBidangApproved($query)
    {
        return $query->where('kepala_bidang_approval_status', '=', ApprovalStatus::Disetujui());
    }
}
