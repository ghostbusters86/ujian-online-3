var table;

$(document).ready(function() {
  ajaxcsrf();

  table = $("#hasil").DataTable({
    initComplete: function() {
      var api = this.api();
      $("#hasil_filter input")
        .off(".DT")
        .on("keyup.DT", function(e) {
          api.search(this.value).draw();
        });
    },
    dom:
      "<'row'<'col-sm-3'l><'col-sm-6 text-center'B><'col-sm-3'f>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-5'i><'col-sm-7'p>>",
    buttons: [
      {
        extend: "copy",
        exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
      },
      {
        extend: "print",
        exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
      },
      {
        extend: "excel",
        exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
      },
      {
        extend: "pdf",
        exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
      }
    ],
    oLanguage: {
      sProcessing: "loading..."
    },
    processing: true,
    serverSide: true,
    ajax: {
      url: base_url + "hasiltugas/data",
      type: "POST"
    },
    columns: [
      {
        data: "id_tugas",
        orderable: false,
        searchable: false
      },
      { data: "nama_tugas" },
      { data: "deskripsi_tugas" },
      { orderable: false,
        searchable: false },
      { data: "nama_matkul" },
      { data: "tanggal_mulai" },
      { data: "terlambat" },
      {
        data: "action",
        orderable: false,
        searchable: false
      }
    ],
    columnDefs: [
      {
        targets: 3,
        data: "file_tugas",
        render: function(data, type, row, meta) {
          return `
                    <div class="text-center">
                        <a class="btn btn-xs btn-success" href="${base_url}tugas/downloadTugas/${data}" >
                            <i class="fa fa-download"></i> Download
                        </a>
                    </div>
                    `;
        }
      }
    ],
    order: [[1, "asc"]],
    rowId: function(a) {
      return a;
    },
    rowCallback: function(row, data, iDisplayIndex) {
      var info = this.fnPagingInfo();
      var page = info.iPage;
      var length = info.iLength;
      var index = page * length + (iDisplayIndex + 1);
      $("td:eq(0)", row).html(index);
    }
  });
});

// table
  // .buttons()
  // .container()
  // .appendTo("#hasil_wrapper .col-md-6:eq(0)");
