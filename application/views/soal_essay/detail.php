<div class="box">
    <div class="box-header with-header">
        <h3 class="box-title">Detail Soal</h3>
        <div class="pull-right">
            <a href="<?=base_url()?>soal_essay" class="btn btn-xs btn-flat btn-default">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>
            <a href="<?=base_url()?>soal_essay/edit/<?=$this->uri->segment(3)?>" class="btn btn-xs btn-flat btn-warning">
                <i class="fa fa-edit"></i> Edit
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
                <h3 class="text-center">Soal</h3>
                <?php if(!empty($soal->file)): ?>
                    <div class="w-50">
                        <?= tampil_media('uploads/bank_soal_essay/'.$soal->file); ?>
                    </div>
                <?php endif; ?>
                <?=$soal->soal_essay?>
                <hr class="my-4">
                
                <strong>Dibuat pada :</strong> <?=$soal->created_at?>
                <br>
                <strong>Terkahir diupdate :</strong> <?=$soal->updated_at?>
            </div>
        </div>
    </div>
</div>