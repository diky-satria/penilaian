<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaliKelasSantri extends Model
{
    use HasFactory;

    protected $fillable = ['wali_kelas_id','santri_id','kelakuan','kerajinan','kebersihan','sakit','izin','alpha','jumlah_nilai','catatan_wali_kelas'];

    public function santri()
    {
        return $this->belongsTo('App\Models\Santri', 'santri_id', 'id');
    }

    public function wali_kelas()
    {
        return $this->belongsTo('App\Models\WaliKelas', 'wali_kelas_id', 'id');
    }
}
