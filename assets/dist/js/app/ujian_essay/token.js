$(document).ready(function () {
    ajaxcsrf();

    $('#btncek').on('click', function () {
        var token = $('#token').val();
        var idUjian = $(this).data('id');
        if (token === '') {
            Swal('Gagal', 'Token harus diisi', 'error');
        } else {
            var key = $('#id_ujian').data('key');
            $.ajax({
                url: base_url + 'ujian_essay/cektoken/',
                type: 'POST',
                data: {
                    id_ujian_essay: idUjian,
                    token: token
                },
                cache: false,
                success: function (result) {
                    Swal({
                        "type": result.status ? "success" : "error",
                        "title": result.status ? "Berhasil" : "Gagal",
                        "text": result.status ? "Token Benar" : "Token Salah"
                    }).then((data) => {
                        if(result.status){
                            location.href = base_url + 'ujian_essay/?key=' + key;
                        }
                    });
                }
            });
        }
        // console.log(idUjian)
    });

    var time = $('.countdown');
    if (time.length) {
        countdown(time.data('time'));
    }
});