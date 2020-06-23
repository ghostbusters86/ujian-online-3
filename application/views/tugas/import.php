<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $subjudul ?></h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        
        <div class="row">
          
            <div class="col-sm-6 col-sm-offset-3">
            
                    <br>
                    <h4>Preview Data</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td>No</td>
                                <td>Jurusan</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                // if (empty($import)) {
                                //     echo '<tr><td colspan="2" class="text-center">Data kosong! pastikan anda menggunakan format yang telah disediakan.</td></tr>';
                                // } else {
                                //     $no = 1;
                                //     foreach ($import as $jurusan) :
                                    $no = 1;
                                        for($i=0; $i<count($import); $i++){
                                            echo '<tr>';
                                            echo '<td>'.$no.'</td>';
                                            echo '<td>'.$import[$i].'</td>';
                                            echo '</tr>';
                                            $no++;
                                        }
                                        ?>
                                    
                            <?php
                                //     endforeach;
                                // }
                                ?>
                        </tbody>
                    </table>
                    
            </div>
        </div>
    </div>
</div>