<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSantrisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('santris', function (Blueprint $table) {
            $table->id();
            $table->string('nisn');
            $table->string('nama_santri');
            $table->string('jenis_kelamin');
            $table->string('telepon_wali');
            $table->text('alamat');
            $table->integer('kelas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('santris');
    }
}
