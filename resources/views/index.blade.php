@extends('layouts.default')
@section('content')

<!-- Hero Section -->
<section class="hero-wrap" style="background-image: url('{{ asset('vendor/technext/vacation-rental/images/bg_1.jpg') }}'); 
           background-size: cover; background-position: center;" data-stellar-background-ratio="0.5">

  <!-- Overlay -->
  <div class="overlay bg-primary" style="opacity: 0.7;"></div>

  <div class="container">
    <div class="row slider-text align-items-center justify-content-center text-center" style="min-height: 70vh;">
      <div class="col-md-9 text-white ftco-animate">
        <h2 class="subheading font-weight-light mb-2">Selamat Datang di</h2>
        <h1 class="mb-4 font-weight-bold">Sistem Peminjaman Ruangan Disdikpora DIY</h1>
        <p class="mt-4">
          <a href="#form-pinjam-ruang" class="btn btn-warning text-dark font-weight-bold px-4 py-2 mr-2 shadow-sm">
            Ajukan Peminjaman
          </a>
          <a href="{{ route('admin.login')}}" class="btn btn-outline-light px-4 py-2 shadow-sm">
            Login
          </a>
        </p>
      </div>
    </div>
  </div>
</section>


<!-- Form Peminjaman -->
<section id="form-pinjam-ruang" class="ftco-section bg-light py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card shadow-lg border-0 rounded-lg">
          <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Ajukan Peminjaman Ruang</h3>
          </div>
          <div class="card-body p-4">

            <!-- Notifikasi -->
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
                Pengajuan berhasil ✅. Silakan cek status peminjaman
                <a href="{{ route('admin.login') }}" class="font-weight-bold">di sini</a>.
                Masuk menggunakan username dan password NIP.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('api.v1.borrow-room-with-pegawai') }}">
              @csrf
              <div class="form-row">
                <div class="form-group col-md-12">
                  <label class="font-weight-semibold">Nama Lengkap</label>
                  <input name="full_name" value="{{ old('full_name') }}" type="text" class="form-control"
                    placeholder="Masukkan nama lengkap Anda">
                </div>

                <div class="form-group col-md-6">
                  <label class="font-weight-semibold">Tanggal & Jam Mulai</label>
                  <div class="input-group date" id="borrow_at_picker" data-target-input="nearest">
                    <input id="borrow_at" name="borrow_at" value="{{ old('borrow_at') }}" type="text"
                      class="form-control datetimepicker-input" placeholder="Pilih tanggal & jam mulai"
                      data-target="#borrow_at_picker" />
                    <div class="input-group-append" data-target="#borrow_at_picker" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>

                <div class="form-group col-md-6">
                  <label class="font-weight-semibold">Tanggal & Jam Selesai</label>
                  <div class="input-group date" id="until_at_picker" data-target-input="nearest">
                    <input id="until_at" name="until_at" value="{{ old('until_at') }}" type="text"
                      class="form-control datetimepicker-input" placeholder="Pilih tanggal & jam selesai"
                      data-target="#until_at_picker" />
                    <div class="input-group-append" data-target="#until_at_picker" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>

                <div class="form-group col-md-6">
                  <label class="font-weight-semibold">Pilih Ruangan</label>
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

                <div class="form-group col-md-6">
                  <label class="font-weight-semibold">Pilih Kepala Bidang</label>
                  <select name="kepala_bidang" class="form-control">
                    <option value="" selected disabled>Pilih Kepala Bidang</option>
                    @forelse ($data['kepala_bidang'] as $key => $name)
                      <option value="{{ $key }}" @if(old('kepala_bidang') == $key) selected @endif>{{ $name }}</option>
                    @empty
                      <option value="" disabled>Belum ada Kepala Bidang yang terdaftar</option>
                    @endforelse
                  </select>
                </div>

                <div class="form-group col-md-6">
                  <label class="font-weight-semibold">NIP</label>
                  <input name="nip" value="{{ old('nip') }}" type="text" class="form-control"
                    placeholder="Masukkan NIP Anda">
                </div>

                <div class="form-group col-md-6">
                  <label class="font-weight-semibold">Unit / Bidang</label>
                  <select name="unit_kerja" class="form-control">
                    <option value="" selected disabled>Pilih Unit / Bidang</option>
                    <option value="bidang-pemuda-olahraga" @if(old('unit_kerja') == 'bidang-pemuda-olahraga') selected
                    @endif>Bidang Pembinaan SMK</option>
                    <option value="bidang-pendidikan-dasar" @if(old('unit_kerja') == 'bidang-pendidikan-dasar') selected
                    @endif>Bidang Pembinaan SMA</option>
                    <option value="bidang-paud-dikmas" @if(old('unit_kerja') == 'bidang-paud-dikmas') selected @endif>
                      Bidang PKLK</option>
                      <option value="bidang-paud-dikmas" @if(old('unit_kerja') == 'bidang-paud-dikmas') selected @endif>
                        Bidang Perencanaan dan PMPPO</option>
                        <option value="bidang-paud-dikmas" @if(old('unit_kerja') == 'bidang-paud-dikmas') selected @endif>
                          Bidang Subbag Kepegawaian</option>
                          <option value="bidang-paud-dikmas" @if(old('unit_kerja') == 'bidang-paud-dikmas') selected @endif>
                            Bidang Subbag Keuangan</option>
                            <option value="bidang-paud-dikmas" @if(old('unit_kerja') == 'bidang-paud-dikmas') selected @endif>
                              Bidang Subbag Umum</option>
                  </select>
                </div>
              </div>

              <div class="form-group text-center mt-3">
                <button type="submit" class="btn btn-primary px-5 py-2 font-weight-bold">
                  Pinjam Ruang Sekarang
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@section('scripts')
  <script>
    $(function () {
      $('#borrow_at_picker').datetimepicker({ format: 'DD-MM-YYYY HH:mm' });
      $('#until_at_picker').datetimepicker({ format: 'DD-MM-YYYY HH:mm' });
    });

    @if ($errors->isNotEmpty())
      $(document).ready(function () {
        if (/Android|iPhone|iPad|Mac|Macintosh|iPod|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
          document.getElementById("form-pinjam-ruang").scrollIntoView();
        }
      });
    @endif
  </script>
@endsection