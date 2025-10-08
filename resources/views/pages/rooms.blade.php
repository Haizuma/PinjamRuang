@extends('layouts.default')
@section('content')

    <section class="hero-wrap hero-wrap-2"
        style="background-image: url('{{ asset('vendor/technext/vacation-rental/images/bg_1.jpg') }}');"
        data-stellar-background-ratio="0.5">
        <div class="overlay bg-primary" style="opacity: 0.7;"></div>
        <div class="container">
            <div class="row no-gutters slider-text align-items-center justify-content-center">
                <div class="col-md-9 text-center text-white">
                    <p class="breadcrumbs mb-2">
                        <span class="mr-2"><a href="{{ route('home') }}" class="text-white">Beranda <i
                                    class="fa fa-chevron-right"></i></a></span>
                        <span>Ruangan <i class="fa fa-chevron-right"></i></span>
                    </p>
                    <h1 class="mb-0 font-weight-bold">Daftar Ruangan</h1>
                </div>
            </div>
        </div>
    </section>
    @if ($errors->any())
        <div class="container my-4">
            <div class="alert alert-danger shadow-sm">
                <h5 class="alert-heading">Terjadi Kesalahan!</h5>
                <p>Mohon periksa kembali input Anda:</p>
                <hr>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    @if (session('success'))
        <div class="container my-4">
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        </div>
    @endif
    {{-- Form Filter Ruangan dan Hari --}}
    <section class="ftco-section pb-0 pt-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h3 class="card-title text-center text-primary mb-4">Lihat Jadwal Ruangan</h3>
                            <form action="{{ route('rooms') }}" method="GET">
                                <div class="form-row align-items-end">
                                    <div class="form-group col-md-6">
                                        <label for="room_id">Pilih Ruangan</label>
                                        <select name="room_id" class="form-control">
                                            <option value="">-- Semua Ruangan --</option>
                                            @foreach ($data['all_rooms'] as $room_option)
                                                <option value="{{ $room_option->id }}"
                                                    @if (request('room_id') == $room_option->id) selected @endif>
                                                    {{ $room_option->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="selected_date">Pilih Tanggal</label>
                                        <div class="input-group date" id="date_picker" data-target-input="nearest">
                                            <input type="text" name="selected_date"
                                                class="form-control datetimepicker-input" data-target="#date_picker"
                                                value="{{ request('selected_date') }}" />
                                            <div class="input-group-append" data-target="#date_picker"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <button type="submit" class="btn btn-primary btn-block">Lihat</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="ftco-section bg-light py-5">
        <div class="container">
            {{-- Notifikasi Filter --}}
            @if (request('room_id') || request('selected_date'))
                <div class="row justify-content-center mb-4">
                    <div class="col-md-10">
                        <div class="alert alert-info d-flex justify-content-between align-items-center">
                            <span>Menampilkan hasil filter.</span>
                            <a href="{{ route('rooms') }}" class="btn btn-sm btn-light border">Hapus Filter</a>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row justify-content-center">
                @forelse ($data['rooms'] as $room)
                    @php
                        // Logika di view sekarang jauh lebih sederhana!
                        // $room->borrow_rooms sudah berisi jadwal yang terfilter oleh Controller.
                        $schedules = $room->borrow_rooms;

                        // Cek apakah ruangan sedang digunakan saat ini (gunakan method dari Model)
                        $isCurrentlyBooked = $room->isCurrentlyBooked();
                        $room_status = $isCurrentlyBooked ? 1 : $room->status;

                        // Mapping gambar (tidak berubah)
                        $roomImages = [
                            'Sasana Mitra' => 'room-1.jpg',
                            'Sasana Krida' => 'room-2.jpg',
                            'Sasana Wiyata' => 'room-4.jpg',
                            'Ruang Rapat SPAB' => 'room-5.jpg',
                            'Ruang Rapat Wakadis' => 'room-6.jpg',
                            'Sasana Cipta' => 'room-7.JPG',
                        ];
                        $imageFile = $roomImages[$room->name] ?? 'default-room.jpg';
                    @endphp
                    <div class="col-md-10 mb-4">
                        <div class="card border-0 shadow-sm">
                            <img class="card-img-top"
                                src="{{ asset('vendor/technext/vacation-rental/images/' . $imageFile) }}"
                                alt="Gambar Ruangan {{ $room->name }}" style="height: 200px; object-fit: cover;">

                            <div class="card-body text-center">
                                <h3 class="card-title text-primary font-weight-bold">{{ $room->name }}</h3>
                                <p class="text-muted mb-2">{{ $room->room_type->name }}</p>
                                <ul class="list-unstyled small mb-3">
                                    <li><strong>Maks:</strong> {{ $room->max_people }} Orang</li>
                                    <li><strong>Status:</strong> {{ App\Enums\RoomStatus::getDescription($room_status) }}
                                    </li>
                                </ul>

                                <a class="btn btn-outline-secondary btn-sm px-3" data-toggle="collapse"
                                    href="#schedule-{{ $room->id }}" role="button" aria-expanded="false"
                                    aria-controls="schedule-{{ $room->id }}">
                                    <i class="fa fa-calendar-alt mr-1"></i> Lihat Jadwal
                                </a>

                                {{-- PERBAIKAN: Menggunakan CLASS bukan ID --}}
                                <button class="btn btn-primary btn-sm px-4 buttonBorrowRoomModal" data-toggle="modal"
                                    data-target="#borrowRoomModal" data-room-id="{{ $room->id }}"
                                    data-room-name="{{ $room->name }}">
                                    Pinjam Ruang Ini
                                </button>
                            </div>

                            <div class="collapse @if (request('room_id') == $room->id) show @endif"
                                id="schedule-{{ $room->id }}">
                                <div class="card card-body border-top">
                                    @if ($schedules->isNotEmpty())
                                        <h6 class="text-center mb-3">Jadwal Peminjaman
                                            {{ request('selected_date') ? 'pada ' . request('selected_date') : '' }}</h6>
                                        <table class="table table-sm table-bordered small">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Nama</th>
                                                    <th scope="col">Mulai Pinjam</th>
                                                    <th scope="col">Selesai Pinjam</th>
                                                    <th scope="col">Kegiatan</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-light">
                                                @foreach ($schedules->take(5) as $index => $schedule)
                                                    <tr>
                                                        <th scope="row">{{ $index + 1 }}</th>
                                                        <td>{{ $schedule->borrower->name ?? '-' }}</td>
                                                        <td>{{ Carbon\Carbon::parse($schedule->borrow_at)->format('d M Y, H:i') }}
                                                        </td>
                                                        <td>{{ Carbon\Carbon::parse($schedule->until_at)->format('d M Y, H:i') }}
                                                        </td>
                                                        <td>{{ $schedule->notes ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <p class="text-center text-muted mb-0">Tidak ada jadwal peminjaman
                                            {{ request('selected_date') ? 'pada tanggal ini' : 'untuk ruangan ini' }}.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <div class="alert alert-warning">
                            Tidak ada ruangan yang ditemukan sesuai dengan filter Anda.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <div class="modal fade" id="borrowRoomModal" tabindex="-1" role="dialog" aria-labelledby="borrowRoomModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="borrowRoomModalLabel">Pinjam Ruang - Nama Ruang</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form method="POST" action="{{ route('borrow.store') }}">
                    @csrf
                    <input id="room" name="room" type="hidden" value="{{ old('room') }}">

                    <div class="modal-body">
                        <div class="row">
                            <!-- Nama Lengkap -->
                            <div class="col-md-12 mb-3">
                                <label class="font-weight-bold">Nama Lengkap</label>
                                <input name="full_name" value="{{ old('full_name') }}" type="text"
                                    class="form-control" placeholder="Masukkan nama lengkap Anda">
                            </div>

                            <!-- Tanggal & Jam Mulai -->
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Tanggal & Jam Mulai</label>
                                <div class="input-group date" id="borrow_at_picker_modal" data-target-input="nearest">
                                    <input name="borrow_at" value="{{ old('borrow_at') }}" type="text"
                                        class="form-control datetimepicker-input" placeholder="Pilih tanggal & jam mulai"
                                        data-target="#borrow_at_picker_modal" />
                                    <div class="input-group-append" data-target="#borrow_at_picker_modal"
                                        data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tanggal & Jam Selesai -->
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Tanggal & Jam Selesai</label>
                                <div class="input-group date" id="until_at_picker_modal" data-target-input="nearest">
                                    <input name="until_at" value="{{ old('until_at') }}" type="text"
                                        class="form-control datetimepicker-input"
                                        placeholder="Pilih tanggal & jam selesai" data-target="#until_at_picker_modal" />
                                    <div class="input-group-append" data-target="#until_at_picker_modal"
                                        data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Kepala Bidang -->
                            <input type="hidden" name="kepala_bidang" value="65">
                            {{-- <div class="col-md-6 mb-3">
                                        <label class="small font-weight-bold">Pilih Kepala Bidang</label>
                                        <select name="kepala_bidang" class="form-control">
                                            <option value="" selected disabled>Pilih Kepala Bidang</option>
                                            @forelse ($data['kepala_bidang'] as $key => $name)
                                                <option value="{{ $key }}" @if (old('kepala_bidang') == $key) selected @endif>{{ $name }}
                                                </option>
                                            @empty
                                                <option value="" disabled>Belum ada Kepala Bidang yang terdaftar</option>
                                            @endforelse
                                        </select>
                                    </div> --}}

                            <!-- NIP -->
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">NIP</label>
                                <input name="nip" value="{{ old('nip') }}" type="text" class="form-control"
                                    placeholder="Masukkan NIP Anda">
                            </div>

                            <!-- Unit / Bidang -->
                            <div class="col-md-12 mb-3">
                                <label class="font-weight-bold">Unit / Bidang</label>
                                <select name="unit_kerja" class="form-control">
                                    <option value="" selected disabled>Pilih Unit / Bidang</option>
                                    <option value="bidang-pembinaan-smk" @if (old('unit_kerja') == 'bidang-pembinaan-smk') selected @endif>
                                        Bidang Pembinaan SMK</option>
                                    <option value="bidang-pembinaan-sma" @if (old('unit_kerja') == 'bidang-pembinaan-sma') selected @endif>
                                        Bidang Pembinaan SMA</option>
                                    <option value="bidang-pklk" @if (old('unit_kerja') == 'bidang-pklk') selected @endif>Bidang
                                        PKLK</option>
                                    <option value="bidang-perencanaan-dan-pmppo"
                                        @if (old('unit_kerja') == 'bidang-perencanaan-dan-pmppo') selected @endif>Bidang
                                        Perencanaan dan PMPPO</option>
                                    <option value="bidang-subbag-kepegawaian"
                                        @if (old('unit_kerja') == 'bidang-subbag-kepegawaian') selected @endif>Bidang Subbag
                                        Kepegawaian</option>
                                    <option value="bidang-subbag-keuangan"
                                        @if (old('unit_kerja') == 'bidang-subbag-keuangan') selected @endif>Bidang Subbag Keuangan</option>
                                    <option value="bidang-subbag-umum" @if (old('unit_kerja') == 'bidang-subbag-umum') selected @endif>
                                        Bidang Subbag Umum</option>
                                </select>
                            </div>
                            <!-- Catatan -->
                            <div class="form-group col-md-12">
                                <label class="font-weight-bold">Kegiatan (Keperluan Peminjaman)</label>
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
@endsection

@push('scripts')
    <script>
        $(function() {
            // Inisialisasi untuk filter tanggal di luar modal
            $('#date_picker').datetimepicker({
                format: 'L',
            });

            // Inisialisasi untuk form DI DALAM MODAL
            var borrowAtPicker = $('#borrow_at_picker_modal');
            var untilAtPicker = $('#until_at_picker_modal');
            var modal = $('#borrowRoomModal');

            borrowAtPicker.datetimepicker({
                format: 'DD-MM-YYYY HH:mm' // Format yang benar dengan waktu
            });

            untilAtPicker.datetimepicker({
                format: 'DD-MM-YYYY HH:mm', // Format yang benar dengan waktu
                useCurrent: false
            });

            // Menghubungkan date picker mulai dan selesai
            borrowAtPicker.on("change.datetimepicker", function(e) {
                untilAtPicker.datetimepicker('minDate', e.date);
            });
            untilAtPicker.on("change.datetimepicker", function(e) {
                borrowAtPicker.datetimepicker('maxDate', e.date);
            });

            // Event listener untuk tombol 'Pinjam Ruang Ini'
            $(document).on('click', '.buttonBorrowRoomModal', function() {
                var roomName = $(this).data('room-name');
                var roomId = $(this).data('room-id');
                var modal = $('#borrowRoomModal');

                // Mengisi data ruangan ke dalam form modal
                modal.find('input[name="room"]').val(roomId);
                modal.find('#borrowRoomModalLabel').text('Pinjam Ruang - ' + roomName);

                // Mengosongkan field yang diisi pengguna secara manual
                modal.find('input[name="full_name"]').val('');
                modal.find('input[name="borrow_at"]').val('');
                modal.find('input[name="until_at"]').val('');
                modal.find('input[name="nip"]').val('');
                modal.find('select[name="unit_kerja"]').prop('selectedIndex', 0);
                modal.find('textarea[name="notes"]').val('');
            });
        });
    </script>
@endpush
