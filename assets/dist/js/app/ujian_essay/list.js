var table;

$(document).ready(function () {

    ajaxcsrf();

    table = $("#ujian").DataTable({
        initComplete: function () {
            var api = this.api();
            $('#ujian_filter input')
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
            "url": base_url+"ujian_essay/list_json",
            "type": "POST",
        },
        columns: [
            {
                "data": "id_ujian_essay",
                "orderable": false,
                "searchable": false
            },
            { "data": 'nama_ujian_essay' },
            { "data": 'nama_matkul' },
            { "data": 'nama_dosen' },
            { "data": 'jumlah_soal' },
            { "data": 'waktu' },
            {
                "searchable": false,
                "orderable": false
            }
        ],
        columnDefs: [
            {
                "targets": 6,
                "data": {
                    "id_ujian_essay": "id_ujian_essay",
                    "ada": "ada",
                    "status_penilaian" : "status_penilaian"
                },
                "render": function (data, type, row, meta) {
                    var btn;
                    if (data.ada > 0 && data.status_penilaian == 'Y') {
                        btn = `
								<a class="btn btn-xs btn-warning" >
									<i class="fa fa-info"></i> Proses Penilaian 
								</a>`;
                    }
                    else if (data.ada > 0 && data.status_penilaian == 'N') {
                        btn = `
								<a class="btn btn-xs btn-success" href="${base_url}hasil_ujian_essay/cetak/${data.id_ujian_essay}" target="_blank">
									<i class="fa fa-print"></i> Cetak Hasil 
								</a>`;
                    }
                     else {
                        btn = `<a class="btn btn-xs btn-primary" href="${base_url}ujian_essay/token/${data.id_ujian_essay}">
								<i class="fa fa-pencil"></i> Ikut Ujian
							</a>`;
                    }
                    return `<div class="text-center">
									${btn}
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