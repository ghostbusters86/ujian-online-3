$(document).ready(function () {
    $('#tgl_mulai').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        date: tgl_mulai
    });
    $('#tgl_selesai').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        date: terlambat
    });


    $('#formpertemuan input, #formpertemuan select').on('change', function () {
        $(this).closest('.form-group').eq(0).removeClass('has-error');
        $(this).nextAll('.help-block').eq(0).text('');
    });

    $('#formpertemuan').on('submit', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        $.ajax({
            url: $(this).attr('action'),
            data: new FormData(this),
            processData:false,
            contentType:false,
            cache:false,
            async:false,
            type: 'POST',
            success: function (data) {
                console.log(data);
                if (data.status) {
                    Swal({
                        "title": "Berhasil",
                        "type": "success",
                        "text": "Data berhasil disimpan"
                    }).then(result => {
                        window.location.href = base_url+"pertemuan/master";
                    });
                } else {
                    if (data.errors) {
                        $.each(data.errors, function (key, val) {
                            $('[name="' + key + '"]').closest('.form-group').eq(0).addClass('has-error');
                            $('[name="' + key + '"]').nextAll('.help-block').eq(0).text(val);
                            if (val === '') {
                                $('[name="' + key + '"]').closest('.form-group').eq(0).removeClass('has-error');
                                $('[name="' + key + '"]').nextAll('.help-block').eq(0).text('');
                            }
                        });
                    }
                }
            }
        });
    });
});