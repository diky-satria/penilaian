<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\MapelUstad;
use App\Models\Nilai;
use App\Models\Santri;
use App\Models\TahunPelajaran;
use App\Models\Ustad;
use App\Models\WaliKelas;
use App\Models\WaliKelasSantri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KelasKuController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $ustad = Ustad::where('user_id', $user->id)
                        ->first();
        $wali_kelas = WaliKelas::where('ustad_id', $ustad->id)
                                ->get();

        if(request()->ajax()){  
            return datatables()->of($wali_kelas)
                                // ->addColumn('status', function($wali_kelas){
                                //     return $wali_kelas->status;
                                // })
                                ->addColumn('action', function($wali_kelas){
                                    $button = "<a href='/kelasku/".$wali_kelas->id."/santri_client' class='btn btn-sm btn-info ms-1'>Santri</a>";
                                    return $button;
                                })
                                ->rawColumns(['status','action'])
                                ->make(true);
        }

        return view('wali_kelas.kelasku');
    }











    // santri menggunakan datatable server side
    public function santri($id)
    {
        $wali_kelas_santri = WaliKelasSantri::where('wali_kelas_id', $id)
                                            ->get();
        if(request()->ajax()){  
            return datatables()->of($wali_kelas_santri)
                                ->addColumn('santri', function($wali_kelas_santri){
                                    return $wali_kelas_santri->santri->nama_santri;
                                })
                                ->addColumn('kelakuan', function($wali_kelas_santri){
                                    if($wali_kelas_santri->kelakuan == null){
                                        return '<div>-</div>';
                                    }else{
                                        return $wali_kelas_santri->kelakuan;
                                    }
                                })
                                ->addColumn('kerajinan', function($wali_kelas_santri){
                                    if($wali_kelas_santri->kerajinan == null){
                                        return '<div>-</div>';
                                    }else{
                                        return $wali_kelas_santri->kerajinan;
                                    }
                                })
                                ->addColumn('kebersihan', function($wali_kelas_santri){
                                    if($wali_kelas_santri->kebersihan == null){
                                        return '<div>-</div>';
                                    }else{
                                        return $wali_kelas_santri->kebersihan;
                                    }
                                })
                                ->addColumn('action', function($wali_kelas_santri){
                                    $button = "<a href='/kelasku/".$wali_kelas_santri->wali_kelas_id."/santri/".$wali_kelas_santri->santri_id."/tp/".$wali_kelas_santri->wali_kelas->tahun_pelajaran."/sm/".$wali_kelas_santri->wali_kelas->semester."/kl/".$wali_kelas_santri->wali_kelas->kelas."' class='btn btn-sm btn-info ms-1'>Nilai</a>";
                                    return $button;
                                })
                                ->rawColumns(['kelakuan','kerajinan','kebersihan','santri','action'])
                                ->make(true);
        }

        // /kelasku/".$wali_kelas_santri->id."/santri
        return view('wali_kelas.santri', [
            'route_id' => $id
        ]);
    }

    // santri menggunakan datatable client side
    public function santri_client($id)
    {
        $wali_kelas = WaliKelas::find($id);

        if(!$wali_kelas || Auth::user()->id !== $wali_kelas->ustad->user->id){
            return redirect('/kelasku');
        }

        return view('wali_kelas.santri_client', [
            'route_id' => $id
        ]); 
    }

    public function ambil_data($id)
    {
        $wali_kelas = WaliKelas::find($id);

        if(!$wali_kelas || Auth::user()->id !== $wali_kelas->ustad->user->id){
            return redirect('/kelasku');
        }

        $wali_kelas_santri = WaliKelasSantri::join('santris', 'santris.id', '=', 'wali_kelas_santris.santri_id')
                                            ->join('wali_kelas', 'wali_kelas.id', '=', 'wali_kelas_santris.wali_kelas_id')
                                            ->where('wali_kelas_id', $wali_kelas->id)
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

    public function update_wali_kelas_santri($id)
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
            'wali_kelas' => $wali_kelas,
            'nilai' => $nilai,
            'peringkat' => $peringkat,
            'jumlah_santri' => $jumlah_santri,
            'wali_kelas_santri' => $wali_kelas_santri,
            'tanggal' => date('d F Y'),
            'jumlah' => $jumlah,
            'jumlah_terbilang' => terbilang($jumlah),
            'rata_rata' => $r2,
            'rata_rata_terbilang' => terbilang($r2),
            'wali_kelas' => Auth::user()->name
        ]);
    }
}
