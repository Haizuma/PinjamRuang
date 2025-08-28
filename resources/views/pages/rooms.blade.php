@extends('layouts.default')
@section('content')

  <!-- Hero Section -->
  <section class="hero-wrap hero-wrap-2" style="background-image: url('{{ asset('vendor/technext/vacation-rental/images/bg_1.jpg') }}');" data-stellar-background-ratio="0.5">
    <div class="overlay bg-primary" style="opacity: 0.7;"></div>
    <div class="container">
      <div class="row no-gutters slider-text align-items-center justify-content-center">
        <div class="col-md-9 text-center text-white">
          <p class="breadcrumbs mb-2">
            <span class="mr-2">
              <a href="{{ route('home') }}" class="text-white">Beranda <i class="fa fa-chevron-right"></i></a>
            </span> 
            <span>Ruangan <i class="fa fa-chevron-right"></i></span>
          </p>
          <h1 class="mb-0 font-weight-bold">Daftar Ruangan</h1>
        </div>
      </div>
    </div>
  </section>

  <!-- Room List -->
  <section class="ftco-section bg-light py-5">
    <div class="container">
      <div class="row">
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
          <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
              <div class="row no-gutters h-100">
                <div class="col-md-5">
                  <div class="h-100 bg-cover rounded-left" style="background-image: url('{{ asset('vendor/technext/vacation-rental/images/room-' . rand(1, 6) . '.jpg') }}'); background-size: cover; background-position: center;"></div>
                </div>
                <div class="col-md-7 d-flex align-items-center">
                  <div class="card-body text-center">
                    <h5 class="card-title text-primary font-weight-bold">{{ $room->name }}</h5>
                    <p class="text-muted mb-2">{{ $room->room_type->name }}</p>
                    <ul class="list-unstyled small mb-3">
                      <li><strong>Maks:</strong> {{ $room->max_people }} Orang</li>
                      <li><strong>Status:</strong> {{ App\Enums\RoomStatus::getDescription($room_status) }}</li>
                      <li class="text-info">{!! implode('<br>', $borrower_status) !!}</li>
                    </ul>
                    <button 
                      class="btn btn-primary btn-sm px-4" 
                      id="buttonBorrowRoomModal" 
                      data-toggle="modal" 
                      data-target="#borrowRoomModal" 
                      data-room-id="{{ $room->id }}" 
                      data-room-name="{{ $room->name }}">
                      Pinjam Ruang Ini
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- Modal Pinjam Ruang -->
  <div class="modal fade" id="borrowRoomModal" tabindex="-1" role="dialog" aria-labelledby="borrowRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="borrowRoomModalLabel">Pinjam Ruang - Nama Ruang</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <form method="POST" action="{{ route('api.v1.borrow-room-with-pegawai') }}">
          @csrf
          <input id="room" name="room" type="hidden" value="{{ old('room') }}">

          <div class="modal-body">
            <div class="row">
              <!-- Nama Lengkap -->
              <div class="col-md-12 mb-3">
                <label class="small font-weight-bold">Nama Lengkap</label>
                <input name="full_name" value="{{ old('full_name') }}" type="text" class="form-control" placeholder="Masukkan nama lengkap Anda">
              </div>

              <!-- Tanggal & Jam Mulai -->
              <div class="col-md-6 mb-3">
                <label class="small font-weight-bold">Tanggal & Jam Mulai</label>
                <div class="input-group date" id="borrow_at_picker_modal" data-target-input="nearest">
                  <input name="borrow_at" value="{{ old('borrow_at') }}" type="text" class="form-control datetimepicker-input" placeholder="Pilih tanggal & jam mulai" data-target="#borrow_at_picker_modal"/>
                  <div class="input-group-append" data-target="#borrow_at_picker_modal" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                  </div>
                </div>
              </div>

              <!-- Tanggal & Jam Selesai -->
              <div class="col-md-6 mb-3">
                <label class="small font-weight-bold">Tanggal & Jam Selesai</label>
                <div class="input-group date" id="until_at_picker_modal" data-target-input="nearest">
                  <input name="until_at" value="{{ old('until_at') }}" type="text" class="form-control datetimepicker-input" placeholder="Pilih tanggal & jam selesai" data-target="#until_at_picker_modal"/>
                  <div class="input-group-append" data-target="#until_at_picker_modal" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                  </div>
                </div>
              </div>

              <!-- Kepala Bidang -->
              <div class="col-md-6 mb-3">
                <label class="small font-weight-bold">Pilih Kepala Bidang</label>
                <select name="kepala_bidang" class="form-control">
                  <option value="" selected disabled>Pilih Kepala Bidang</option>
                  @forelse ($data['kepala_bidang'] as $key => $name)
                    <option value="{{ $key }}" @if(old('kepala_bidang') == $key) selected @endif>{{ $name }}</option>
                  @empty
                    <option value="" disabled>Belum ada Kepala Bidang yang terdaftar</option>
                  @endforelse
                </select>
              </div>

              <!-- NIP -->
              <div class="col-md-6 mb-3">
                <label class="small font-weight-bold">NIP</label>
                <input name="nip" value="{{ old('nip') }}" type="text" class="form-control" placeholder="Masukkan NIP Anda">
              </div>

              <!-- Unit / Bidang -->
              <div class="col-md-12 mb-3">
                <label class="small font-weight-bold">Unit / Bidang</label>
                <select name="unit_kerja" class="form-control">
                  <option value="" selected disabled>Pilih Unit / Bidang</option>
                  <option value="bidang-pembinaan-smk" @if(old('unit_kerja') == 'bidang-pembinaan-smk') selected @endif>Bidang Pembinaan SMK</option>
                  <option value="bidang-pembinaan-sma" @if(old('unit_kerja') == 'bidang-pembinaan-sma') selected @endif>Bidang Pembinaan SMA</option>
                  <option value="bidang-pklk" @if(old('unit_kerja') == 'bidang-pklk') selected @endif>Bidang PKLK</option>
                  <option value="bidang-perencanaan-dan-pmppo" @if(old('unit_kerja') == 'bidang-perencanaan-dan-pmppo') selected @endif>Bidang Perencanaan dan PMPPO</option>
                  <option value="bidang-subbag-kepegawaian" @if(old('unit_kerja') == 'bidang-subbag-kepegawaian') selected @endif>Bidang Subbag Kepegawaian</option>
                  <option value="bidang-subbag-keuangan" @if(old('unit_kerja') == 'bidang-subbag-keuangan') selected @endif>Bidang Subbag Keuangan</option>
                  <option value="bidang-subbag-umum" @if(old('unit_kerja') == 'bidang-subbag-umum') selected @endif>Bidang Subbag Umum</option>
                </select>
              </div>
              <!-- Catatan -->
              <div class="form-group col-md-12">
                <label class="font-weight-semibold">Catatan (Keperluan Peminjaman)</label>
                <textarea name="notes" class="form-control" rows="3"
                  placeholder="Contoh: Digunakan untuk rapat koordinasi Bidang Pembinaan SMK">{{ old('notes') }}</textarea>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Pinjam Ruang Sekarang</button>
          </div>
        </form>
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
        $('#borrowRoomModal').find('select[name="kepala_bidang"]').val($('select[name="kepala_bidang"] option:first').val());
        $('#borrowRoomModal').find('input[name="nip"]').val('');
        $('#borrowRoomModal').find('select[name="unit_kerja"]').val($('select[name="unit_kerja"] option:first').val());
    }

    // Datepicker Modal
    $('#borrow_at_picker_modal').datetimepicker({ format: 'DD-MM-YYYY HH:mm' });
    $('#until_at_picker_modal').datetimepicker({ format: 'DD-MM-YYYY HH:mm', useCurrent: false });
    $("#borrow_at_picker_modal").on("change.datetimepicker", function (e) {
        $('#until_at_picker_modal').datetimepicker('minDate', e.date);
    });
  </script>
  @endsection
@endsection
