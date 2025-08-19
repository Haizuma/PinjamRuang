@extends('layouts.default')
@section('content')

<section class="hero-wrap hero-wrap-2" style="background-image: url('vendor/technext/vacation-rental/images/bg_1.jpg');" data-stellar-background-ratio="0.5">
    <div class="overlay"></div>
    <div class="container">
      <div class="row no-gutters slider-text align-items-center justify-content-center">
        <div class="col-md-9 ftco-animate text-center">
            <p class="breadcrumbs mb-2"><span class="mr-2"><a href="{{ route('home') }}">Beranda <i class="fa fa-chevron-right"></i></a></span> <span>Ruangan <i class="fa fa-chevron-right"></i></span></p>
          <h1 class="mb-0 bread">Daftar Ruangan</h1>
        </div>
      </div>
    </div>
</section>

<section class="ftco-section bg-light ftco-no-pt ftco-no-pb">
    <div class="container-fluid px-md-0">
        <div class="row no-gutters">
        @foreach ($data['rooms'] as $room)
            @php
                $room_status = $room->status;
                $borrower_status = [];

                if ($room->borrow_rooms->isNotEmpty()) {
                    foreach ($room->borrow_rooms as $borrow_room) {
                        if (
                            $borrow_room->returned_at == null &&
                            $borrow_room->admin_approval_status == App\Enums\ApprovalStatus::Disetujui
                        ) {
                            $room_status = 1;
                            $borrower_first_name = ucfirst(strtolower(explode(' ', Encore\Admin\Auth\Database\Administrator::find($borrow_room->borrower_id)->name)[0]));
                            $borrow_at = Carbon\Carbon::parse($borrow_room->borrow_at);
                            $until_at = Carbon\Carbon::parse($borrow_room->until_at);
                            $count_days = $borrow_at->diffInDays($until_at) + 1;

                            $borrower_status[] = $borrower_first_name . ' - ' . $borrow_at->format('d M Y') . ($count_days > 1 ? ' s.d ' . $until_at->format('d M Y') : '');
                        }
                    }
                }
            @endphp
            <div class="col-lg-6">
                <div class="room-wrap d-md-flex">
                    <a href="#" class="img" style="background-image: url({{ asset('vendor/technext/vacation-rental/images/room-'. rand(1, 6) . '.jpg') }});"></a>
                    <div class="half left-arrow d-flex align-items-center">
                        <div class="text p-4 p-xl-5 text-center">
                            <p class="star mb-0"><span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span></p>
                            <p class="mb-0">{{ $room->room_type->name }}</p>
                            <h3 class="mb-3"><a href="#">{{ $room->name }}</a></h3>
                            <ul class="list-accomodation">
                                <li><span>Maks:</span> {{ $room->max_people }} Orang</li>
                                <li><span>Status:</span> {{ App\Enums\RoomStatus::getDescription($room_status) }}</li>
                                <li>{!! implode('<br>', $borrower_status) !!}</li>
                            </ul>
                            <p class="pt-1">
                                <a href="javascript:void(0)" id="buttonBorrowRoomModal" class="btn-custom px-3 py-2" data-toggle="modal" data-target="#borrowRoomModal" data-room-id="{{ $room->id }}" data-room-name="{{ $room->name }}">
                                    Pinjam Ruang Ini <span class="icon-long-arrow-right"></span>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        </div>
    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="borrowRoomModal" tabindex="-1" role="dialog" aria-labelledby="borrowRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title" id="borrowRoomModalLabel">Pinjam Ruang - Nama Ruang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('api.v1.borrow-room-with-pegawai', []) }}" class="appointment-form">
                @csrf
                <input id="room" name="room" type="hidden" value="{{ old('room') }}">

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <input name="full_name" type="text" class="form-control" placeholder="Nama Lengkap Pegawai">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-wrap">
                                <div class="icon"><span class="ion-md-calendar"></span></div>
                                <input name="borrow_at" type="text" class="form-control appointment_date-check-in" placeholder="Tgl Mulai">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-wrap">
                                <div class="icon"><span class="ion-md-calendar"></span></div>
                                <input name="until_at" type="text" class="form-control appointment_date-check-out" placeholder="Tgl Selesai">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-field">
                                <div class="select-wrap">
                                    <select name="head_of_unit" class="form-control">
                                        <option value="" selected disabled>Pilih Kepala Bidang</option>
                                        @forelse ($data['kepala_bidang'] as $head)
                                            <option value="{{ $head->id }}">{{ $head->name }}</option>
                                        @empty
                                            <option value="" disabled>Belum ada kepala bidang yang terdaftar</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <input name="nip" type="text" class="form-control" placeholder="NIP">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="form-field">
                                <div class="select-wrap">
                                    <select name="unit" class="form-control">
                                        <option value="" selected disabled>Pilih Unit / Bidang</option>
                                        <option value="sekretariat">Sekretariat</option>
                                        <option value="bidang-pendidikan">Bidang Pendidikan</option>
                                        <option value="bidang-pemuda-dan-olahraga">Bidang Pemuda dan Olahraga</option>
                                        <option value="bidang-ketenagaan">Bidang Ketenagaan</option>
                                        <option value="bidang-sarana-prasarana">Bidang Sarana dan Prasarana</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <input type="submit" value="Pinjam Ruang Sekarang" class="btn btn-primary">
            </form>
        </div>
    </div>
    </div>
</div>

@section('scripts')
<script>
    $(document).on('click', '#buttonBorrowRoomModal', function () {
        var roomName = $(this).data('room-name');
        var roomId = $(this).data('room-id');

        $('input[name="room"]').val(roomId);
        $('#borrowRoomModalLabel').text('Pinjam Ruang - ' + roomName);
        resetBorrowRoomModalForm();
    });

    function resetBorrowRoomModalForm() {
        $('#borrowRoomModal').find('input[name="full_name"]').val('');
        $('#borrowRoomModal').find('input[name="borrow_at"]').val('');
        $('#borrowRoomModal').find('input[name="until_at"]').val('');
        $('#borrowRoomModal').find('select[name="head_of_unit"]').val($('select[name="head_of_unit"] option:first').val());
        $('#borrowRoomModal').find('input[name="nip"]').val('');
        $('#borrowRoomModal').find('select[name="unit"]').val($('select[name="unit"] option:first').val());
    }
</script>
@endsection
@endsection
