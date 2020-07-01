var table;

$(document).ready(function () {

    ajaxcsrf();
    var id = $("#es").val();

    table = $("#hasil_nilai").DataTable({
        
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
            "url": base_url+"ujian_essay/data_penilaian/"+id,
            "type": "POST",
        },
        columns: [
            {
                "data": 'id',
                "orderable": false,
                "searchable": false
            },
            { "data": 'nim' },
            { "data": 'nama' },
            { "data": 'tgl_mulai' },
            { "data": 'tgl_selesai' },
            { "data": 'nilai' }
        ],
        columnDefs: [
            
            {
                "targets": 6,
                "data": "id",
                "render": function (data, type, row, meta) {
                    return `<div class="text-center">
                                <a href="${base_url}ujian_essay/hasil_jawaban/${data}" class="btn btn-xs bg-maroon">
                    							<i class="fa fa-search"> Tampilkan </i>
                                            </a>
							</div>`;
                }
            }
        ],
        order: [
            [1, 'desc']
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
            if ( data.status_penilaian == 'Y') {
                $('td', row).css('color', 'red');
            }
        }
    });

});
