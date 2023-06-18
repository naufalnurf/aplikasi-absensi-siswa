<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Jadwal;
use App\Models\Absensi;
use Illuminate\Http\Request;


class AbsensiController extends Controller
{
    public function index()
    {
        return view('admin.absensi.index', [
            'kelas' => Kelas::paginate(10)
        ]);
    }

    public function show($kelasId)
    {
        $kelas = Kelas::firstWhere('id', $kelasId);

        $count = Siswa::where('kelas_id', $kelasId)->whereHas('absensi')->count();

        return view('admin.absensi.show', compact('kelas', 'count'));
    }

    public function store(Request $request)
    {
        $absensiData = $request->input('siswa');
        $tanggal = now('Asia/Jakarta')->format('Y-m-d');
        $guruId = auth()->user()->id;
        $kelasId = $request->kelas;

        foreach ($absensiData as $index => $siswaId) {
            $status = $request->input('status.' . ($index + 1));

            $existingAbsensi = Absensi::where('siswa_id', $siswaId)
                ->where('tanggal', $tanggal)->first();

            if ($existingAbsensi) {
                // Update absensi jika sudah ada
                $existingAbsensi->update([
                    'status' => $status,
                ]);
                $pesan = 'diupdate';
            } else {
                // Buat absensi baru jika belum ada
                Absensi::create([
                    'siswa_id' => $siswaId,
                    'guru_id' => $guruId,
                    'kelas_id' => $kelasId,
                    'tanggal' => $tanggal,
                    'status' => $status
                ]);
                $pesan = 'disimpan';
            }
        }

        return redirect()->route('absensi.index')->with('success', 'Data absensi berhasil ' .$pesan);
    }

    public function indexGuru()
    {
        $guruId = auth()->user()->guru->id;
        $hariIni = Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('dddd');

        // Mendapatkan jadwal guru hari ini
        $jadwalHariIni = Jadwal::where('guru_id', $guruId)
            ->where('hari', $hariIni)
            ->with('kelas')
            ->orderBy('jam_mulai', 'asc') // Mengurutkan jadwal berdasarkan jam_mulai terkecil
            ->get();

        // Mendapatkan semua jadwal guru yang sedang login
        $jadwalGuru = Jadwal::where('guru_id', $guruId)
            ->with(['guru', 'mapel', 'kelas'])
            ->orderByRaw("CASE
                WHEN hari = 'Senin' THEN 1
                WHEN hari = 'Selasa' THEN 2
                WHEN hari = 'Rabu' THEN 3
                WHEN hari = 'Kamis' THEN 4
                WHEN hari = 'Jumat' THEN 5
                WHEN hari = 'Sabtu' THEN 6
                WHEN hari = 'Minggu' THEN 7
                ELSE 8
            END")
            ->orderBy('jam_mulai')
            ->where('hari', '>=', $hariIni)
            ->get();

        return view('guru.absensi.index', compact('jadwalHariIni', 'jadwalGuru'));
    }

    public function showAbsen($kelasId)
    {
        $kelas = Kelas::firstWhere('id', $kelasId);

        $count = Siswa::where('kelas_id', $kelasId)->whereHas('absensi')->count();

        return view('guru.absensi.show', compact('kelas', 'count'));
    }

    public function storeAbsen(Request $request)
    {
        $absensiData = $request->input('siswa');
        $tanggal = now('Asia/Jakarta')->format('Y-m-d');
        $guruId = auth()->user()->guru_id; // Menggunakan kolom guru_id dari tabel users
        $kelasId = $request->kelas;

        // Mengambil jadwal guru pada tanggal dan kelas yang sesuai
        $jadwal = Jadwal::where('guru_id', $guruId)
            ->where('kelas_id', $kelasId)
            ->where('hari', now('Asia/Jakarta')->locale('id')->isoFormat('dddd')) // Menggunakan nama hari saat ini
            ->first();

        if (!$jadwal) {
            return redirect()->route('absensi.index')->with('error', 'Tidak ada jadwal guru pada hari ini');
        }

        foreach ($absensiData as $index => $siswaId) {
            $status = $request->input('status.' . ($index + 1));

            $existingAbsensi = Absensi::where('siswa_id', $siswaId)
                ->where('guru_id', $guruId)
                ->where('kelas_id', $kelasId)
                ->where('jadwal_id', $jadwal->id)
                ->where('tanggal', $tanggal)
                ->first();

            if ($existingAbsensi) {
                // Update absensi jika sudah ada
                $existingAbsensi->update([
                    'status' => $status,
                ]);
                $pesan = 'diupdate';
            } else {
                // Buat absensi baru jika belum ada
                Absensi::create([
                    'siswa_id' => $siswaId,
                    'guru_id' => $guruId,
                    'kelas_id' => $kelasId,
                    'jadwal_id' => $jadwal->id,
                    'tanggal' => $tanggal,
                    'status' => $status
                ]);
                $pesan = 'disimpan';
            }
        }

        return redirect()->route('guru.absensi.index')->with('success', 'Data absensi berhasil ' . $pesan);
    }

}
