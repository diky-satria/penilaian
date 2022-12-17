<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
    use HasFactory;

    protected $fillable = ['nisn','nama_santri','kelas','wali','telepon_wali','jenis_kelamin','alamat'];
}
