var table;

$(document).ready(function () {

    ajaxcsrf();

    table = $("#absensi").DataTable({
        initComplete: function () {
            var api = this.api();
            $('#absensi_filter input')
                .off('.DT')
                .on('keyup.DT', function (e) {
                    api.search(this.value).draw();
                });
        },
        oLanguage: {
            sProcessing: "loading..."
        },
        processing: true,
        serverSide: true,
        ajax: {
            "url": base_url+"absensi/list_json",
            "type": "POST",
        },
        columns: [
            {
                "data": "id_pertemuan",
                "orderable": false,
                "searchable": false
            },
            { "data": 'nama_pertemuan' },
            { "data": 'materi' },
            { "data": 'file_materi' },
            { "data": 'nama_matkul' },
            { "data": 'nama_dosen' },
            
            { "data": 'tanggal_mulai' },
            
            { "data": 'tanggal_selesai' 
            
            },
            { "data": 'keterangan', 
            "orderable": false,
            "searchable": false},
            {
                
                "searchable": false,
                "orderable": false
            }
        ],
        columnDefs: [
            {
                "targets": 9,
                "data": {
                    "id_pertemuan": "id_pertemuan",
                    "status": "status", 
                    "tanggal_mulai" : "tanggal_mulai",
                    "tanggal_selesai" : "tanggal_selesai"
                },
                "render": function (data, type, row, meta) {
                    
                    var date = new Date(Date.now());
                    // sometimes even the US needs 24-hour time
                    options = {
                    year: 'numeric', month: 'numeric', day: 'numeric',
                    hour: 'numeric', minute: 'numeric', second: 'numeric',
                    hour12: false,
                    timeZone: 'Asia/Jakarta' 
                    };
                    var tanggalsekarang = new Intl.DateTimeFormat('en-US', options).format(date);
                    // console.log(tanggalsekarang)

                    var data_mulai = data.tanggal_mulai;
                    var p_mulai = data_mulai.split(' ');
                    var dateq_ = p_mulai[0];
                    var timeq_ = p_mulai[1];
                    var p_mulai = dateq_.split('-');
                    var batas_mulai = (p_mulai[2])+'-'+(p_mulai[0])+'-'+(p_mulai[1])+'T'+timeq_+':00';
                    // var dcontoh = '2020-06-30T11:51:00';
                    var date_mulai = new Date(batas_mulai);
                    var tanggalmulai = new Intl.DateTimeFormat('en-US', options).format(date_mulai);

                    var data_selesai = data.tanggal_selesai;
                    var p_lambat = data_selesai.split(' ');
                    var date_ = p_lambat[0];
                    var time_ = p_lambat[1];
                    var p_lambat = date_.split('-');
                    var batas_lambat = (p_lambat[2])+'-'+(p_lambat[0])+'-'+(p_lambat[1])+'T'+time_+':00';
                    // var dcontoh = '2020-06-30T11:51:00';
                    var date_late = new Date(batas_lambat);
                    var tanggalselesai = new Intl.DateTimeFormat('en-US', options).format(date_late);
                    
                    if(tanggalsekarang < tanggalmulai){
                        return `<span class="btn btn-xs btn-warning" >
                                <i class="fa  fa-clock-o"></i> Belum Mulai
                            </span>`;
                    }else{
                        if(tanggalsekarang > tanggalmulai && tanggalsekarang < tanggalselesai && data.status == null){
                            return `<div class="text-center">
                                        
                                        <a href="javascript:void(0);" class="isi_absen btn btn-success btn-xs" data-kode="${data.id_pertemuan}"><i class="fa fa-rocket"></i> Isi Absen</a>
                                            
                                    </div>`;
                        }else if(tanggalsekarang > tanggalmulai && tanggalsekarang > tanggalselesai && data.status == null){
                            return `<span class="btn btn-xs btn-danger" >
                                <i class="fa fa-close"></i> Terlambat Absen
                            </span>`;
                        }else if(tanggalsekarang > tanggalmulai && tanggalsekarang < tanggalselesai && data.status != null){
                            return `<span class="btn btn-xs btn-success" >
                                <i class="fa fa-check-circle-o"></i> Sudah Absen
                            </span>`;
                        }else if(tanggalsekarang > tanggalmulai && tanggalsekarang > tanggalselesai && data.status != null){
                            return `<span class="btn btn-xs btn-success" >
                                <i class="fa fa-check-circle-o"></i> Sudah Absen
                            </span>`;
                        }

                        
                    }
                }
            }
        ],
        order: [
            [6, 'asc']
        ],
        rowId: function (a) {
            return a;
        },
        "rowCallback": function (row, data, iDisplayIndex) {
            var info = this.fnPagingInfo();
            var page = info.iPage;
            var length = info.iLength;
            var index = page * length + (iDisplayIndex + 1);
            $('td:eq(0)', row).html(index);
            
            
            

            // if ( tanggalpatokan < tanggalsekarang && data.ada == '0') {
            //     $('td', row).css('color', 'red');
            // }

            // if (data.telat == 'Y') {
            //     $('td', row).css('background-color', 'yellow');
            //     $('td', row).css('color', 'black');
            // }
            
            
        }
    });
});