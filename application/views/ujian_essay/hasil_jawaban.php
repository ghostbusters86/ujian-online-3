<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?=$subjudul?></h3>
        <div class="box-tools pull-right">
            <a href="<?=base_url()?>tugas/master" class="btn btn-sm btn-flat btn-warning">
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
                    <h4>NIM <i class="fa fa-address-book-o pull-right"></i></h4>
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
                                        <input type="hidden" name ="id_detail[]" id="id_detail[] value='.$jawaban->nilai.' size="2">
                                        <td>'.$no.'</td>
                                        <td><button class="btn btn-xs btn-primary" id="'.$jawaban->file.'">Preview<button></td>
                                        <td>'.$jawaban->soal_essay.'</td>
                                        <td>'.$jawaban->jawaban_essay.'</td>
                                        <td><input type="text" name ="nilai[]" id="nilai[]" value='.$jawaban->nilai.' size="2"></td>
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
