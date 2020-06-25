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
            
            { "data": 'terlambat',
                render: function ( data, type, row ) {
                return data;
                } 
            },
            { "data": 'nilai' },
            { 
                // "data": 'ada',
                "searchable": false,
                "orderable": false 
            },
            { "data": 'ada' },
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

            var patokan = new Date(data.terlambat);
            // sometimes even the US needs 24-hour time
            options = {
            year: 'numeric', month: 'numeric', day: 'numeric',
            hour: 'numeric', minute: 'numeric', second: 'numeric',
            hour12: false,
            timeZone: 'Asia/Jakarta' 
            };
            var tanggalpatokan = new Intl.DateTimeFormat('en-US', options).format(patokan);
            // console.log(tanggalpatokan)

            
            if ( tanggalpatokan < tanggalsekarang && data.ada == '0') {
                // $('td', row).css('background-color', '#b50000');
                $('td', row).css('color', 'red');
                // $('td', row).css('font-weight', 'bold');
              }
        }
    });
});