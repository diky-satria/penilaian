<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use Illuminate\Http\Request;

class SantriController extends Controller
{
    public function index()
    {
        $santri = Santri::orderBy('id','desc')->get();

        if(request()->ajax()){  
            return datatables()->of($santri)
                                ->addColumn('jenis_kelamin', function($santri){
                                    if($santri->jenis_kelamin == 'L'){
                                        $jk = 'Laki-laki';
                                    }else{
                                        $jk = 'Perempuan';
                                    }
                                    return $jk;
                                })
                                ->addColumn('action', function($santri){
                                    $button = "<a href='/santri/edit/".$santri->id."' class='btn btn-sm btn-success ms-1'>Edit</a>";
                                   $button .= "<button class='btn btn-sm btn-danger ms-1 hapus-siswa' id='".$santri->id."'>Hapus</button>";
                                    return $button;
                                })
                                ->rawColumns(['jenis_kelamin','action'])
                                ->make(true);
        }
        return view('admin.santri.santri');
    }

    public function add()
    {
        return view('admin.santri.tambah');
    } 

    public function store()
    {
        request()->validate([
            'nisn' => 'required|unique:santris,nisn',
            'nama_siswa' => 'required',
            'kelas' => 'required',
            'jenis_kelamin' => 'required',
            'nama_wali' => 'required',
            'telepon_wali' => 'required|numeric',
            'alamat' => 'required',
        ],[
            'nisn.required' => 'NISN harus di isi',
            'nisn.unique' => 'NISN sudah terdaftar',
            'nama_siswa.required' => 'Nama santri harus di isi',
            'kelas.required' => 'Kelas harus di pilih',
            'jenis_kelamin.required' => 'Jenis kelamin harus dipilih',
            'nama_wali.required' => 'Nama wali harus di isi',
            'telepon_wali.required' => 'Telpon wali harus di isi',
            'telepon_wali.numeric' => 'Telpon wali harus angka',
            'alamat.required' => 'Alamat harus di isi',
        ]);

        Santri::create([
            'nisn' => strtoupper(request('nisn')),
            'nama_santri' => ucwords(request('nama_siswa')),
            'kelas' => request('kelas'),
            'jenis_kelamin' => request('jenis_kelamin'),
            'wali' => ucwords(request('nama_wali')),
            'telepon_wali' => request('telepon_wali'),
            'alamat' => ucwords(request('alamat')),
        ]);

        return response()->json([
            'message' => 'santri berhasil ditambahkan'
        ]);
    }

    public function edit($id)
    {
        $santri = Santri::find($id);

        return view('admin.santri.edit', [
            'santri' => $santri
        ]);
    }

    public function update($id)
    {
        $santri = Santri::find($id);

        request()->validate([
            'nisn-edit' => 'required|unique:santris,nisn,'.$santri->id,
            'nama-siswa-edit' => 'required',
            'nama-wali-edit' => 'required',
            'telepon_wali-edit' => 'required|numeric',
            'alamat-edit' => 'required',
        ],[
            'nisn-edit.required' => 'NISN harus di isi',
            'nisn-edit.unique' => 'NISN sudah terdaftar',
            'nama-siswa-edit.required' => 'Nama siswa harus di isi',
            'nama-wali-edit.required' => 'Nama wali harus di isi',
            'telepon_wali-edit.required' => 'Telepon wali harus di isi',
            'telepon_wali-edit.numeric' => 'Telpon wali harus angka',
            'alamat-edit.required' => 'Alamat harus di isi'
        ]);

        $santri->update([
            'nisn' => strtoupper(request('nisn-edit')),
            'nama_santri' => ucwords(request('nama-siswa-edit')),
            'kelas' => request('kelas-edit'),
            'jenis_kelamin' => request('jenis_kelamin-edit'),
            'wali' => ucwords(request('nama-wali-edit')),
            'telepon_wali' => request('telepon_wali-edit'),
            'alamat' => ucwords(request('alamat-edit')),
        ]);

        return response()->json([
            'message' => 'santri berhasil di edit'
        ]);
    }

    public function delete($id)
    {
        $santri = Santri::find($id);

        $santri->delete();

        return response()->json([
            'message' => 'santri berhasil di hapus'
        ]);
    }
}
