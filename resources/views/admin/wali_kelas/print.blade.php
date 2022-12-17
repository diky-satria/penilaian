@extends('layout/print_template')

@push('js')
<script>
    window.print()
</script>
@endpush

@section('konten')

<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-12">
            <div class="row">
                <table class="table table-sm" style="font-size:14px;border:1px solid transparent;" id="tab-santri">
                    <thead>
                        <tr>
                            <td>Nama MDT</td>
                            <td>:</td>
                            <td><b>BAHRUL MAGHFIROH</b></td>
                            <td>Kelas</td>
                            <td>:</td>
                            <td>{{ $santri->kelas }}</td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>:</td>
                            <td>Jl. Tegal Rotan No. 72</td>
                            <td>Semester</td>
                            <td>:</td>
                            <td>{{ $tahun_pelajaran->semester }}</td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                            <td>Sawah Baru, Ciputat, Tangsel</td>
                            <td>Tahun Pelajaran</td>
                            <td>:</td>
                            <td>{{ $tahun_pelajaran->tahun_pelajaran }}</td>
                        </tr>
                        <tr>
                            <td>Nama Murid</td>
                            <td>:</td>
                            <td><b>{{ $santri->nama_santri }}</b></td>
                            <td>Nomor Induk</td>
                            <td>:</td>
                            <td>{{ $santri->nisn }}</td>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="row mb-2">
                <table class="table table-sm table-bordered" style="font-size:14px;">
                    <tr style="text-align:center;">
                        <th rowspan="2">No</th>
                        <th rowspan="2">Mata Pelajaran</th>
                        <th colspan="2">Nilai Prestasi</th>
                        <th rowspan="2" style="width:10%;">Rata-rata Angka</th>
                    </tr>
                    <tr style="text-align:center;">
                        <th style="width:10%;">Angka</th>
                        <th>Huruf</th>
                    </tr> 

                    <?php
                        $jumlah = 0;
                        $looping = 0;
                    ?>
                    @foreach($nilai as $n)
                    <tr>
                        <td style="text-align:center;">{{ $loop->iteration }}</td>
                        <td>{{ $n['mata_pelajaran'] }}</td>
                        <td style="text-align:center;">{{ $n['nilai_akhir'] }}</td>
                        <td style="text-align:center;"><i>{{ terbilang($n['nilai_akhir']) }}</i></td>
                        <td style="text-align:center;">{{ $n['rata_rata_nilai'] }}</td>
                    </tr>

                    <?php 
                        $jumlah = $jumlah + $n['nilai_akhir'];
                        $looping = $looping+1;
                    ?>
                    @endforeach
                    <tr>
                        <td><div style="color:transparent;">-</div></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th colspan="2" style="text-align:center;width:40%;">Jumlah</th>
                        <th style="text-align:center;">{{ $jumlah }}</th>
                        <th colspan="2" style="text-align:center;">
                            <i>
                                @if($looping > 0)
                                    {{ terbilang($jumlah) }}
                                @else
                                    Kosong
                                @endif
                            </i>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="2" style="text-align:center;">Rata-rata</th>
                        @if($looping > 0)
                        <th style="text-align:center;">{{ round($jumlah / $looping) }}</th>
                        @else
                        <th style="text-align:center;">0</th>
                        @endif

                        
                        <th colspan="2" style="text-align:center;">
                            <i>
                                @if($looping > 0)
                                    {{ terbilang(round($jumlah / $looping)) }}
                                @else
                                    Kosong
                                @endif
                            </i>
                        </th>
                        
                    </tr>
                    <tr>
                        <td colspan="2">Peringkat kelas ke :</td>
                        <td style="text-align:center;border-left:1px solid transparent;"><b>{{ $peringkat }}</b></td>
                        <td colspan="2">
                            <div style="display:flex;justify-content:space-evenly;width:100%;">
                                <div>Dari</div>
                                <div>{{ $jumlah_santri }}</div>
                                <div>Murid</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="row mb-2">
                <table class="table table-sm table-bordered" style="font-size:14px;">
                    <tr>
                        <td></td>
                        <td style="width:30%;"></td>
                        <td style="text-align:center;">Nilai</td>
                    </tr>
                    <tr>
                        <td rowspan="4" style="width:40%;">Kepribadian</td>
                    </tr>
                    <tr>
                        <td>1. Kelakuan</td>
                        <td style="text-align:center;">
                            @if($wali_kelas_santri->kelakuan == NULL)
                                <div>-</div>
                            @else
                                {{ $wali_kelas_santri->kelakuan }}
                            @endif    
                        </td>
                    </tr>
                    <tr>
                        <td>2. Kerajinan</td>
                        <td style="text-align:center;">
                            @if($wali_kelas_santri->kerajinan == NULL)
                                <div>-</div>
                            @else
                                {{ $wali_kelas_santri->kerajinan }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>3. Kebersihan</td>
                        <td style="text-align:center;">
                            @if($wali_kelas_santri->kebersihan == NULL)
                                <div>-</div>
                            @else
                                {{ $wali_kelas_santri->kebersihan }}
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            <div class="row mb-2">
                <table class="table table-sm table-bordered" style="font-size:14px;">
                    <tr>
                        <td rowspan="4" style="width:40%;">Ketidakhadiran</td>
                    </tr>
                    <tr>
                        <td style="width:30%;">1. Sakit</td>
                        <td style="text-align:center;">{{ $wali_kelas_santri->sakit }}</td>
                    </tr>
                    <tr>
                        <td>2. Izin</td>
                        <td style="text-align:center;">{{ $wali_kelas_santri->izin }}</td>
                    </tr>
                    <tr>
                        <td>3. Alpha</td>
                        <td style="text-align:center;">{{ $wali_kelas_santri->alpha }}</td>
                    </tr>
                    <tr>
                        <td colspan="3"><b><i>Catatan Wali Santri : {{ $wali_kelas_santri->catatan_wali_kelas }}</i></b></td>
                    </tr>
                </table>
            </div>
            <div class="row mb-2">
                <table class="table table-sm table-bordered" style="font-size:14px;border:1px solid transparent;">
                    <tr>
                        <td style="width:33%;"></td>
                        <td style="width:33%;"></td>
                        <td style="width:34%;">Diberikan di : Tangerang Selatan</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>Tanggal : {{ date('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td>Mengetahui,</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Orang Tua / Wali</td>
                        <td>Wali Kelas</td>
                        <td>Kepala MDT</td>
                    </tr>
                    <tr style="height:100px;">
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><b><u>{{ $santri->wali }}</u></b></td>
                        <td><b><u>{{ $wali_kelas }}</u></b></td>
                        <td><b><u>Ust. H. Ahmad Ghozali</u></b></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection