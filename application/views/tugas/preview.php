
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?=$subjudul?></h3>
        <div class="box-tools pull-right">
            <a href="<?=base_url()?>hasiltugas" class="btn btn-sm btn-flat btn-warning">
                <i class="fa fa-arrow-left"></i> Batal
            </a>
            
        </div>
    </div>
    <div class="box-body">
    <embed src="<?php echo base_url() ?>uploads/tugasMahasiswa/<?=$id?>" type="application/pdf" width="100%" height="600px" />
    </div>
	

    

