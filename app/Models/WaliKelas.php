<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaliKelas extends Model
{
    use HasFactory;

    protected $fillable = ['ustad_id','tahun_pelajaran','semester','kelas','status'];

    public function ustad()
    {
        return $this->belongsTo('App\Models\Ustad', 'ustad_id', 'id');
    }
}
