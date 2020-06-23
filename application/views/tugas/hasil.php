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
                <button type="button" onclick="reload_ajax()" class="btn bg-purple btn-flat btn-sm"><i class="fa fa-refresh"></i> Reload</button>
            </div>
        </div>
    </div>
    <div class="table-responsive px-4 pb-3" style="border: 0">
        <table id="hasil" class="w-100 table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Tugas</th>
                <th>Deskripsi Tugas</th>
                <th>File Tugas</th>
                <th>Mata Kuliah</th>
                <th>Waktu Mulai</th>
                <th>Waktu Selesai</th>
                <th class="text-center">
                    <i class="fa fa-search"></i>Action
                </th>
            </tr>        
        </thead>
        <tfoot>
            <tr>
                <th>No.</th>
                <th>Nama Tugas</th>
                <th>Deskripsi Tugas</th>
                <th>File Tugas</th>
                <th>Mata Kuliah</th>
                <th>Waktu Mulai</th>
                <th>Waktu Selesai</th>
                <th class="text-center">
                    <i class="fa fa-search"></i>Action
                </th>
            </tr>
        </tfoot>
        </table>
    </div>


    <div class="modal fade" id="modalWarning" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
               <div class="modal-content">
                   <div class="modal-header">
                       <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                       <h4 class="modal-title" id="myModalLabel">Tugas Mahasiswa</h4>
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
</div>

<script src="<?=base_url()?>assets/dist/js/app/tugas/hasil.js"></script>

<script>
    $('#hasil').on('click','.detailTugasMahasiswa',function(){
        var kode=$(this).data('kode');
            $.ajax({
                type : "GET",
                url  : "<?php echo base_url() ?>hasiltugas/detailHasilTugas/"+kode,
                dataType : "JSON",
                success: function(data){
                    console.log(data)
                    tabel = ''
                    tabel += '<form method="post" id="form-download">'
                    tabel += '<table class="w-100 table table-striped table-bordered table-hover">'
                    tabel += '<tr>'
                    tabel += '<th><input type="checkbox" id="check-all"></th>'
                    tabel += '<th>NIM</th>'
                    tabel += '<th>Nama</th>'
                    tabel += '<th>File Tugas</th>'
                    tabel += '<th>Tanggal Mengumpulkan</th>'
                    tabel += '<th>Aksi</th>'
                    tabel += '</tr>'
                        if(data.length > 0){
                            no = 1;
                            for(i=0; i<data.length; i++){
                                tabel += '<tr>'
                                tabel += '<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">'
                                tabel += '<th><input type="checkbox" class="check-item" id="tugas_'+i+'"  name="tugas" value="'+data[i].tugas_mahasiswa+'"></th>'
                                tabel += '<th>'+data[i].nim+'</th>'
                                tabel += '<th>'+data[i].nama+'</th>'
                                tabel += '<th>'+data[i].tugas_mahasiswa+'</th>'
                                tabel += '<th>'+data[i].waktu+'</th>'
                                tabel += '<th><a href="'+base_url+'hasiltugas/preview/'+data[i].tugas_mahasiswa+'/'+data[i].ext+'" class="btn btn-xs bg-maroon" target:_blank > Preview </a></th>'
                                tabel += '</tr>'
                            no++;
                            }
                        }
                        // ?>
                        tabel += '</table>'
                        tabel += '<hr>'
                        tabel += '<button type="button" id="download" class="btn btn-success">Download</button>'
                        tabel += '</form>'
                    $('#tampilData').html(tabel);
                    $('#modalWarning').modal('show');
                }
            });
            return false;
            
    });


    // $(document).ready(function(){ // Ketika halaman sudah siap (sudah selesai di load)
        $(document).on("click", "#check-all", function () {
        // $("#check-all").click(function(){ // Ketika user men-cek checkbox all
        if($(this).is(":checked")) // Jika checkbox all diceklis
            $(".check-item").prop("checked", true); // ceklis semua checkbox siswa dengan class "check-item"
        else // Jika checkbox all tidak diceklis
            $(".check-item").prop("checked", false); // un-ceklis semua checkbox siswa dengan class "check-item"
        });
        
        $(document).on("click", "#download", function (e) {
            var theForm = document.getElementById( 'form-download' );
            var i;
            var selectArray = []; //initialise empty array
            for (i = 0; i < theForm.tugas.length; i++) {
                if(theForm.tugas[i].type == 'checkbox'){
                    if(theForm.tugas[i].checked == true){
                        selectArray.push(theForm.tugas[i].value);
                        // alert(theForm.tugas[i].value);
                        window.open(base_url+'tugas/downloadTugasMahasiswa/'+theForm.tugas[i].value);
                    }
                }
            }
            
        });
    // });


</script>