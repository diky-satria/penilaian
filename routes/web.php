<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MapelUstadController;
use App\Http\Controllers\Admin\MataPelajaranController;
use App\Http\Controllers\Admin\NilaiController;
use App\Http\Controllers\Admin\SantriController;
use App\Http\Controllers\Admin\TahunPelajaranController;
use App\Http\Controllers\Admin\UstadController;
use App\Http\Controllers\Admin\WaliKelasController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UbahPasswordController;
use App\Http\Controllers\Ustad\NilaiSantriController;
use App\Http\Controllers\Ustad\PenilaianController;
use App\Http\Controllers\WaliKelas\KelasKuController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['logged']], function(){
    Route::get('/', [AuthController::class, 'login'])->name('login');
    Route::post('/store', [AuthController::class, 'store']);
});

Route::group(['middleware' => ['auth']], function(){

    // access untuk admin
    Route::group(['middleware' => ['accessAdmin']], function(){
        Route::get('/dashboard', [DashboardController::class, 'index']);
        
        Route::get('/ustad', [UstadController::class, 'index']);
        Route::get('/ustad/tambah', [UstadController::class, 'add']);
        Route::post('/ustad/tambah', [UstadController::class, 'store']);
        Route::get('/ustad/edit/{id}', [UstadController::class, 'edit']);
        Route::post('/ustad/update/{id}', [UstadController::class, 'update']);
        Route::delete('/ustad/delete/{id}', [UstadController::class, 'delete']);
        
        Route::get('/santri', [SantriController::class, 'index']);
        Route::get('/santri/tambah', [SantriController::class, 'add']);
        Route::post('/santri/tambah', [SantriController::class, 'store']);
        Route::get('/santri/edit/{id}', [SantriController::class, 'edit']);
        Route::post('/santri/update/{id}', [SantriController::class, 'update']);
        Route::delete('/santri/delete/{id}', [SantriController::class, 'delete']);
        
        Route::get('/mata_pelajaran', [MataPelajaranController::class, 'index']);
        Route::get('/mata_pelajaran/ambilData', [MataPelajaranController::class, 'ambilData']);
        Route::get('/mata_pelajaran/detail/{id}', [MataPelajaranController::class, 'show']);
        Route::post('/mata_pelajaran/tambah', [MataPelajaranController::class, 'store']);
        Route::post('/mata_pelajaran/edit/{id}', [MataPelajaranController::class, 'update']);
        Route::delete('/mata_pelajaran/hapus/{id}', [MataPelajaranController::class, 'delete']);
        
        Route::get('/tahun_pelajaran', [TahunPelajaranController::class, 'index']);
        Route::post('/tahun_pelajaran/tambah', [TahunPelajaranController::class, 'store']);
        Route::get('/tahun_pelajaran/edit/{id}', [TahunPelajaranController::class, 'edit']);
        Route::post('/tahun_pelajaran/update/{id}', [TahunPelajaranController::class, 'update']);
        
        Route::get('/tahun_pelajaran/{id}/mapel_ustad', [MapelUstadController::class, 'index']);
        Route::post('/tahun_pelajaran/{id}/mapel_ustad/tambah', [MapelUstadController::class, 'store']);
        Route::get('/tahun_pelajaran/{tp_id}/mapel_ustad/{id}/edit', [MapelUstadController::class, 'edit']);
        Route::post('/tahun_pelajaran/{tp_id}/mapel_ustad/{id}/update', [MapelUstadController::class, 'update']);
        Route::post('/tahun_pelajaran/{tp_id}/mapel_ustad/{id}/publish', [MapelUstadController::class, 'publish']);
        Route::delete('/tahun_pelajaran/{tp_id}/mapel_ustad/{id}/hapus', [MapelUstadController::class, 'delete']);
        
        Route::get('/tahun_pelajaran/{tp_id}/mapel_ustad/{id}/detail', [NilaiController::class, 'index']);
        Route::get('/tahun_pelajaran/{tp_id}/mapel_ustad/{id_mp_ust}/detail/{id_nilai}/edit', [NilaiController::class, 'edit']);
        Route::post('/tahun_pelajaran/{tp_id}/mapel_ustad/{id_mp_ust}/detail/{id_nilai}/update', [NilaiController::class, 'update']);

        Route::get('/wali_kelas', [WaliKelasController::class, 'index']);
        Route::get('/wali_kelas/{id}', [WaliKelasController::class, 'show']);
        Route::post('/wali_kelas/tambah', [WaliKelasController::class, 'store']);
        Route::post('/wali_kelas/edit/{id}', [WaliKelasController::class, 'update']);
        Route::post('/wali_kelas/aktifkan/{id}/{id_ustad}', [WaliKelasController::class, 'aktifkan']);
        Route::post('/wali_kelas/nonaktifkan/{id}', [WaliKelasController::class, 'non_aktifkan']);

        Route::get('/wali_kelas/{wali_kelas_id}/santri', [WaliKelasController::class, 'wali_kelas_santri']);
        Route::get('/wali_kelas/{wali_kelas_id}/ambil_data', [WaliKelasController::class, 'ambil_data']);
        Route::get('/wali_kelas/{id}/wali_kelas_santri/{wali_kelas_santri_id}/santri_client/{santri}/tp/{tahun_pelajaran}/sm/{semester}/data_edit', [WaliKelasController::class, 'data_edit'])->where('tahun_pelajaran', '(.*)'); 
        Route::post('/wali_kelas_santri_admin/{id}', [WaliKelasController::class, 'update_wali_kelas_santri_admin']);
        Route::get('/wali_kelas/{id}/wali_kelas_santri/{wali_kelas_santri_id}/santri_client/{santri}/tp/{tahun_pelajaran}/sm/{semester}/peringkat/{peringkat}', [WaliKelasController::class, 'nilai'])->where('tahun_pelajaran', '(.*)');
        Route::get('/wali_kelas/{id}/wali_kelas_santri/{wali_kelas_santri_id}/santri_client/{santri}/tp/{tahun_pelajaran}/sm/{semester}/peringkat/{peringkat}/print', [WaliKelasController::class, 'print'])->where('tahun_pelajaran', '(.*)');
    });
    
    // access untuk ustad dan wali kelas
    Route::group(['middleware' => ['accessUstadDanWaliKelas']], function(){
        Route::get('/penilaian', [PenilaianController::class, 'index']);

        Route::get('/penilaian/tahun_pelajaran_id/{tahun_pelajaran_id}/mapel_ustad_id/{mapel_ustad_id}/nilai_santri', [NilaiSantriController::class, 'index']);
        Route::get('/penilaian/tahun_pelajaran_id/{id_thn_pelajaran}/mapel_ustad_id/{id_mapel_ustad}/nilai_santri/{id_nilai}/detail', [NilaiSantriController::class, 'detail']);
        Route::post('/penilaian/tahun_pelajaran_id/{id_thn_pelajaran}/mapel_ustad_id/{id_mapel_ustad}/nilai_santri/{id_nilai}/update', [NilaiSantriController::class, 'update']);
        


        // access untuk wali kelas
        Route::group(['middleware' => ['accessWaliKelas']], function(){
            Route::get('/kelasku', [KelasKuController::class, 'index']); 


            // santri datatable server side
            // Route::get('/kelasku/{id}/santri', [KelasKuController::class, 'santri']); 
            // Route::get('/kelasku/{id}/santri/{santri}/tp/{tahun_pelajaran?}/sm/{semester}/kl/{kelas}', [KelasKuController::class, 'nilai'])->where('tahun_pelajaran', '(.*)'); 

            
            // santri datatable client side
            Route::get('/kelasku/{id}/santri_client', [KelasKuController::class, 'santri_client']); 
            Route::get('/kelasku/{id}/ambil_data', [KelasKuController::class, 'ambil_data']); 
            Route::get('/kelasku/{id}/wali_kelas_santri/{wali_kelas_santri_id}/santri_client/{santri}/tp/{tahun_pelajaran}/sm/{semester}/data_edit', [KelasKuController::class, 'data_edit'])->where('tahun_pelajaran', '(.*)'); 

            Route::post('/wali_kelas_santri/{id}', [KelasKuController::class, 'update_wali_kelas_santri']);

            Route::get('/kelasku/{id}/wali_kelas_santri/{wali_kelas_santri_id}/santri_client/{santri}/tp/{tahun_pelajaran}/sm/{semester}/peringkat/{peringkat}', [KelasKuController::class, 'nilai'])->where('tahun_pelajaran', '(.*)'); 

        });
    });

    
    Route::get('/ubah_password', [UbahPasswordController::class, 'index']);
    Route::post('/ubah_password', [UbahPasswordController::class, 'ubah_password']);

    Route::get('/logout', [AuthController::class, 'logout']);
});


// Route::get('/pilih', function () {
//     return view('layout.pilih', [
//         'data1' => 22,
//         'data2' => 28
//     ]);
// });