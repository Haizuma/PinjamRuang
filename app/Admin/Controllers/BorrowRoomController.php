<?php

namespace App\Admin\Controllers;

use App\Enums\ApprovalStatus;
use App\Models\BorrowRoom;
use App\Http\Controllers\Controller;
use App\Models\Room;
use Carbon\Carbon;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Form\Field;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\Models\AdminUserDetail;

class BorrowRoomController extends Controller
{
    use HasResourceActions;

    public function index(Content $content)
    {
        return $content
            ->header('Pinjam Ruang')
            ->description(trans('admin.list'))
            ->body($this->grid());
    }

    public function show($id, Content $content)
    {
        return $content
            ->header('Pinjam Ruang')
            ->description(trans('admin.show'))
            ->body($this->detail($id));
    }

    public function edit($id, Content $content)
    {
        return $content
            ->header('Pinjam Ruang')
            ->description(trans('admin.edit'))
            ->body($this->form()->edit($id));
    }

    public function create(Content $content)
    {
        return $content
            ->header('Pinjam Ruang')
            ->description(trans('admin.create'))
            ->body($this->form());
    }

    protected function grid()
    {
        $grid = new Grid(new BorrowRoom);

        $admin_user = \Admin::user();

        if ($admin_user->isRole('kepala-bidang'))
            $grid->model()->where('kepala_bidang_id', $admin_user->id);
        else if ($admin_user->isRole('pegawai'))
            $grid->model()->where('borrower_id', $admin_user->id);
        else if ($admin_user->isRole('tata-usaha'))
            $grid->model()->whereIn('kepala_bidang_approval_status', [ApprovalStatus::Disetujui(), ApprovalStatus::Ditolak()]);

        $grid->id('ID');
        $grid->column('borrower.name', 'Peminjam');
        $grid->column('unit_kerja', 'Unit Kerja')->display(function () {
            // Cari detail user secara manual berdasarkan borrower_id
            $detail = AdminUserDetail::where('admin_user_id', $this->borrower_id)->first();

            if ($detail) {
                // Decode kolom JSON 
                $data = json_decode($detail->data, true);

                // Ambil nilai 'unit_kerja'
                $unitKerja = $data['unit_kerja'] ?? 'N/A';

                // Rapikan teksnya
                return ucwords(str_replace('-', ' ', $unitKerja));
            }

            return 'N/A';
        });
        $grid->column('room.name', 'Ruangan');
        $grid->column('borrow_at', 'Mulai Pinjam')->display(function ($borrow_at) {
            return Carbon::parse($borrow_at)->format('d M Y H:i');
        });
        $grid->column('until_at', 'Lama Pinjam')->display(function ($title) {
            $borrow_at = Carbon::parse($this->borrow_at);
            $until_at = Carbon::parse($title);
            return $until_at->diffForHumans($borrow_at);
        });
        $grid->column('kepala_bidang.name', 'Kepala Bidang');
        $grid->column('status', 'Status')->display(function () {
            $kepala_bidang_approval_status = $this->kepala_bidang_approval_status;
            $admin_approval_status = $this->admin_approval_status;
            $returned_at = $this->returned_at ?? null;
            $processed_at = $this->processed_at ?? null;

            if ($kepala_bidang_approval_status == 1) {
                if ($admin_approval_status == 1) {
                    if ($returned_at != null)
                        $val = ['success', 'Peminjaman selesai'];
                    else if ($processed_at != null)
                        $val = ['success', 'Ruangan sedang digunakan'];
                    else
                        $val = ['success', 'Sudah disetujui TU'];
                } else if ($admin_approval_status == 0)
                    $val = ['info', 'Menunggu persetujuan TU'];
                else
                    $val = ['danger', 'Ditolak TU'];
            } else if ($kepala_bidang_approval_status == 0) {
                $val = ['info', 'Menunggu persetujuan Kepala Bidang'];
            } else {
                $val = ['danger', 'Ditolak Kepala Bidang'];
            }

            return '<span class="label-' . $val[0] . '" style="width: 8px;height: 8px;padding: 0;border-radius: 50%;display: inline-block;"></span>&nbsp;&nbsp;'
                . $val[1];
        });

        if (!\Admin::user()->can('create.borrow_rooms'))
            $grid->disableCreateButton();

        $grid->actions(function ($actions) {
            if (!\Admin::user()->can('edit.borrow_rooms')) {
                $actions->disableEdit();
            }
            if (!\Admin::user()->can('list.borrow_rooms')) {
                $actions->disableView();
            }
            if (!\Admin::user()->can('delete.borrow_rooms')) {
                $actions->disableDelete();
            }
        });

        return $grid;
    }

