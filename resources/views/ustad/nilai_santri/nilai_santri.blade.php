@extends('layout/dashboard')

@section('konten')
<input type="hidden" id="tahun_pelajaran_id" value="{{ $mapel_ustad->tahun_pelajaran_id }}">
<input type="hidden" id="mapel_ustad_id" value="{{ $mapel_ustad->id }}">

<div class="container-fluid px-4">
    <div class="content-top">
    <table>
            <tr>
                <td>Tahun pelajaran</td>
                <td>:</td>
                <td>{{ $mapel_ustad->tahun_pelajaran->tahun_pelajaran }}</td>
            </tr>
            <tr>
                <td>Semester</td>
                <td>:</td>
                <td>{{ $mapel_ustad->tahun_pelajaran->semester }}</td>
            </tr>
            <tr>
                <td>Ustad</td>
                <td>:</td>
                <td>{{ $mapel_ustad->ustad->nama_ustad }}</td>
            </tr>
            <tr>
                <td>Mapel</td>
                <td>:</td>
                <td>{{ $mapel_ustad->mapel->nama_mata_pelajaran }}</td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>:</td>
                <td>{{ $mapel_ustad->kelas }}</td>
            </tr>
            <tr style="font-weight:bold;">
                <td>NH</td>
                <td>:</td>
                <td>Nilai Harian</td>
            </tr>
        </table>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="/tahun_pelajaran">penilaian</a></li>
            <li class="breadcrumb-item active"><a href="/penilaian/tahun_pelajaran_id/{{ $mapel_ustad->tahun_pelajaran_id }}/mapel_ustad_id/{{ $mapel_ustad->id }}/nilai_santri">Detail</a></li>
        </ol>
    </div> 
    <div class="row" style="padding:0 10px;">
        <div class="col-12 col-md-12 bg-table">
            <div class="row">
                <table class="table" style="font-size:14px;" id="tab-nilai-santri">
                    <thead>
                        <tr style="border-bottom:2px solid #5034FF;">
                            <th>No</th>
                            <th>Santri</th>
                            <th>NH 1</th>
                            <th>NH 2</th>
                            <th>NH 3</th>
                            <th>NH 4</th>
                            <th>NH 5</th>
                            <th>NH 6</th>
                            <th>Rata-rata NH</th>
                            <th>UAS</th>
                            <th>Nilai Akhir</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal edit -->
<div class="modal fade" id="staticBackdropEdit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Edit Nilai</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="btn-close-modal-edit"></button>
      </div>
      <div class="modal-body">

        <div class="error"></div>

        <form action="" id="form-edit-nilai">

            <table class="table">
                <thead>
                    <tr style="border-bottom:2px solid #5034FF;">
                        <td>N1</td>
                        <td>N2</td>
                        <td>N3</td>
                        <td>N4</td>
                        <td>N5</td>
                        <td>N6</td>
                        <td>UAS</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <input type="hidden" name="id_nilai" id="id_nilai">
                            <input type="hidden" name="id_thn_pelajaran" id="id_thn_pelajaran">
                            <input type="hidden" name="id_mapel_ustad" id="id_mapel_ustad">
                            <input type="number" min="0" name="n1" id="n1" class="form-control fc-edited">
                        </td>
                        <td>
                            <input type="number" min="0" name="n2" id="n2" class="form-control fc-edited">
                        </td>
                        <td>
                            <input type="number" min="0" name="n3" id="n3" class="form-control fc-edited">
                        </td>
                        <td>
                            <input type="number" min="0" name="n4" id="n4" class="form-control fc-edited">      
                        </td>
                        <td>
                            <input type="number" min="0" name="n5" id="n5" class="form-control fc-edited">
                        </td>
                        <td>
                            <input type="number" min="0" name="n6" id="n6" class="form-control fc-edited">
                        </td>
                        <td>
                            <input type="number" min="0" name="uas" id="uas" class="form-control fc-edited">
                        </td>
                    </tr>
                </tbody>
            </table>

            <button class="btn btn-sm btn-primary float-end d-flex" id="btn-edit-nilai-santri">
                <div>Edit</div>
                <svg id="loading-edit" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; background: rgba(255, 255, 255, 0); display: none; shape-rendering: auto;" width="24px" height="24px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                <g>
                    <path d="M50 15A35 35 0 1 0 74.74873734152916 25.251262658470843" fill="none" stroke="#ffffff" stroke-width="12"></path>
                    <path d="M49 3L49 27L61 15L49 3" fill="#ffffff"></path>
                    <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"></animateTransform>
                </g>
                </svg>
            </button>
        </form>
        
      </div>
    </div>
  </div>
