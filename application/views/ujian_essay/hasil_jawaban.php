<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?=$subjudul?></h3>
        <div class="box-tools pull-right">
            <a href="<?=base_url()?>ujian_essay/nilai/<?=$ujian->id_ujian_essay?>" class="btn btn-sm btn-flat btn-warning">
                <i class="fa fa-arrow-left"></i> Batal
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-4">
                <div class="alert bg-purple">
                    <h4>Mata Kuliah <i class="fa fa-book pull-right"></i></h4>
                    <p><?=$matkul->nama_matkul?></p>
                </div>
                <div class="alert bg-purple">
                    <h4>Dosen <i class="fa fa-address-book-o pull-right"></i></h4>
                    <p><?=$dosen->nama_dosen?></p>
                </div>
                <div class="alert bg-purple">
                    <h4>Nama Ujian <i class="fa fa-address-book-o pull-right"></i></h4>
                    <p><?=$ujian->nama_ujian_essay?></p>
                </div>
                <div class="alert bg-purple">
                    <h4>NPP <i class="fa fa-address-book-o pull-right"></i></h4>
                    <p><?=$mahasiswa->nim?></p>
                </div>
                <div class="alert bg-purple">
                    <h4>Nama <i class="fa fa-address-book-o pull-right"></i></h4>
                    <p><?=$mahasiswa->nama?></p>
                </div>
            </div>
            <div class="col-sm-8">
            <?php echo form_open('ujian_essay/save_hasil_jawaban'); ?>
                <div class="table-responsive " style="border: 0">
                
                    <table id="hasil_nilai" class="w-100 table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>File</th>
                            <th>Bobot</th>
                            <th>Soal</th>
                            <th>Jawaban</th>
                            <th>Nilai</th>
                        </tr>        
                    </thead>
                        <?php
                            $no = 1;
                            foreach ($jawab as $jawaban) {
                                # code...
                                echo '
                                    <tr>
                                        <input type="hidden" name ="id" id="id" value='.$jawaban->id.' size="2">
                                        <input type="hidden" name ="bobot[]" id="bobot[]" value='.$jawaban->bobot.' size="2">
                                        <input type="hidden" name ="id_detail[]" id="id_detail[]" value='.$jawaban->id_detail.' size="2">
                                        <td>'.$no.'</td>
                                        <td><button class="btn btn-xs btn-primary preview_soal" onclick="return false" id="'.$jawaban->file.'">Preview</td>
                                        <td>'.$jawaban->bobot.'</td>
                                        <td>'.$jawaban->soal_essay.'</td>
                                        
                                        <td>'.$jawaban->jawaban_essay.'</td>
                                        <td><input type="number" name ="nilai[]" id="nilai[]" value='.$jawaban->nilai.' min="0" max="100"></td>
                                    </tr>       
                                ';
                                $no++;
                            }
                        ?>
                    </table>
                </div>
                <button type="submit" class="btn btn-primary pull-right"><i class="icon-ok"></i> Simpan Data </button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-default fade" id="modalkuu">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Gambar Soal</h4>
              </div>
              <div class="modal-body">
                <div id="tampilgambar"></div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-outline">Save changes</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->


<script>
    $('.preview_soal').click(function(){
        var oo = $(this).attr('id');
        tampil = ''
        tampil += '<center><img src="'+base_url+'uploads/bank_soal_essay/'+oo+'" width="200"  heigh="100"></center>'
        $('#tampilgambar').html(tampil)
        $('#modalkuu').modal('show')
        console.log(oo)
    });
</script>