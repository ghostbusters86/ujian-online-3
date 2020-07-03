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

         <!-- <div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
               <div class="modal-content">
                   <div class="modal-header">
                       <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                       <h4 class="modal-title" id="myModalLabel">Detail Tugas</h4>
                   </div>
                   <div class="modal-body">
                       <div id="tampilData"></div>

                   </div>
                   <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                   </div>
                    </div>
            </div>
         </div>

         <div class="modal fade" id="modalWarning" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
               <div class="modal-content">
                   <div class="modal-header">
                       <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                       <h4 class="modal-title" id="myModalLabel">Upload Tugas</h4>
                   </div>
                   <div class="modal-body">
                       <div id="tampilWarning"></div>

                   </div>
                   <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                   </div>
                    </div>
            </div>
         </div> -->

         
         
         
<script src="<?=base_url()?>assets/dist/js/app/absensi/list.js"></script>

<script>
    // $('#tugas').on('click','.detailTugas',function(){
    //     var kode=$(this).data('kode');
    //         $.ajax({
    //             type : "GET",
    //             url  : "<?php //echo base_url() ?>tugas/detailTugas/"+kode,
    //             dataType : "JSON",
    //             success: function(data){
    //                 var tampil = ''
    //                     tampil += '<p><b>Judul Tugas : '+data.nama_tugas+'</b></p>'
    //                     tampil += '<p>Deskripsi : '+data.deskripsi_tugas+'</p>'
    //                     if(data.file_tugas == ''){
    //                         tampil += ''
    //                     }else{
    //                         tampil += '<center><a href="'+base_url+'tugas/downloadTugas/'+data.file_tugas+'" class="btn btn-success"><i class="fa fa-download"></i> Download File</a></center>'
    //                     }
    //                 $('#tampilData').html(tampil);
    //                 $('#modalDetail').modal('show');
    //             }
    //         });
    //         return false;
            
    // });

    // $('#tugas').on('click','.uploadTugas',function(){
    //     var kode=$(this).data('kode');
    //         $.ajax({
    //             type : "GET",
    //             url  : "<?php //echo base_url() ?>tugas/uploadTugasMahasiswa/"+kode,
    //             dataType : "JSON",
    //             success: function(data){
    //                 console.log(data)
    //                 var method
    //                 $('[name="id_tugas"]').val(data.data[0]);
    //                 $('[name="nim"]').val(data.data[1]);
    //                 $('[name="deskripsi"]').val(data.rowdata.deskripsi);
    //                 if(data.hasil == 'zonk'){
    //                     method = 'add'
    //                     $('[name="method"]').val(method);
    //                 }else{
    //                     method = 'edit'
    //                     $('[name="method"]').val(method);
    //                     $('[name="id_hasil_tugas"]').val(data.rowdata.id);
    //                     var download = ''
    //                     download += '<strong>Tugas Yang Sudah Dikumpulkan : </strong><a href="'+base_url+'tugas/downloadTugasMahasiswa/'+data.rowdata.tugas_mahasiswa+'" class="btn btn-success"><i class="fa fa-download"></i> Download File</a>'
    //                 }
    //                 $('#download').html(download);
    //                 $('#uploadTugas').modal('show');
                    
    //             }
    //         });
    //         return false;
            
    // });

    // $('#btn_save').on('click', function (e) {
    //     e.preventDefault(); 
    //         $.ajax({
    //             url:'<?php //echo base_url();?>index.php/tugas/do_upload_mahasiswa',
    //             type:"post",
    //             data:new FormData(document.getElementById("formUpload")),
    //             processData:false,
    //             contentType:false,
    //             cache:false,
    //             async:false,
    //             success: function(data){
    //                 if(data == '"upload berhasil"'){
    //                     title = 'Sukses' 
    //                     text  = "Data Berhasil disimpan"
    //                     type  = "success"
    //                 }else{
    //                     title = 'Gagal' 
    //                     text  = data
    //                     type  = "warning"
    //                 }
    //                 Swal({
    //                     "title": title,
    //                     "text": text,
    //                     "type": type
    //                 }).then((result) => {
    //                     document.getElementById("formUpload").reset();
    //                     $('#uploadTugas').modal('hide');
    //                     reload_ajax();
    //                 });

                    
    //             }
    //         });
    // });
</script>
