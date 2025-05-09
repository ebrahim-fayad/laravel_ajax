
        let modal = $('#modal-form');

        $(document).on('click', 'button#edit', function() {
            let id = $(this).data('id');
            let url = "{{ route('get_country') }}";

            // Reset form & errors
            modal.find('form')[0].reset();
            modal.find('span.error-text').text('');
            let loader = modal.find('.modal-loader');

            $.get(url, {
                    id: id
                }, function(result) {
                    if (result && result.data) {
                        modal.find('input[name="country_id"]').val(result.data.id);
                        modal.find('input[name="country_name"]').val(result.data.country_name);
                        modal.find('input[name="capital_city"]').val(result.data.capital_city);
                        modal.modal('show');
                    } else {
                        toastr.error('Failed to load country data.');
                    }

                }, 'json')
                .fail(function(xhr) {
                    if (xhr.status === 404) {
                        toastr.error('Country not found (404).');
                    } else if (xhr.status === 500) {
                        toastr.error('Internal server error (500).');
                    } else {
                        toastr.error('Something went wrong while fetching data.');
                    }
                });
        });
        $('form#update_country_form').on('submit', function(e) {
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
                beforeSend: () => {
                    modal.find('.modal-loader').show();
                    $(form).find('.error-text').text('');
                    $(form).find('button[type="submit"]').attr('disabled', true).text('Updating...');
                },
                success: (data) => {
                    if (data.status == 1) {
                        $(form).trigger('reset');
                        toastr.success(data.message, 'Success', {
                            "closeButton": true,
                            "progressBar": true,
                            "positionClass": "toast-top-right",
                        });
                        if (typeof table !== 'undefined') {
                            table.ajax.reload(null, false);
                        };
                        modal.modal('hide');
                    };
                },
                error: (xhr) => {
                    modal.find('.modal-loader').hide();
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
                    };
                },
                complete: () => {
                    modal.find('.modal-loader').hide();
                    //  modal.modal('hide');
                    $(form).find('button[type="submit"]').attr('disabled', false).text('Save changes');
                }

            });
        });
  