<?php

namespace App\Admin\Controllers;

use App\Enums\RoomStatus;
use App\Models\Room;
use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class RoomController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Ruangan')
            ->description(trans('admin.list'))
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Ruangan')
            ->description(trans('admin.show'))
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Ruangan')
            ->description(trans('admin.edit'))
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Ruangan')
            ->description(trans('admin.create'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    // protected function grid()
    // {
    //     $grid = new Grid(new Room);

    //     $grid->id('ID');

    //     $grid->column('name', 'Nama');
    //     $grid->column('room_type.name', 'Tipe Ruangan');
    //     $grid->column('max_people', 'Maks Orang');
    //     $grid->column('room_status', 'Status Ruangan')->display(function ($value) {
    //         $val = ['info', 'Kosong'];
    //         foreach ($this->borrow_rooms as $borrow_room) {
    //             $lecturer_approval_status = $borrow_room->lecturer_approval_status;
    //             $admin_approval_status = $borrow_room->admin_approval_status;
    //             $returned_at = $borrow_room->returned_at ?? null;
    //             $processed_at = $borrow_room->processed_at ?? null;

    //             if ($lecturer_approval_status == 1) {
    //                 // $Val = ['success', 'Sudah disetujui Kepala Bidang'];
    //                 if ($admin_approval_status == 1) {
    //                     if ($returned_at != null)
    //                         $val = ['success', 'Peminjaman selesai'];
    //                     else if ($processed_at != null)
    //                         $val = ['success', 'Ruangan sedang digunakan'];
    //                     else
    //                         $val = ['success', 'Sudah disetujui TU'];
    //                 } else if ($admin_approval_status == 0)
    //                     $val = ['info', 'Menunggu persetujuan TU'];
    //             } else if ($lecturer_approval_status == 0) {
    //                 $val = ['info', 'Menunggu persetujuan Kepala Bidang'];
    //             }
    //         }
    //         // return 'wkwk';
    //         return '<span class="label-' . $val[0] . '" style="width: 8px;height: 8px;padding: 0;border-radius: 50%;display: inline-block;"></span>&nbsp;&nbsp;'
    //             . $val[1];
    //     });

    //     // Role & Permission
    //     if (!\Admin::user()->can('create.rooms'))
    //         $grid->disableCreateButton();

    //     $grid->actions(function ($actions) {

    //         // The roles with this permission will not able to see the view button in actions column.
    //         if (!\Admin::user()->can('edit.rooms')) {
    //             $actions->disableEdit();
    //         }
    //         // The roles with this permission will not able to see the show button in actions column.
    //         if (!\Admin::user()->can('list.rooms')) {
    //             $actions->disableView();
    //         }
    //         // The roles with this permission will not able to see the delete button in actions column.
    //         if (!\Admin::user()->can('delete.rooms')) {
    //             $actions->disableDelete();
    //         }
    //     });

    //     return $grid;
    // }
    protected function grid()
    {
        $grid = new Grid(new Room);

        $grid->id('ID');

        $grid->column('name', 'Nama');
        $grid->column('room_type.name', 'Tipe Ruangan');
        $grid->column('max_people', 'Maks Orang');

        // KOLOM YANG DIPERBAIKI
        $grid->column('room_status', 'Status Ruangan')->display(function () {

            // 1. Ambil HANYA SATU peminjaman yang belum selesai (aktif atau menunggu persetujuan)
            // Kita menggunakan scope 'isNotFinished' yang sudah Anda buat.
            $active_borrow = $this->borrow_rooms()->isNotFinished()->first();

            // 2. Set status default menjadi 'Kosong'
            $val = ['success', 'Kosong'];

            // 3. Jika ada peminjaman yang aktif/menunggu, baru kita cek statusnya
            if ($active_borrow) {
                $kepala_bidang_approval = $active_borrow->kepala_bidang_approval_status;
                $admin_approval = $active_borrow->admin_approval_status;
                $processed_at = $active_borrow->processed_at;

                // Logika status yang sama seperti sebelumnya, tapi tanpa loop
                if ($kepala_bidang_approval == 0) { // Belum disetujui Kepala Bidang
                    $val = ['warning', 'Menunggu persetujuan Kepala Bidang'];
                } elseif ($kepala_bidang_approval == 1 && $admin_approval == 0) { // Disetujui Kepala Bidang, Menunggu TU
                    $val = ['info', 'Menunggu persetujuan TU'];
                } elseif ($kepala_bidang_approval == 1 && $admin_approval == 1) { // Sudah disetujui semua
                    if ($processed_at != null) {
                        $val = ['danger', 'Ruangan sedang digunakan'];
                    } else {
                        $val = ['primary', 'Sudah disetujui TU'];
                    }
                }
            }

            // 4. Tampilkan hasilnya
            return '<span class="label label-' . $val[0] . '">' . $val[1] . '</span>';
        });

        // ... sisa kode Anda (Role & Permission, actions) tetap sama ...

        if (!\Admin::user()->can('create.rooms'))
            $grid->disableCreateButton();

        $grid->actions(function ($actions) {
            if (!\Admin::user()->can('edit.rooms')) {
                $actions->disableEdit();
            }
            if (!\Admin::user()->can('list.rooms')) {
                $actions->disableView();
            }
            if (!\Admin::user()->can('delete.rooms')) {
                $actions->disableDelete();
            }
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Room::findOrFail($id));

        $show->id('ID');
        $show->field('name', 'Nama');
        $show->field('room_type.name', 'Tipe Ruangan');
        $show->field('max_people', 'Maks Orang');
        $show->field('status', 'Status')->using(RoomStatus::asSelectArray());
        $show->field('notes', 'Catatan');
        $show->created_at(trans('admin.created_at'));
        $show->updated_at(trans('admin.updated_at'));

        // Role & Permission
        $show->panel()
            ->tools(function ($tools) {
                // The roles with this permission will not able to see the view button in actions column.
                if (!\Admin::user()->can('edit.rooms'))
                    $tools->disableEdit();

                // The roles with this permission will not able to see the show button in actions column.
                if (!\Admin::user()->can('list.rooms'))
                    $tools->disableList();

                // The roles with this permission will not able to see the delete button in actions column.
                if (!\Admin::user()->can('delete.rooms'))
                    $tools->disableDelete();
            });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Room);

        if ($form->isEditing())
            $form->display('id', 'ID');

        $form->text('name', 'Nama');
        $form->select('room_type_id', 'Tipe Ruangan')->options(function ($id) {
            return RoomType::all()->pluck('name', 'id');
        });
        $form->text('max_people', 'Maksimal Orang');
        // $form->slider('max_people', 'Maksimal Orang')->options([
        //     'min' => 1,
        //     'max' => 200,
        //     'from' => 1,
        //     'postfix' => ' orang'
        // ]);
        $form->radio('status', 'Status')->options(RoomStatus::asSelectArray())->stacked();
        $form->textarea('notes', 'Catatan');

        if ($form->isEditing()) {
            $form->display('created_at', trans('admin.created_at'));
            $form->display('updated_at', trans('admin.updated_at'));
        }

        return $form;
    }
}
