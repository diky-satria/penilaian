<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    use HasFactory;

    protected $fillable = ['tahun_pelajaran_id','mapel_ustad_id','santri_id','n1','n2','n3','n4','n5','n6','rata_rata_n','uas','nilai_akhir'];

    public function santri()
    {
        return $this->belongsTo('App\Models\Santri', 'santri_id', 'id');
    }

    public function mapel_ustad()
    {
        return $this->belongsTo('App\Models\MapelUstad', 'mapel_ustad_id', 'id');
    }
}
