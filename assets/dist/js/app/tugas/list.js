var table;

$(document).ready(function () {

    ajaxcsrf();

    table = $("#tugas").DataTable({
        initComplete: function () {
            var api = this.api();
            $('#tugas_filter input')
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
            "url": base_url+"tugas/list_json",
            "type": "POST",
        },
        columns: [
            {
                "data": "id_tugas",
                "orderable": false,
                "searchable": false
            },
            { "data": 'nama_tugas' },
            { "data": 'nama_matkul' },
            { "data": 'nama_dosen' },
            
            { "data": 'tanggal_mulai' },
            
            { "data": 'terlambat'
            },
            { "data": 'nilai' },
            { 
                // "data": 'ada',
                "searchable": false,
                "orderable": false 
            },
            { "data": 'ada',
            "orderable": false,
            "searchable": false},
            {
                "data": 'action',
                "searchable": false,
                "orderable": false
            }
        ],
        columnDefs: [
            { "visible": false, "targets": 8 },
            // {
            //     "targets": 5,
            //     "render": $.fn.dataTable.render.moment( 'Do MMM YYYY' )
            //   },
            {
                "targets": 7,
                "data": 'ada',
                "render": function (data, type, row, meta) {
                    var btn;
                    if (data > 0) {
                        btn = `
								<span class="btn btn-xs btn-primary" >
									<i class="fa fa-check-circle"></i> Sudah Mengumpulkan
								</span>`;
                    } else {
                        btn = `<span class="btn btn-xs btn-warning" >
								<i class="fa fa-times-circle"></i> Belum Mengumpulkan
							</span>`;
                    }
                    return `<div class="text-center">
									${btn}
								</div>`;
                }
            }
        ],
        order: [
            [1, 'asc']
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

            var data_terlambat = data.terlambat;
            var p_lambat = data_terlambat.split(' ');
            var date_ = p_lambat[0];
            var time_ = p_lambat[1];
            var p_lambat = date_.split('-');
            var batas_lambat = (p_lambat[2])+'-'+(p_lambat[0])+'-'+(p_lambat[1])+'T'+time_+':00';
            var dcontoh = '2020-06-30T11:51:00';
            var date_late = new Date(batas_lambat);
            var tanggalpatokan = new Intl.DateTimeFormat('en-US', options).format(date_late);
            

            if ( tanggalpatokan < tanggalsekarang && data.ada == '0') {
                $('td', row).css('color', 'red');
            }

            if (data.telat == 'Y') {
                $('td', row).css('background-color', 'yellow');
                $('td', row).css('color', 'black');
            }
            
            
        }
    });
});