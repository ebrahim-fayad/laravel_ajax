
$('form#store_country_form').on('submit', function(e) {
    e.preventDefault();
    let form = this;
    let formData = new FormData(form);
    $.ajax({
        url: $(form).attr('action'),
        type: $(form).attr('method'),
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            $('#loading').show();
            $(form).find('.error-text').text('');
            $(form).find('button[type="submit"]').attr('disabled', true).text('Saving...');
        },
        success: function(data) {
            if (data.status == 1) {
                toastr.success(data.message, 'Success', {
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                });
                $(form).trigger('reset');
                if (typeof table !== 'undefined') {
                    table.ajax.reload(null, false);
                }
            } else {
                toastr.error(data.message, 'Error', {
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                });
            }
        },
        error: function(xhr) {
            if (xhr.status == 422) {
                $.each(xhr.responseJSON.errors, function(prefix, val) {
                    $(form).find('span.' + prefix + '_error').html(val.join('<br>'));
                });
            } else if (xhr.status == 500) {
                toastr.error('Internal Server Error. Please try again later.');
            } else if (xhr.status == 404) {
                toastr.error('Requested resource not found (404).');
            } else {
                toastr.error('An unexpected error occurred.');
            }
        },
        complete: function() {
            $('#loading').hide();
            $(form).find('button[type="submit"]').attr('disabled', false).text('SAVE');
        }
    });
});