    protected function detail($id)
    {
        $show = new Show(BorrowRoom::findOrFail($id));

        $show->id('ID');
        $show->field('borrower.name', 'Peminjam');
        $show->field('unit_kerja', 'Unit Kerja')->as(function ($borrowerId) {
            // Cari detail user secara manual berdasarkan borrower_id
            $detail = AdminUserDetail::where('admin_user_id', $borrowerId)->first();

            if ($detail) {
                // Decode kolom JSON
                $data = json_decode($detail->data, true);

                // Ambil nilai 'unit_kerja'
                $unitKerja = $data['unit_kerja'] ?? 'N/A';

                // Rapikan teksnya
                return ucwords(str_replace('-', ' ', $unitKerja));
            }

            return 'N/A';
        });
        $show->field('room.name', 'Ruangan');
        $show->field('borrow_at', 'Mulai Pinjam');
        $show->field('until_at', 'Selesai Pinjam');
        $show->field('kepala_bidang.name', 'Kepala Bidang');
        $show->field('kepala_bidang_approval_status', 'Status Persetujuan Kepala Bidang')->using(ApprovalStatus::asSelectArray());
        $show->field('admin.name', 'Tata Usaha');
        $show->field('admin_approval_status', 'Status Persetujuan Tata Usaha')->using(ApprovalStatus::asSelectArray());
        $show->field('processed_at', 'Kunci Diambil Pada');
        $show->field('returned_at', 'Diselesaikan Pada');
        $show->field('notes', 'Catatan');
        $show->created_at(trans('admin.created_at'));
        $show->updated_at(trans('admin.updated_at'));

        $show->panel()->tools(function ($tools) {
            if (!\Admin::user()->can('edit.borrow_rooms'))
                $tools->disableEdit();
            if (!\Admin::user()->can('list.borrow_rooms'))
                $tools->disableList();
            if (!\Admin::user()->can('delete.borrow_rooms'))
                $tools->disableDelete();
        });

        return $show;
    }

