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
            { "data": 'terlambat' },
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
            { "visible": false, "targets": 7 },
            {
                "targets": 5,
                "render": $.fn.dataTable.render.moment( 'Do MMM YYYY' )
              },
            {
                "targets": 6,
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
            // var today = new Date();
            // var hari = date.getDate();
            // var bulan = date.getMonth();
            // var tahun = date.getFullYear();
            // var jam = today.getHours();
            // var menit = today.getMinutes();
            // var detik = today.getSeconds();
            // console.log(data)
            // if (data[7] ===1) {
            if ( data.ada == "0" ) {
                $('td', row).css('background-color', '#b50000');
                $('td', row).css('color', '#FFFFFF');
              }
        }
    });
});