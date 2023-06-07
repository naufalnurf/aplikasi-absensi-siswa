{{-- @dd($count); --}}
@extends('layouts.admin.app', ['title' => 'Data Kelas'])

@section('content')
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="{{ route('absensi.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Absensi Kelas
                {{ $kelas->tingkat_kelas . ' ' . $kelas->jurusan . ' ' . $kelas->nama . ' (' . Carbon\Carbon::now('Asia/Jakarta')->format('d F Y') . ')' }}
            </h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="/">Dashboard</a></div>
                <div class="breadcrumb-item active"><a href="{{ route('absensi.index') }}">Absensi</a></div>
                <div class="breadcrumb-item">{{ $kelas->tingkat_kelas }} {{ $kelas->jurusan }} {{ $kelas->nama }}</div>
            </div>
        </div>
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible show fade col-lg-7 col-md-12 col-12 col-sm-12">
                <div class="alert-body">
                    <button class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible show fade col-lg-7 col-md-12 col-12 col-sm-12">
                <div class="alert-body">
                    <button class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                    {{ session('error') }}
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-7 col-md-12 col-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Data Siswa</h4>
                    </div>
                    @if (count($kelas->siswa) > 0)
                        <form action="/admin/absensi" method="post">
                            @csrf
                            <input type="hidden" name="kelas" value="{{ $kelas->id }}">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>NIS</th>
                                                <th>Nama</th>
                                                <th>Jenis Kelamin</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($kelas->siswa as $siswa)
                                                <tr>
                                                    <input type="hidden" name="siswa[]" value="{{ $siswa->id }}">
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $siswa->nis }}</td>
                                                    <td>{{ $siswa->nama }}</td>
                                                    <td>{{ $siswa->jk }}</td>
                                                    <td>
                                                        <div class="selectgroup w-50">
                                                            <label class="selectgroup-item">
                                                                <input type="radio" name="status[{{ $siswa->id }}]"
                                                                    value="A" class="selectgroup-input"
                                                                    {{ optional($siswa->absensi->where('tanggal', now('Asia/Jakarta')->format('Y-m-d'))->first())->status === 'A' ? 'checked' : '' }}
                                                                    {{ !$siswa->absensi->where('tanggal', now('Asia/Jakarta')->format('Y-m-d'))->first() ? 'checked' : '' }}>
                                                                <span class="selectgroup-button">A</span>
                                                            </label>
                                                            <label class="selectgroup-item">
                                                                <input type="radio" name="status[{{ $siswa->id }}]"
                                                                    value="S" class="selectgroup-input"
                                                                    {{ optional($siswa->absensi->where('tanggal', now('Asia/Jakarta')->format('Y-m-d'))->first())->status === 'S' ? 'checked' : '' }}>
                                                                <span class="selectgroup-button">S</span>
                                                            </label>
                                                            <label class="selectgroup-item">
                                                                <input type="radio" name="status[{{ $siswa->id }}]"
                                                                    value="I" class="selectgroup-input"
                                                                    {{ optional($siswa->absensi->where('tanggal', now('Asia/Jakarta')->format('Y-m-d'))->first())->status === 'I' ? 'checked' : '' }}>
                                                                <span class="selectgroup-button">I</span>
                                                            </label>
                                                            <label class="selectgroup-item">
                                                                <input type="radio" name="status[{{ $siswa->id }}]"
                                                                    value="H" class="selectgroup-input"
                                                                    {{ optional($siswa->absensi->where('tanggal', now('Asia/Jakarta')->format('Y-m-d'))->first())->status === 'H' ? 'checked' : '' }}>
                                                                <span class="selectgroup-button">H</span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-center m-3">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="card-body">
                            <div class="empty-state" data-height="400">
                                <div class="empty-state-icon">
                                    <i class="fas fa-question"></i>
                                </div>
                                <h2>Tidak ada siswa dalam kelas ini</h2>
                                <p class="lead">
                                    Untuk menghilangkan pesan ini, tambahkan setidaknya 1 siswa kedalam kelas.
                                </p>
                                <a href="../kelas/tambah-siswa/{{ $kelas->id }}" class="btn btn-primary mt-4">Tambah
                                    Siswa
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection
@section('js')
    <script>
        function deleteSiswa(kelasId) {
            // Mengambil referensi formulir dengan menggunakan ID yang unik
            var form = document.getElementById('deleteForm-' + kelasId);

            // Mengatur atribut action pada formulir
            form.action = "hapus-siswa/" + kelasId; // Misalkan URL delete berisi parameter kelas ID

            // Melakukan submit formulir
            form.submit();
        }
    </script>
@endsection
