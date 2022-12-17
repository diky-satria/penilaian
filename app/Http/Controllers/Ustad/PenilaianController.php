<?php

namespace App\Http\Controllers\Ustad;

use App\Http\Controllers\Controller;
use App\Models\MapelUstad;
use App\Models\Ustad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenilaianController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $ustad = Ustad::where('user_id', $user->id)->first();
        $mapel_ustad = MapelUstad::where('ustad_id', $ustad->id)
                                ->where('status', 1)
                                ->get();
        
        if(request()->ajax()){  
            return datatables()->of($mapel_ustad)
                                ->addColumn('tahun_pelajaran', function($mapel_ustad){
                                    return $mapel_ustad->tahun_pelajaran->tahun_pelajaran;
                                })
                                ->addColumn('semester', function($mapel_ustad){
                                    if($mapel_ustad->tahun_pelajaran->semester == 'genap'){
                                        $badge = '<span class="badge rounded-pill bg-primary">'.$mapel_ustad->tahun_pelajaran->semester.'</span>';
                                    }else{
                                        $badge = '<span class="badge rounded-pill bg-success">'.$mapel_ustad->tahun_pelajaran->semester.'</span>';
                                    }
                                    return $badge;
                                })
                                ->addColumn('mapel', function($mapel_ustad){
                                    return $mapel_ustad->mapel->nama_mata_pelajaran;
                                })
                                ->addColumn('action', function($mapel_ustad){
                                    $button = "<a href='/penilaian/tahun_pelajaran_id/".$mapel_ustad->tahun_pelajaran_id."/mapel_ustad_id/".$mapel_ustad->id."/nilai_santri' class='btn btn-sm btn-info ms-1'>Nilai Santri</a>";
                                    return $button;
                                })
                                ->rawColumns(['tahun_pelajaran','semester','mapel','action'])
                                ->make(true);
        }

        return view('ustad.penilaian');
    }
}
