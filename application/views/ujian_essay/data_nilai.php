<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?=$subjudul?></h3>
        <div class="box-tools pull-right">
            <a href="<?=base_url()?>ujian_essay/master" class="btn btn-sm btn-flat btn-warning">
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
                    <h4>Nama Ujian Essay <i class="fa fa-address-book-o pull-right"></i></h4>
                    <p><?=$ujian->nama_ujian_essay?></p>
                </div>
                <input type="hidden" name="es" id="es" value="<?php echo $es; ?>" readonly>
            </div>
            <div class="col-sm-8">
                <div class="table-responsive " style="border: 0">
                
                    <table id="hasil_nilai" class="w-100 table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>NPP</th>
                            <th>Nama</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Nilai</th>
                            <th class="text-center">Aksi</th>
                        </tr>        
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No.</th>
                            <th>NPP</th>
                            <th>Nama</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Nilai</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="<?=base_url()?>assets/dist/js/app/ujian_essay/data_nilai.js"></script>