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
                "data": 'tugas_mahasiswa',
                "searchable": false,
                "orderable": false 
            },
            {
                "searchable": false,
                "orderable": false
            }
        ],
        columnDefs: [
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
            },
            {
                "targets": 7,
                "data": {
                    "id_tugas": "id_tugas"
                },
                "render": function (data, type, row, meta) {
                    return `<div class="text-center">
                                <a class="btn btn-xs btn-primary" href="${base_url}tugas/detailTugas/${data.id_tugas}">
                                    <i class="fa fa-print"></i> Detail Tugas
                                </a>
                                <a class="btn btn-xs btn-success" >
                                    <i class="fa fa-upload"></i> Upload Tugas
                                </a>
							</div>`;
                }
            },
        ],
        order: [
            [1, 'asc']
        ],
        rowId: function (a) {
            return a;
        },
        rowCallback: function (row, data, iDisplayIndex) {
            var info = this.fnPagingInfo();
            var page = info.iPage;
            var length = info.iLength;
            var index = page * length + (iDisplayIndex + 1);
            $('td:eq(0)', row).html(index);
        }
    });
});