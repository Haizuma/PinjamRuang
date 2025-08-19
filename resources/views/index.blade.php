@extends('layouts.default')
@section('content')

<div class="hero-wrap js-fullheight" style="background-image: url('vendor/technext/vacation-rental/images/bg_1.jpg');" data-stellar-background-ratio="0.5">
  <div class="overlay"></div>
  <div class="container">
    <div class="row no-gutters slider-text js-fullheight align-items-center justify-content-start" data-scrollax-parent="true">
      <div class="col-md-7 ftco-animate">
        <h2 class="subheading">Selamat datang di Sistem Peminjaman Ruang</h2>
        <h1 class="mb-4">Ajukan peminjaman ruangan dengan mudah dan cepat</h1>
        <p><a href="#" class="btn btn-primary">Pelajari lebih lanjut</a> <a href="#" class="btn btn-white">Hubungi kami</a></p>
      </div>
    </div>
  </div>
</div>

<section id="form-pinjam-ruang" class="ftco-section ftco-book ftco-no-pt ftco-no-pb">
  <div class="container">
    <div class="row justify-content-end">
      <div class="col-lg-4">
        <form method="POST" action="{{ route('api.v1.borrow-room-with-pegawai') }}" class="appointment-form">
          @csrf
          <h3 class="mb-3">Ajukan Peminjaman Ruang</h3>

          @if ($errors->isNotEmpty())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              @foreach ($errors->all() as $message)
                @if ($message == 'login_for_more_info')
                  <a href="{{ route('admin.login') }}">Masuk</a> untuk melihat aktivitas peminjaman.
                @else
                  {{ $message }}<br>
                @endif
              @endforeach
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          @endif

          @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              Pengajuan berhasil. Silakan cek status peminjaman <a href="{{ route('admin.login') }}">di sini</a>. Masuk menggunakan username dan password NIP.
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          @endif

          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <input name="full_name" value="{{ old('full_name') }}" type="text" class="form-control" placeholder="Nama Lengkap">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <div class="input-wrap">
                  <div class="icon"><span class="ion-md-calendar"></span></div>
                  <input id="borrow_at" name="borrow_at" value="{{ old('borrow_at') }}" type="text" class="form-control datetimepicker-input" placeholder="Tgl Mulai" data-toggle="datetimepicker" data-target="#borrow_at">
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <div class="input-wrap">
                  <div class="icon"><span class="ion-md-calendar"></span></div>
                  <input id="until_at" name="until_at" value="{{ old('until_at') }}" type="text" class="form-control datetimepicker-input" placeholder="Tgl Selesai" data-toggle="datetimepicker" data-target="#until_at">
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <div class="form-field">
                  <div class="select-wrap">
                    <div class="icon"><span class="fa fa-chevron-down"></span></div>
                    <select name="room" class="form-control">
                      <option value="" selected disabled>Pilih ruangan</option>
                      @forelse ($data['rooms'] as $room)
                        <option value="{{ $room->id }}" @if(old('room') == $room->id) selected @endif>
                          {{ $room->room_type->name . ' - ' . $room->name }}
                        </option>
                      @empty
                        <option value="" disabled>Belum ada ruangan yang tersedia</option>
                      @endforelse
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <div class="form-field">
                  <div class="select-wrap">
                    <div class="icon"><span class="fa fa-chevron-down"></span></div>
                    <select name="kepala_bidang" class="form-control">
                      <option value="" selected disabled>Pilih Kepala Bidang</option>
                      @forelse ($data['kepala_bidang'] as $key => $name)
                        <option value="{{ $key }}" @if(old('kepala_bidang') == $key) selected @endif>{{ $name }}</option>
                      @empty
                        <option value="" disabled>Belum ada Kepala Bidang yang terdaftar</option>
                      @endforelse
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input name="nip" value="{{ old('nip') }}" type="text" class="form-control" placeholder="NIP">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <div class="form-field">
                  <div class="select-wrap">
                    <div class="icon"><span class="fa fa-chevron-down"></span></div>
                    <select name="unit_kerja" class="form-control">
                      <option value="" selected disabled>Unit / Bidang</option>
                      <option value="bidang-pemuda-olahraga" @if(old('unit_kerja') == 'bidang-pemuda-olahraga') selected @endif>Bidang Pemuda & Olahraga</option>
                      <option value="bidang-pendidikan-dasar" @if(old('unit_kerja') == 'bidang-pendidikan-dasar') selected @endif>Bidang Pendidikan Dasar</option>
                      <option value="bidang-paud-dikmas" @if(old('unit_kerja') == 'bidang-paud-dikmas') selected @endif>Bidang PAUD & DIKMAS</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <input type="submit" value="Pinjam Ruang Sekarang" class="btn btn-primary py-3 px-4">
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

@section('scripts')
<script>
  $(document).ready(function() {
    $('.appointment_date-check-in, #borrow_at_alt').datetimepicker({ format:'DD-MM-YYYY HH:mm' });
    $('.appointment_date-check-out, #until_at_alt').datetimepicker({ format:'DD-MM-YYYY HH:mm' });
  });

  @if ($errors->isNotEmpty())
    $(document).ready(function(){
      if (/Android|iPhone|iPad|Mac|Macintosh|iPod|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        document.getElementById("form-pinjam-ruang").scrollIntoView();
      }
    });
  @endif
</script>
@endsection