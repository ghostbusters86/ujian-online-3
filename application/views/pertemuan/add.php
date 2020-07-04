<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?=$subjudul?></h3>
        <div class="box-tools pull-right">
            <a href="<?=base_url()?>pertemuan/master" class="btn btn-sm btn-flat btn-warning">
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
            </div>
            <div class="col-sm-4">
            <?php echo form_open_multipart('pertemuan/save', array('id'=>'formpertemuan'), array('method'=>'add','dosen_id'=>$dosen->id_dosen, 'matkul_id'=>$matkul->matkul_id))?>
                <div class="form-group">
                    <label for="id_kelas">Kelas</label>
                    <select id="id_kelas" name="id_kelas" class="form-control">
                        <?php 
                            foreach ($kelas as $datakelas) {
                                # code...
                                echo '
                                    <option value="'.$datakelas->id_kelas.'">'.$datakelas->nama_kelas.'</option>
                                ';
                            }
                        ?>
                        
                    </select>
                    <!-- <input autofocus="autofocus" onfocus="this.select()" placeholder="Nama Pertemuan" type="text" class="form-control" name="nama_pertemuan"> -->
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="nama_pertemuan">Nama Pertemuan</label>
                    <input autofocus="autofocus" onfocus="this.select()" placeholder="Nama Pertemuan" type="text" class="form-control" name="nama_pertemuan">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="materi">Materi</label>
                    <textarea name="materi" class="form-control" rows="4" cols="50" placeholder="Materi"></textarea>
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="file_materi">File Materi</label>
                    <input type="file" name="file_materi" class="form-control">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="tgl_mulai">Tanggal Mulai</label>
                    <input name="tgl_mulai" type="text" class="datetimepicker form-control" placeholder="Tanggal Mulai">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="tgl_selesai">Tanggal Selesai</label>
                    <input name="tgl_selesai" type="text" class="datetimepicker form-control" placeholder="Tanggal Selesai">
                    <small class="help-block"></small>
                </div>
                
                <div class="form-group pull-right">
                    <button type="reset" class="btn btn-default btn-flat">
                        <i class="fa fa-rotate-left"></i> Reset
                    </button>
                    <button id="submit" type="submit" class="btn btn-flat bg-purple"><i class="fa fa-save"></i> Simpan</button>
                </div>
                <?=form_close()?>
            </div>
        </div>
    </div>
</div>

<script src="<?=base_url()?>assets/dist/js/app/pertemuan/add.js"></script>