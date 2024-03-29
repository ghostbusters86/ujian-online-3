$(document).ready(function () {
    $('.datetimepicker').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss'
    });

    $('#formpertemuan input, #formpertemuan select').on('change', function () {
        $(this).closest('.form-group').eq(0).removeClass('has-error');
        $(this).nextAll('.help-block').eq(0).text('');
    });

    $('#formpertemuan').on('submit', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        let btn = $('#submit');
        btn.attr('disabled', 'disabled').text('Proses...');

        $.ajax({
            url: $(this).attr('action'),
            data: new FormData(this),
            processData:false,
            contentType:false,
            cache:false,
            async:false,
            type: 'POST',
            success: function (data) {
                console.log(data)
                btn.removeAttr('disabled').html('<i class="fa fa-save"></i> Simpan');

                if (data.status) {
                    Swal({
                        "title": "Berhasil",
                        "type": "success",
                        "text": "Data berhasil disimpan"
                    }).then(result => {
                        window.location = "master";
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