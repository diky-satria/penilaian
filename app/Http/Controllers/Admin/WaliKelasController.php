<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Nilai;
use App\Models\Santri;
use App\Models\TahunPelajaran;
use App\Models\User;
use App\Models\Ustad;
use App\Models\WaliKelas;
use App\Models\WaliKelasSantri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WaliKelasController extends Controller
{
    public function index()
    {
        $ustad = Ustad::orderBy('nama_ustad', 'asc')
                        ->get();
        $tahun_pelajaran = DB::table('tahun_pelajarans')
                        ->select('tahun_pelajaran')
                        ->groupBy('tahun_pelajaran')
                        ->get();
        $wali_kelas = WaliKelas::orderBy('status','desc')
                                ->get();

        if(request()->ajax()){  
            return datatables()->of($wali_kelas)
                                ->addColumn('ustad', function($wali_kelas){
                                    return $wali_kelas->ustad->nama_ustad;
                                })
                                ->addColumn('status', function($wali_kelas){
                                    if($wali_kelas->status == 0){
                                        $status = '<span class="badge bg-dark rounded-pill">Tidak Aktif</span>';
                                    }else{
                                        $status = '<span class="badge bg-primary rounded-pill">Aktif</span>';
                                    }
                                    return $status;
                                })
                                ->addColumn('action', function($wali_kelas){
                                    $button = '<button class="btn btn-sm btn-success ms-1 edit-wali-kelas" data-bs-toggle="modal" data-bs-target="#staticBackdropEdit" id="'.$wali_kelas->id.'">Edit</button>';
                                    if($wali_kelas->status == 0){
                                        $button .= "<button class='btn btn-sm btn-primary ms-1 aktifkan' id='".$wali_kelas->id."' id_ustad='".$wali_kelas->ustad_id."'>Aktifkan</button>";
                                    }else{
                                        $button .= "<button class='btn btn-sm btn-dark ms-1 non-aktifkan' id='".$wali_kelas->id."'>Non Aktifkan</button>";
                                    }
                                    $button .= "<a href='/wali_kelas/". $wali_kelas->id ."/santri' class='btn btn-sm btn-info ms-1'>Santri</a>";
                                    return $button;
                                })
                                ->rawColumns(['ustad','status','action'])
                                ->make(true);
        }

        return view('admin.wali_kelas.wali_kelas', [
            'ustad' => $ustad,
            'tahun_pelajaran' => $tahun_pelajaran
        ]);
    }

    public function show($id)
    {
        $wali_kelas = WaliKelas::find($id);

        return response()->json([
            'data' => $wali_kelas
        ]);
    }

    public function store()
    {
        request()->validate([
            'ustad' => 'required',
            'tahun_pelajaran' => 'required',
            'semester' => 'required',
            'kelas' => 'required'
        ],[
            'ustad.required' => 'Guru harus dipilih',
            'tahun_pelajaran.required' => 'Tahun pelajaran harus dipilih',
            'semester.required' => 'Semester harus dipilih',
            'kelas.required' => 'Kelas harus dipilih',
        ]);

        // cek jika datanya sudah ada
        $cekDuplikat = WaliKelas::where('tahun_pelajaran', request('tahun_pelajaran'))
                                    ->where('semester', request('semester'))
                                    ->where('kelas', request('kelas'))
                                    ->get();

        if(count($cekDuplikat) > 0){
            return response()->json([
                'duplikat' => 'Tahun pelajaran, semester dan kelas ini sudah terdaftar'
            ]);
        }else{

            // cek santri yang dipilih berdasarkan kelas ada atau tidak
            $cek_santri = Santri::where('kelas', request('kelas'))->count();
            if($cek_santri < 1){
                return response()->json([
                    'duplikat' => 'Santri di kelas ini belum ada, silahkan tambahkan santri nya dulu'
                ]);
            }else{
                // masukan data wali kelas
                $wali_kelas = WaliKelas::create([
                    'ustad_id' => request('ustad'),
                    'tahun_pelajaran' => request('tahun_pelajaran'),
                    'semester' => request('semester'),
                    'kelas' => request('kelas'),
                    'status' => 0,
                ]);
        
                // ambil santri berdasarkan kelas yang dipilih
                $santri = Santri::where('kelas', request('kelas'))->get();
        
                // masukan semua santri berdasarkan kelas yang dipilih ke table wali_kelas_santris
                for($i=0; $i<count($santri); $i++){
                    WaliKelasSantri::create([
                        'wali_kelas_id' => $wali_kelas->id,
                        'santri_id' => $santri[$i]->id
                    ]);
                }
            }

        }

    }

    public function update($id)
    {
        request()->validate([
            'ustad_edit' => 'required'
        ],[
            'ustad_edit.required' => 'Ustad harus dipilih'
        ]);

        // cek wali kelas yang akan di edit status nya
        $cek_status_wali_kelas = WaliKelas::find($id);

        if($cek_status_wali_kelas->status == 1){
            // dalam 3 wali kelas aktif tidak boleh ada nama yang sama
            $cek_wali_kelas_yang_namanya_sama = WaliKelas::where('ustad_id', request('ustad_edit'))
                                                        ->where('status', 1)
                                                        ->get();
    
    
            if(count($cek_wali_kelas_yang_namanya_sama) > 0 && request('ustad_edit') !== request('id_ustad')){
                return response()->json([
                    'duplikat' => 'Tidak boleh ada nama yang sama, di wali kelas yang aktif'
                ]);
            }else{
                // ambil data ustad yang lama
                $ustad_lama = Ustad::find(request('id_ustad'));
                // ambil user nya
                $user_ustad_lama = User::find($ustad_lama->user_id);
                // update user nya
                $user_ustad_lama->update([
                    'role' => 'ustad'
                ]);

                // update wali kelas nya
                $wali_kelas = WaliKelas::find($id);
                $wali_kelas->update([
                    'ustad_id' => request('ustad_edit')
                ]);

                // ambil data ustad yang baru
                $ustad = Ustad::find($wali_kelas->ustad_id);
                // ambil user nya
                $user = User::find($ustad->user_id);
                // update user nya
                $user->update([
                    'role' => 'wali_kelas'
                ]);
        
                return response()->json([
                    'message' => 'Wali kelas berhasil di edit'
                ]);
            }
        }else{
            $wali_kelas = WaliKelas::find($id);
            $wali_kelas->update([
                'ustad_id' => request('ustad_edit')
            ]);
    
            return response()->json([
                'message' => 'Wali kelas berhasil di edit'
            ]);
        }


    }

    public function aktifkan($id, $id_ustad)
    {
        // cek jumlah wali kelas yang aktif, maksimal harus 3 karena kelas nya ada 3
        $cek_jumlah_wali_kelas = WaliKelas::where('status', 1)
                                            ->get();

        if(count($cek_jumlah_wali_kelas) >= 3){
            return response()->json([
                'duplikat' => 'Wali kelas yang aktif tidak boleh lebih dari 3 orang'
            ]);
        }else{

            // dalam 3 wali kelas aktif tidak boleh ada nama yang sama
            $cek_wali_kelas_yang_namanya_sama = WaliKelas::where('ustad_id', $id_ustad)
                                                        ->where('status', 1)
                                                        ->get();

            if(count($cek_wali_kelas_yang_namanya_sama) > 0){
                return response()->json([
                    'duplikat' => 'Tidak boleh ada nama yang sama, di wali kelas yang aktif',
                    'cek' => count($cek_wali_kelas_yang_namanya_sama)
                ]);
            }else{
                // ambil data wali kelas
                $wali_kelas = WaliKelas::find($id);
                // update status nya 
                $wali_kelas->update([
                    'status' => 1
                ]);
        
                // ambil ustadnya
                $ustad = Ustad::find($wali_kelas->ustad_id);
        
                // ambil user ustad nya
                $user = User::find($ustad->user_id);
                // update role access nya
                $user->update([
                    'role' => 'wali_kelas'
                ]);
        
                return response()->json([
                    'message' => 'Berhasil mengaktifkan guru ini menjadi wali kelas',
                    'cek' => count($cek_wali_kelas_yang_namanya_sama)
                ]);
            }
            
        }

    }

    public function non_aktifkan($id)
    {
        // ambil data wali kelas
        $wali_kelas = WaliKelas::find($id);
        $wali_kelas->update([
            'status' => 0
        ]);

        // ambil data ustad 
        $ustad = Ustad::find($wali_kelas->ustad_id);

        // ambil data user
        $user = User::find($ustad->user_id);
        // update role access nya
        $user->update([
            'role' => 'ustad'
        ]);

        return response()->json([
            'message' => 'berhasil menonaktifkan wali kelas'
        ]);
    }

    public function wali_kelas_santri($wali_kelas_id)
    {
        return view('admin.wali_kelas.santri', [
            'route_id' => $wali_kelas_id 
        ]);
    }

    public function ambil_data($wali_kelas_id)
    {
        $wali_kelas_santri = WaliKelasSantri::join('santris', 'santris.id', '=', 'wali_kelas_santris.santri_id')
                                            ->join('wali_kelas', 'wali_kelas.id', '=', 'wali_kelas_santris.wali_kelas_id')
                                            ->where('wali_kelas_id', $wali_kelas_id)
                                            ->select('wali_kelas_santris.*', 'santris.id as santri_id', 'santris.nama_santri as nama_santri', 'wali_kelas.tahun_pelajaran as tahun_pelajaran', 'wali_kelas.semester as semester', 'wali_kelas.kelas as kelas')
                                            ->orderBy('wali_kelas_santris.jumlah_nilai', 'desc')
                                            ->orderBy('nama_santri', 'asc')
                                            ->get();

        $data = [];
        for($i=0; $i<count($wali_kelas_santri); $i++){

            // ambil data di table "tahun pelajaran" yang "tahun_pelajaran" sama dengan "$tahun_pelajaran" dan "semester" sama dengan "$semester" (ini untuk kebutuhan query nilai)
            $tahun_pelajaran = TahunPelajaran::where('tahun_pelajaran', $wali_kelas_santri[$i]->tahun_pelajaran)
                                            ->where('semester', $wali_kelas_santri[$i]->semester)
                                            ->first();

            // ambil data santri
            $data_santri = Santri::find($wali_kelas_santri[$i]->santri_id);

            // ambil data nilai
            $nilai = Nilai::where('tahun_pelajaran_id', $tahun_pelajaran->id)
                            ->where('santri_id', $data_santri->id)
                            ->get();

            $nilai_akhir = 0;
            for($j=0; $j<count($nilai); $j++){
                $nilai_akhir = $nilai_akhir + $nilai[$j]->nilai_akhir;
            }

            $data[] = [
                'id' => $wali_kelas_santri[$i]->id,
                'wali_kelas_id' => $wali_kelas_santri[$i]->wali_kelas_id,
                'santri_id' => $wali_kelas_santri[$i]->santri_id,
                'nama_santri' => $wali_kelas_santri[$i]->nama_santri,
                'kelakuan' => $wali_kelas_santri[$i]->kelakuan,
                'kerajinan' => $wali_kelas_santri[$i]->kerajinan,
                'kebersihan' => $wali_kelas_santri[$i]->kebersihan,
                'sakit' => $wali_kelas_santri[$i]->sakit,
                'izin' => $wali_kelas_santri[$i]->izin,
                'alpha' => $wali_kelas_santri[$i]->alpha,
                'catatan_wali_kelas' => $wali_kelas_santri[$i]->catatan_wali_kelas,
                'tahun_pelajaran' => $wali_kelas_santri[$i]->tahun_pelajaran,
                'semester' => $wali_kelas_santri[$i]->semester,
                'kelas' => $wali_kelas_santri[$i]->kelas,
                'nilai_akhir' => $nilai_akhir,
                'jumlah_nilai' => $wali_kelas_santri[$i]->jumlah_nilai,
                'created_at' => $wali_kelas_santri[$i]->created_at,
                'updated_at' => $wali_kelas_santri[$i]->updated_at,
            ];
        }

        return response()->json([
            'data' => $data,
        ]);
    }

    public function data_edit($id, $wali_kelas_santri_id, $santri, $tahun_pelajaran, $semester)
    {
        // ambil data di table "tahun pelajaran" yang "tahun_pelajaran" sama dengan "$tahun_pelajaran" dan "semester" sama dengan "$semester" (ini untuk kebutuhan query nilai)
        $tahun_pelajaran = TahunPelajaran::where('tahun_pelajaran', $tahun_pelajaran)
                                            ->where('semester', $semester)
                                            ->first();

        // ambil data santri
        $data_santri = Santri::find($santri);

        // ambil data nilai
        $nilai = Nilai::where('tahun_pelajaran_id', $tahun_pelajaran->id)
                        ->where('santri_id', $data_santri->id)
                        ->get();

        $nilai_akhir = 0;
        for($i=0; $i<count($nilai); $i++){
            $nilai_akhir = $nilai_akhir + $nilai[$i]->nilai_akhir;
        }

        // ambil data wali kelas santri
        $data = WaliKelasSantri::find($wali_kelas_santri_id);

        return response()->json([
            'nilai_akhir' => $nilai_akhir,
            'data' => $data
        ]);
    }

    public function update_wali_kelas_santri_admin($id)
    {
        $data = WaliKelasSantri::find($id);

        $data->update([
            'kelakuan' => request('kelakuan'),
            'kerajinan' => request('kerajinan'),
            'kebersihan' => request('kebersihan'),
            'sakit' => request('sakit'),
            'izin' => request('izin'),
            'alpha' => request('alpha'),
            'catatan_wali_kelas' => ucwords(request('catatan_wali_kelas')),
            'jumlah_nilai' => request('nilai_akhir'),
        ]);

        return response()->json([
            'message' => 'berhasil update wali kelas santri'
        ]);
    }

    public function nilai($id, $wali_kelas_santri_id, $santri, $tahun_pelajaran, $semester, $peringkat)
    {
        // ambil data di table "tahun pelajaran" yang "tahun_pelajaran" sama dengan "$tahun_pelajaran" dan "semester" sama dengan "$semester" (ini untuk kebutuhan query nilai)
        $tahun_pelajaran = TahunPelajaran::where('tahun_pelajaran', $tahun_pelajaran)
                                            ->where('semester', $semester)
                                            ->first();

        // ambil data santri
        $data_santri = Santri::find($santri);

        $nilai_data = Nilai::where('tahun_pelajaran_id', $tahun_pelajaran->id)
                        ->where('santri_id', $data_santri->id)
                        ->get();

        // cek rata-rata nilai
        $nilai = [];
        $r2 = 0;
        foreach($nilai_data as $n){
            $jumlah = 0;

            $nilai_rata_rata = Nilai::where('tahun_pelajaran_id', $tahun_pelajaran->id)
                                    ->where('mapel_ustad_id', $n->mapel_ustad_id)
                                    ->get();
            $nrr = 0;

            foreach($nilai_rata_rata as $nr){
                $nrr = ($nrr + $nr->nilai_akhir);
            }
            $c = round($nrr / count($nilai_rata_rata));

            $nilai[] = [
                'mata_pelajaran' => $n->mapel_ustad->mapel->nama_mata_pelajaran,
                'nilai_akhir' => $n->nilai_akhir,
                'nilai_akhir_terbilang' => terbilang($n->nilai_akhir),
                'rata_rata_nilai' => $c
            ];

            foreach($nilai as $ni){
                $jumlah = $jumlah + $ni['nilai_akhir'];
            }

            $r2 = round($jumlah / count($nilai));
        }

        // jumlah santri
        $jumlah_santri = WaliKelasSantri::where('wali_kelas_id', $id)
                                        ->count();

        // wali kelas santri yang dipilih
        $wali_kelas_santri = WaliKelasSantri::find($wali_kelas_santri_id);

        // ambil data wali kelas
        $wali_kelas = WaliKelas::find($id);

        // cek response yang diberikan
        // return [
        //     'route_id' => $id,
        //     'route_santri' => $santri,
        //     'route_tahun_pelajaran' => $tahun_pelajaran,
        //     'route_semester' => $semester,
        //     'santri' => $data_santri,
        //     'tahun_pelajaran' => $tahun_pelajaran,
        //     'wali_kelas' => $wali_kelas,
        //     'nilai' => $nilai,
        //     'peringkat' => $peringkat,
        //     'jumlah_santri' => $jumlah_santri,
        //     'wali_kelas_santri' => $wali_kelas_santri
        // ];

        // menggunakan halaman baru
        // return view('wali_kelas.nilai', [
        //     'route_id' => $id,
        //     'route_santri' => $santri,
        //     'route_tahun_pelajaran' => $tahun_pelajaran,
        //     'route_semester' => $semester,
        //     'santri' => $data_santri,
        //     'tahun_pelajaran' => $tahun_pelajaran,
        //     'wali_kelas' => $wali_kelas,
        //     'nilai' => $nilai,
        //     'peringkat' => $peringkat,
        //     'jumlah_santri' => $jumlah_santri,
        //     'wali_kelas_santri' => $wali_kelas_santri
        // ]);

        // menggunakan modal
        return response()->json([
            'route_id' => $id,
            'route_santri' => $santri,
            'route_tahun_pelajaran' => $tahun_pelajaran,
            'route_semester' => $semester,
            'santri' => $data_santri,
            'tahun_pelajaran' => $tahun_pelajaran,
            'wali_kelas' => $wali_kelas->ustad->nama_ustad,
            'nilai' => $nilai,
            'peringkat' => $peringkat,
            'jumlah_santri' => $jumlah_santri,
            'wali_kelas_santri' => $wali_kelas_santri,
            'tanggal' => date('d F Y'),
            'jumlah' => $jumlah,
            'jumlah_terbilang' => terbilang($jumlah),
            'rata_rata' => $r2,
            'rata_rata_terbilang' => terbilang($r2)
        ]);
    }

    public function print($id, $wali_kelas_santri_id, $santri, $tahun_pelajaran, $semester, $peringkat)
    {
        // ambil data di table "tahun pelajaran" yang "tahun_pelajaran" sama dengan "$tahun_pelajaran" dan "semester" sama dengan "$semester" (ini untuk kebutuhan query nilai)
        $tahun_pelajaran = TahunPelajaran::where('tahun_pelajaran', $tahun_pelajaran)
                                            ->where('semester', $semester)
                                            ->first();

        // ambil data santri
        $data_santri = Santri::find($santri);

        $nilai_data = Nilai::where('tahun_pelajaran_id', $tahun_pelajaran->id)
                        ->where('santri_id', $data_santri->id)
                        ->get();

        // cek rata-rata nilai
        $nilai = [];
        $r2 = 0;
        foreach($nilai_data as $n){
            $jumlah = 0;

            $nilai_rata_rata = Nilai::where('tahun_pelajaran_id', $tahun_pelajaran->id)
                                    ->where('mapel_ustad_id', $n->mapel_ustad_id)
                                    ->get();
            $nrr = 0;

            foreach($nilai_rata_rata as $nr){
                $nrr = ($nrr + $nr->nilai_akhir);
            }
            $c = round($nrr / count($nilai_rata_rata));

            $nilai[] = [
                'mata_pelajaran' => $n->mapel_ustad->mapel->nama_mata_pelajaran,
                'nilai_akhir' => $n->nilai_akhir,
                'nilai_akhir_terbilang' => terbilang($n->nilai_akhir),
                'rata_rata_nilai' => $c
            ];

            foreach($nilai as $ni){
                $jumlah = $jumlah + $ni['nilai_akhir'];
            }

            $r2 = round($jumlah / count($nilai));
        }

        // jumlah santri
        $jumlah_santri = WaliKelasSantri::where('wali_kelas_id', $id)
                                        ->count();

        // wali kelas santri yang dipilih
        $wali_kelas_santri = WaliKelasSantri::find($wali_kelas_santri_id);

        // ambil data wali kelas
        $wali_kelas = WaliKelas::find($id);

        // menggunakan halaman baru
        return view('admin.wali_kelas.print', [
            'route_id' => $id,
            'route_santri' => $santri,
            'route_tahun_pelajaran' => $tahun_pelajaran,
            'route_semester' => $semester,
            'santri' => $data_santri,
            'tahun_pelajaran' => $tahun_pelajaran,
            'wali_kelas' => $wali_kelas->ustad->nama_ustad,
            'nilai' => $nilai,
            'peringkat' => $peringkat,
            'jumlah_santri' => $jumlah_santri,
            'wali_kelas_santri' => $wali_kelas_santri,
            'tanggal' => date('d F Y'),
            'jumlah' => $jumlah,
            'jumlah_terbilang' => terbilang($jumlah),
            'rata_rata' => $r2,
            'rata_rata_terbilang' => terbilang($r2)
        ]);

    }
}