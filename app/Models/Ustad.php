<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ustad extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','nip','nama_ustad','jenis_kelamin','telepon','alamat'];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
