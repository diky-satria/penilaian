<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MapelUstad;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Santri;
use App\Models\TahunPelajaran;
use App\Models\Ustad;
use Illuminate\Http\Request;

class MapelUstadController extends Controller
{
    public function index($id)
    {
        $data = TahunPelajaran::find($id);
        $ustad = Ustad::orderBy('nama_ustad','asc')->get();
        $mapel = MataPelajaran::orderBy('nama_mata_pelajaran','asc')
                                ->get();

        $mapelUstad = MapelUstad::where('tahun_pelajaran_id', $id)
                                ->orderBy('created_at','desc')
                                ->get();
        if(request()->ajax()){  
            return datatables()->of($mapelUstad)
                                ->addColumn('ustad', function($mapelUstad){
                                    return $mapelUstad->ustad->nama_ustad;
                                })
                                ->addColumn('mapel', function($mapelUstad){
                                    return $mapelUstad->mapel->nama_mata_pelajaran;
                                })
                                ->addColumn('status', function($mapelUstad){
                                    if($mapelUstad->status == 0){
                                        $status = '<span class="badge bg-warning rounded-pill">Pending</span>';
                                    }else{
                                        $status = '<span class="badge bg-success rounded-pill">Berlangsung</span>';
                                    }
                                    return $status;
                                })
                                ->addColumn('action', function($mapelUstad){
                                    $button = '<a href="/tahun_pelajaran/'.$mapelUstad->tahun_pelajaran_id.'/mapel_ustad/'.$mapelUstad->id.'/detail" class="btn btn-sm btn-info ms-1">Detail</a>';
                                    $button .= '<button class="btn btn-sm btn-success ms-1 edit-mapel-ustad" data-bs-toggle="modal" data-bs-target="#staticBackdropEdit" id="'.$mapelUstad->id.'" thn_pel_id="'.$mapelUstad->tahun_pelajaran_id.'">Edit</button>';
                                    if($mapelUstad->status == 0){
                                        $button .= "<button class='btn btn-sm btn-primary ms-1 publish-mapel-ustad' id_thn_pelajaran='".$mapelUstad->tahun_pelajaran_id."' id_mapel_ustad='".$mapelUstad->id."'>Publish</button>";
                                        $button .= "<button class='btn btn-sm btn-danger ms-1 hapus-mapel-ustad' id_thn_pelajaran='".$mapelUstad->tahun_pelajaran_id."' id_mapel_ustad='".$mapelUstad->id."'>Hapus</button>";
                                    }
                                    return $button;
                                })
                                ->rawColumns(['ustad','mapel','status','action'])
                                ->make(true);
        }

        return view('admin.mapel_ustad.mapel_ustad', [
            'data' => $data,
            'ustad' => $ustad,
            'mapel' => $mapel
        ]);
    }
 
    public function store()
    {
        request()->validate([ 
            'ustad' => 'required',
            'mapel' => 'required',
            'kelas' => 'required'
        ],[
            'ustad.required' => 'Guru harus dipilih',
            'mapel.required' => 'Mata pelajaran harus dipilih',
            'kelas.required' => 'Kelas harus dipilih',
        ]);

        $cekDuplikat = MapelUstad::where('tahun_pelajaran_id', request('tahun_pelajaran_id'))
                                    ->where('ustad_id', request('ustad'))
                                    ->where('mapel_id', request('mapel'))
                                    ->where('kelas', request('kelas'))
                                    ->get();
        
        if(count($cekDuplikat) > 0){
            return response()->json([
                'duplikat' => 'Guru, mapel dan kelas ini sudah terdaftar'
            ]);
        }else{
            $cekSantri = Santri::where('kelas', request('kelas'))->get();
            if(count($cekSantri) <= 0){
                return response()->json([
                    'duplikat' => 'Santri di kelas ini masih kosong, silahkan input data santri !'
                ]);
            }else{
                $cekDuplikat2 = MapelUstad::where('tahun_pelajaran_id', request('tahun_pelajaran_id'))
                                    ->where('mapel_id', request('mapel'))
                                    ->where('kelas', request('kelas'))
                                    ->get();

                if(count($cekDuplikat2) > 0){
                    return response()->json([
                        'duplikat' => 'Mapel dan kelas ini sudah terdaftar'
                    ]);
                }else{
                    $mapelUstad = MapelUstad::create([
                        'tahun_pelajaran_id' => request('tahun_pelajaran_id'),
                        'ustad_id' => request('ustad'),
                        'mapel_id' => request('mapel'),
                        'kelas' => request('kelas')
                    ]);
        
                    $santri = Santri::where('kelas', request('kelas'))->get();
        
                    for($i=0; $i<count($santri); $i++){
                        Nilai::create([
                            'tahun_pelajaran_id' => request('tahun_pelajaran_id'),
                            'mapel_ustad_id' => $mapelUstad->id,
                            'santri_id' => $santri[$i]->id
                        ]);
                    }
            
                    return response()->json([
                        'message' => 'mapel ustad berhasil ditambahkan'
                    ]);
                }
            }
        }
    }

    public function edit($tp_id, $id)
    {
        $mapelUstad = MapelUstad::find($id);

        return response()->json([
            'data' => $mapelUstad
        ]);
    }

    public function update($tp_id, $id)
    {
        $mapelUstad = MapelUstad::find($id);

        $cekDuplikat = MapelUstad::where('tahun_pelajaran_id', request('id_thn_pel_edit'))
                                    ->where('ustad_id', request('ustad-edit'))
                                    ->where('mapel_id', request('mapel-edit'))
                                    ->where('kelas', request('kelas-edit'))
                                    ->get();

        if(request('ustad-edit') !== request('ustad-edit-old') && count($cekDuplikat) > 0 || request('mapel-edit') !== request('mapel-edit-old') && count($cekDuplikat) > 0 || request('kelas-edit') !== request('kelas-edit-old') && count($cekDuplikat) > 0){

            return response()->json([
                'duplikat' => 'Guru, mapel dan kelas ini sudah terdaftar'
            ]);
        }else{

            $cekDuplikat2 = MapelUstad::where('tahun_pelajaran_id', request('id_thn_pel_edit'))
                                    ->where('mapel_id', request('mapel-edit'))
                                    ->where('kelas', request('kelas-edit'))
                                    ->get();
            if(request('mapel-edit') !== request('mapel-edit-old') && count($cekDuplikat2) > 0 || request('kelas-edit') !== request('kelas-edit-old') && count($cekDuplikat2) > 0){
                return response()->json([
                    'duplikat' => 'Mapel dan kelas ini sudah terdaftar'
                ]);
            }else{
                $mapelUstad->update([
                    'ustad_id' => request('ustad-edit'),
                    'mapel_id' => request('mapel-edit')
                ]);
        
                return response()->json([
                    'message' => 'mapel ustad berhasil diedit'
                ]);
            }

        }
    }

    public function publish($tp_id, $id)
    {
        $mp_ustad = MapelUstad::find($id);
        $updated = $mp_ustad->update([
            'status' => 1
        ]);

        return response()->json([
            'message' => 'mapel ustad berhasil di publish'
        ]);
    }

    public function delete($tp_id, $id)
    {
        $nilai = Nilai::where('tahun_pelajaran_id', $tp_id)
                        ->where('mapel_ustad_id', $id);
        $nilai->delete();

        $mp_ustad = MapelUstad::find($id);
        $mp_ustad->delete();

        return response()->json([
            'message' => 'siswa di table nilai berhasil dihapus, mapel ustad berhasil dihapus'
        ]);
    }
}