</div>
@endsection

@push('script')
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function(){

        var tahun_pelajaran_id = $('#tahun_pelajaran_id').val()
        var mapel_ustad_id = $('#mapel_ustad_id').val()

        // ambil data nilai santri
        $('#tab-nilai-santri').DataTable({
            language: {
                url: '{{ asset("plugins/datatable-bahasa/bahasa-indonesia.json") }}'
            },
            serverSide: true,
            responsive: true,
            ajax: {
                url: '/penilaian/tahun_pelajaran_id/'+tahun_pelajaran_id+'/mapel_ustad_id/'+mapel_ustad_id+'/nilai_santri',
            },
            columns: [
                {
                "data" : null, "sortable" : false,
                render: function(data, type, row, meta){
                    return meta.row + meta.settings._iDisplayStart + 1
                }
                },
                {data: 'santri', name: 'santri'},
                {data: 'n1', name: 'n1'},
                {data: 'n2', name: 'n2'},
                {data: 'n3', name: 'n3'},
                {data: 'n4', name: 'n4'},
                {data: 'n5', name: 'n5'},
                {data: 'n6', name: 'n6'},
                {data: 'rata_rata_n', name: 'rata_rata_n'},
                {data: 'uas', name: 'uas'},
                {data: 'nilai_akhir', name: 'nilai_akhir'},
                {data: 'action', name: 'action'}
            ]
        })


        // ajax toke setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })


        // ambil data edit untuk modal nilai santri
        $(document).on('click', '.edit-nilai-santri', function(){
            var id_nilai = $(this).attr('id_nilai')
            var id_thn_pelajaran = $(this).attr('id_thn_pelajaran')
            var id_mapel_ustad = $(this).attr('id_mapel_ustad')

            $.ajax({
                type: "GET",
                url: "/penilaian/tahun_pelajaran_id/"+id_thn_pelajaran+"/mapel_ustad_id/"+id_mapel_ustad+"/nilai_santri/"+id_nilai+"/detail",
                success: function(response){
                    $('#id_nilai').val(response.data.id)
                    $('#id_thn_pelajaran').val(response.data.tahun_pelajaran_id)
                    $('#id_mapel_ustad').val(response.data.mapel_ustad_id)

                    $('#n1').val(response.data.n1)
                    $('#n2').val(response.data.n2)
                    $('#n3').val(response.data.n3)
                    $('#n4').val(response.data.n4)
                    $('#n5').val(response.data.n5)
                    $('#n6').val(response.data.n6)
                    $('#uas').val(response.data.uas)
                },
                error: function(error){
                    console.log(error)
                }
            })
        })


        // tutup modal
        $(document).on('click', '#btn-close-modal-edit', function(){
            $('.error').html('')
        })


        // edit nilai
        $(document).on('submit', '#form-edit-nilai', function(e){
            e.preventDefault()

            $('.error').html('')

            // id tahun pelajaran
            var id_nilai = $('#id_nilai').val()
            var id_thn_pelajaran = $('#id_thn_pelajaran').val()
            var id_mapel_ustad = $('#id_mapel_ustad').val()

            // animasi
            let btn = document.getElementById('btn-edit-nilai-santri')
            btn.setAttribute('disabled', true)
            let loading = document.getElementById('loading-edit')
            loading.style.display = 'block'

            let formData = new FormData($('#form-edit-nilai')[0])

            $.ajax({
                type: "POST",
                url: "/penilaian/tahun_pelajaran_id/"+id_thn_pelajaran+"/mapel_ustad_id/"+id_mapel_ustad+"/nilai_santri/"+id_nilai+"/update",
                data: formData,
                contentType: false,
                processData: false, 
                success: function(response){
                    if(response.error){
                        $('.error').append('<div class="alert alert-danger">'+response.error+'</div>')
                        // hilangkan animasi
                        loading.style.display = 'none'
                        btn.removeAttribute('disabled', false)
                    }else{
                        
                        $('#tab-nilai-santri').DataTable().ajax.reload()
                        $('#btn-close-modal-edit').click()
    
                        // hilangkan animasi
                        loading.style.display = 'none'
                        btn.removeAttribute('disabled', false)
    
                        // notifikasi
                        Toast.fire({
                            icon: 'success',
                            title: 'Nilai berhasil diedit'
                        })
                    }
                }
            })
        })

    })
</script>
@endpush