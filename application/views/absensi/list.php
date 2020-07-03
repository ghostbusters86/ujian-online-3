<div class="row">
	<div class="col-sm-3">
        <div class="alert bg-green">
            <h4>Kelas<i class="pull-right fa fa-building-o"></i></h4>
            <span class="d-block"> <?=$mhs->nama_kelas?></span>                
        </div>
    </div>
    <div class="col-sm-3">
        <div class="alert bg-blue">
            <h4>Jurusan<i class="pull-right fa fa-graduation-cap"></i></h4>
            <span class="d-block"> <?=$mhs->nama_jurusan?></span>                
        </div>
    </div>
    <div class="col-sm-3">
        <div class="alert bg-yellow">
            <h4>Tanggal<i class="pull-right fa fa-calendar"></i></h4>
            <span class="d-block"> <?=strftime('%A, %d %B %Y')?></span>                
        </div>
    </div>
    <div class="col-sm-3">
        <div class="alert bg-red">
            <h4>Jam<i class="pull-right fa fa-clock-o"></i></h4>
            <span class="d-block"> <span class="live-clock"><?=date('H:i:s')?></span></span>                
        </div>
    </div>
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?=$subjudul?></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-4">
                        <button type="button" onclick="reload_ajax()" class="btn btn-sm btn-flat bg-purple"><i class="fa fa-refresh"></i> Reload</button>
                    </div>
                </div>
            </div>
            <div class="table-responsive px-4 pb-3" style="border: 0">
                <table id="absensi" class="w-100 table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Pertemuan</th>
                        <th>Materi</th>
                        <th>File</th>
                        <th>Mata Kuliah</th>
						<th>Dosen</th>
                        <th>Mulai</th>
                        <th>Selesai</th>
                        <th>Absensi</th>
                        <th class="text-center">Aksi</th>
                    </tr>        
                </thead>
                <tfoot>
                    <tr>
                        <th>No.</th>
                        <th>Nama Pertemuan</th>
                        <th>Materi</th>
                        <th>File</th>
                        <th>Mata Kuliah</th>
						<th>Dosen</th>
                        <th>Mulai</th>
                        <th>Selesai</th>
                        <th>Absensi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

         <div class="modal fade" id="modalAbsen" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
               <div class="modal-content">
                   <div class="modal-header">
                       <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                       <h4 class="modal-title" id="myModalLabel">Isi Absensi</h4>
                   </div>
                   <div class="modal-body">
                   <?php //echo form_open('', array('id'=>'formSimpan'))?>
                   <div class="form-group">
                            <label class="col-xs-3"">Token</label>
                
                            <input name="id_pertemuan" id="id_pertemuan" class="form-control" type="hidden"   readonly>
                            <input name="id_mahasiswa" id="id_mahasiswa" class="form-control" type="hidden"   readonly>
                            <div class="col-xs-9"">
                                <input name="token" id="token" class="form-control" type="text" placeholder="Token" required>
                            </div>
                        </div>
                   </div>
                   <div class="modal-footer">
                   <button style="margin-top:15px" type="button" type="submit" id="simpan_absensi" class="btn btn-primary">Simpan</button>
                    <button style="margin-top:15px" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                   </div>

                   <?php //echo form_close(); ?>
                    </div>
            </div>
         </div>
         
         
<script src="<?=base_url()?>assets/dist/js/app/absensi/list.js"></script>

<script>
    $('#absensi').on('click','.isi_absen',function(){
        var kode=$(this).data('kode');
            $.ajax({
                type : "GET",
                url  : "<?php echo base_url() ?>absensi/cekdata/"+kode,
                dataType : "JSON",
                success: function(data){
                    // console.log(data)
                    if(data.status == 'telat'){
                        Swal({
                            "title": 'Gagal',
                            "text": 'Waktu isi absensi anda telah habis',
                            "type": 'warning'
                        }).then((result) => {
                            reload_ajax();
                        });
                    }else{
                        $('[name="id_pertemuan"]').val(kode);
                        $('[name="id_mahasiswa"]').val(data.id_mhs);
                        $('#modalAbsen').modal('show');
                    }
                }
            });
            return false;
            
    });


    $('#simpan_absensi').on('click', function (e) {
        // e.preventDefault(); 
        var id_pertemuan = $('#id_pertemuan').val();
        var id_mahasiswa = $('#id_mahasiswa').val();
        var token = $('#token').val();
            $.ajax({
                url:'<?php echo base_url();?>/absensi/simpan_absensi',
                type:"POST",
                dataType : "JSON",
                data : {id_pertemuan:id_pertemuan, id_mahasiswa:id_mahasiswa, token:token},
                success: function(data){
                    
                    if(data.status == 'Terlambat'){
                        Swal({
                            "title": 'Gagal',
                            "text": 'Waktu isi absensi anda telah habis',
                            "type": 'warning'
                        }).then((result) => {
                            $('#modalAbsen').modal('hide');
                            reload_ajax();
                        });
                    }else if(data.status == 'Token Salah'){
                        Swal({
                            "title": 'Gagal',
                            "text": 'Token yang dimasukkan salah',
                            "type": 'warning'
                        }).then((result) => {
                            reload_ajax();
                        });
                    }else{
                        Swal({
                            "title": 'Berhasil',
                            "text": 'Data Absensi anda sudah tersimpan',
                            "type": 'success'
                        }).then((result) => {
                            $('#modalAbsen').modal('hide');
                            reload_ajax();
                        });
                    }
                }
            });
    });
</script>