    protected function form()
    {
        $form = new Form(new BorrowRoom);
        $admin_user = \Admin::user();
        $isKepalaBidang = $admin_user->isRole('kepala-bidang');
        $isTatausaha = $admin_user->isRole('tata-usaha');

        if ($form->isEditing())
            $form->display('id', 'ID');

        if ($isKepalaBidang || $isTatausaha) {
            $form->display('borrower.name', 'Peminjam');
            $form->display('room.name', 'Ruangan');
            $form->display('borrow_at', 'Lama Pinjam')->with(function () {
                $borrow_at = Carbon::parse($this->borrow_at);
                $until_at = Carbon::parse($this->until_at);
                $count_days = $borrow_at->diffInDays($until_at) + 1;

                if ($count_days == 1)
                    return $count_days . ' hari (' . $until_at->format('d M Y') . ')';
                else
                    return $count_days . ' hari (' . $borrow_at->format('d M Y') . ' s/d ' . $until_at->format('d M Y') . ')';
            });
        } else {
            $form->select('borrower_id', 'Peminjam')->options(function ($id) {
                $pegawai = Administrator::find($id);
                if ($pegawai)
                    return [$pegawai->id => $pegawai->name];
            })->ajax('/admin/api/pegawai');

            $form->select('room_id', 'Ruangan')->options(function ($id) {
                $room = Room::find($id);
                if ($room)
                    return [$room->id => $room->name];
            })->ajax('/admin/api/rooms');

            $form->datetime('borrow_at', 'Mulai Pinjam')->format('YYYY-MM-DD HH:mm');
            $form->datetime('until_at', 'Selesai Pinjam')->format('YYYY-MM-DD HH:mm');
        }

        if ($isKepalaBidang) {
            $form->display('created_at', 'Diajukan pada')->with(function () {
                return Carbon::parse($this->created_at)->format('d M Y');
            });
            $form->radio('kepala_bidang_approval_status', 'Status Persetujuan Kepala Bidang')->options(ApprovalStatus::asSelectArray());
        } else if ($isTatausaha) {
            $form->display('created_at', 'Diajukan pada')->with(function () {
                return Carbon::parse($this->created_at)->format('d M Y');
            });
            $form->display('kepala_bidang.name', 'Kepala Bidang');
            $form->display('kepala_bidang_approval_status', 'Status Persetujuan Kepala Bidang')->with(function () {
                return ApprovalStatus::getDescription($this->kepala_bidang_approval_status);
            });

            $form->hidden('admin_id');
            $form->radio('admin_approval_status', 'Status Persetujuan Tata Usaha')
                ->options(ApprovalStatus::asSelectArray())
                ->with(function ($value, Field $thisField) {
                    if (
                        $this->kepala_bidang_approval_status === ApprovalStatus::Pending
                        || $this->kepala_bidang_approval_status === ApprovalStatus::Ditolak
                    )
                        $thisField->attribute('disabled', true);
                    return $value;
                });

            $form->datetime('processed_at', 'Kunci Diambil Pada')->format('YYYY-MM-DD HH:mm')->with(function ($value, Field $thisField) {
                if (
                    $this->admin_approval_status === null
                    || $this->admin_approval_status === ApprovalStatus::Pending
                    || $this->admin_approval_status === ApprovalStatus::Ditolak
                )
                    $thisField->attribute('readonly', 'readonly');
            });

            $form->datetime('returned_at', 'Diselesaikan Pada')->format('YYYY-MM-DD HH:mm')->with(function ($value, Field $thisField) {
                if ($this->processed_at == null)
                    $thisField->attribute('readonly', 'readonly');
            });

            $form->textarea('notes', 'Catatan');
        } else {
            $form->select('kepala_bidang_id', 'Kepala Bidang')->options(function ($id) {
                $kepala = Administrator::find($id);
                if ($kepala)
                    return [$kepala->id => $kepala->name];
            })->ajax('/admin/api/kepala-bidang');

            $form->radio('kepala_bidang_approval_status', 'Status Persetujuan Kepala Bidang')->options(ApprovalStatus::asSelectArray());

            $form->select('admin_id', 'Tata Usaha')->options(function ($id) {
                $tu = Administrator::find($id);
                if ($tu)
                    return [$tu->id => $tu->name];
            })->ajax('/admin/api/administrators');

            $form->radio('admin_approval_status', 'Status Persetujuan Tata Usaha')->options(ApprovalStatus::asSelectArray());
            $form->datetime('processed_at', 'Kunci Diambil Pada')->format('YYYY-MM-DD HH:mm');
            $form->datetime('returned_at', 'Diselesaikan Pada')->format('YYYY-MM-DD HH:mm');
            $form->textarea('notes', 'Catatan');

            if ($form->isEditing()) {
                $form->display('created_at', trans('admin.created_at'));
                $form->display('updated_at', trans('admin.updated_at'));
            }
        }

        $form->saving(function (Form $form) {
            $form->admin_id = \Admin::user()->id;
        });

        return $form;
    }
}
