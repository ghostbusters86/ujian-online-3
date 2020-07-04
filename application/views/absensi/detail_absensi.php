<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?=$subjudul?></h3>
        <div class="box-tools pull-right">
            <a href="<?=base_url()?>absensi" class="btn btn-sm btn-flat btn-warning">
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
                    <h4>Kelas <i class="fa fa-address-book-o pull-right"></i></h4>
                    <p><?=$pertemuan->nama_kelas?></p>
                </div>
                <div class="alert bg-purple">
                    <h4>Nama Pertemuan <i class="fa fa-address-book-o pull-right"></i></h4>
                    <p><?=$pertemuan->nama_pertemuan?></p>
                </div>
                <input type="hidden" name="id" id="id" value="<?php echo $id; ?>" readonly>
            </div>
            <div class="col-sm-8">
            <?php echo form_open('absensi/update_absensi'); ?>
                <div class="table-responsive " style="border: 0">
                
                    <table id="hasil_nilai" class="w-100 table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>NPP</th>
                            <th>Nama</th>
                            <th>Keterangan</th>
                            <th>Tanggal Absensi</th>
                        </tr>        
                    </thead>
                    <?php
                    $no = 1;
                        foreach ($absensi as $dataabsensi) {
                            # code...
                            if($dataabsensi->keterangan == ''){
                                $input_keterangan = '
                                    <select class="form-control" name="keterangan[]" required>
                                        <option value="">---Pilih---</option>
                                        <option value="S">Sakit</option>
                                        <option value="I">Ijin</option>
                                        <option value="A">Alpha</option>
                                    </select>';
                                $input_nim = '
                                    <input type="hidden" name="id_mahasiswa[]" class="form-control" value="'.$dataabsensi->id_mahasiswa.'">'.$dataabsensi->nim;
                                $input_pertemuan = '
                                    <input type="hidden" name="id_pertemuan[]" class="form-control" value="'.$pertemuan->id_pertemuan.'">';
                                    // $kosong = 'ada';
                            }else{
                                $input_keterangan = $dataabsensi->keterangan;
                                $input_nim = $dataabsensi->nim;
                                $input_pertemuan = '';
                                // $kosong = 'tidak ada';
                            }

                            echo '
                            <tr>
                                <td>'.$input_pertemuan.$no.'</td>
                                <td>'.$input_nim.'</td>
                                <td>'.$dataabsensi->nama.'</td>
                                <td><center>'.$input_keterangan.'</center></td>
                                <td>'.$dataabsensi->waktu.'</td>
                            </tr>     
                            ';
                            $no++;
                        }
                    ?>
                    </table>
                </div>
                <?php 
                $data  = '';
                    foreach ($absensi as $dataabsensi) {
                        if($dataabsensi->keterangan == ''){
                            $data .= '1';
                        }else{
                            $data .= '0';
                        }
                    }

                    if((integer)$data > 0){
                        echo '
                        <button type="submit" class="btn btn-primary pull-right"><i class="icon-ok"></i> Simpan Data </button>
                        ';
                    }else{
                        echo '';
                    }
                ?>
                
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>


<!-- <script src="<?=base_url()?>assets/dist/js/app/ujian_essay/data_nilai.js"></script> -->