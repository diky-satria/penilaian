<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMapelUstadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mapel_ustads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_pelajaran_id');
            $table->foreign('tahun_pelajaran_id')->references('id')->on('tahun_pelajarans');
            $table->foreignId('ustad_id');
            $table->foreign('ustad_id')->references('id')->on('ustads');
            $table->foreignId('mapel_id');
            $table->foreign('mapel_id')->references('id')->on('mata_pelajarans');
            $table->integer('kelas');
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('mapel_ustads');
    }
}
