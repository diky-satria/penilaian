<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapelUstad extends Model
{
    use HasFactory;

    protected $fillable = ['tahun_pelajaran_id','ustad_id','mapel_id','kelas','status'];

    public function ustad()
    {
        return $this->belongsTo('App\Models\Ustad', 'ustad_id', 'id');
    }

    public function mapel()
    {
        return $this->belongsTo('App\Models\MataPelajaran', 'mapel_id', 'id');
    }

    public function tahun_pelajaran()
    {
        return $this->belongsTo('App\Models\TahunPelajaran', 'tahun_pelajaran_id', 'id');
    }
}
