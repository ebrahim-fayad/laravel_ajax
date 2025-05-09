<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>World Countries</title>
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('datatables/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('toastr/toastr.min.css') }}">
    <style>
        table tbody td:last-child {
            text-align: right;
        }
    </style>
</head>

<body>


    <div class="container">

        <div class="col-md-12 text-center">
            <h2>World Countries</h2>
            <hr>
        </div><!-- end col-12 -->
        <div class="row" style="margin-top: 45px;">
            <div class="col-md-8">

                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>World Countries</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-condensed table-sm" id="countries">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" name="main_checkbox"></th>
                                        <th>#</th>
                                        <th>Country Name</th>
                                        <th>Capital City</th>
                                        <th class="text-right">
                                            <button class="btn btn-danger btn-sm d-none"
                                                id="multipleDeleteBtn">Delete</button>
                                        </th>
                                    </tr>
                                </thead>
                            </table>

                        </div><!-- end table-responsive -->

                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col-8 -->

            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>Countries</h4>
                    </div>
                    <div class="card-body">
                        <div id="loading" class="text-center my-3" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                {{-- <span class="visually-hidden">Loading...</span> --}}
                            </div>
                        </div>
                        <form action="{{ route('store') }}" method="POST" id="store_country_form">
                            @csrf
                            <div class="form-group">
                                <label for="name">Country name</label>
                                <input type="text" id="name" name="country_name" class="form-control"
                                    placeholder="Enter country name" value="{{ old('country_name') }}">
                                <span class="text-danger error-text country_name_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="capital">Capital city</label>
                                <input type="text" id="capital" name="capital_city" class="form-control"
                                    placeholder="Enter capital city" value="{{ old('capital_city') }}">
                                <span class="text-danger error-text capital_city_error"></span>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-block btn-success">SAVE</button>
                            </div>
                        </form>
                    </div><!-- end card body -->
                </div><!-- end card -->

            </div><!-- end col-4 -->

        </div><!-- end row -->
    </div><!-- end of container -->
    @include('model-form-update')
    <script src="{{ asset('jquery/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('toastr/toastr.min.js') }}"></script>
    <script>
        toastr.options.preventDuplicates = true;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <!-- ajax for store form -->
    <script src="{{ asset('ajax/form.js') }}"></script>
    <!-- getting data for table -->
    <script>
        let table = $('table#countries').DataTable({
            processing: true,
            info: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            aLengthMenu: [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "All"]
            ],
            ajax: "{{ route('countries') }}",
            columns: [{
                    data: 'checkbox',
                    name: 'checkbox',
                    orderable: false,
                    searchable: false,
                }, {
                    data: null,
                    name: 'index',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'country_name',
                    name: 'country_name'
                },
                {
                    data: 'capital_city',
                    name: 'capital_city'
                },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                }
            ],
        }).on('draw', function() {
            $('input[name="country_checkbox"]').prop('checked', false);
            $('input[name="main_checkbox"]').prop('checked', false);
            $('#multipleDeleteBtn').addClass('d-none');
        });
    </script>
    <!-- update -->
    <script>
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
    </script>
    <!-- delete single record -->
    <script>
        $(document).on('click', 'button#deleteButton', function() {
            let id = $(this).data('id');
            let url = "{{ route('delete') }}";

            swal.fire({
                title: 'Are you sure?',
                html: 'You want to delete selected country.',
                showCancelButton: true,
                showCloseButton: true,
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#556ee6',
                cancelButtonColor: '#d33',
                width: 300,
                allowOutsideClick: false
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            id: id,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(data) {
                            if (data.status == 1) {
                                toastr.success(data.message, 'Success', {
                                    "closeButton": true,
                                    "progressBar": true,
                                    "positionClass": "toast-top-right",
                                });
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
                            if (xhr.status == 404) {
                                toastr.error('Country not found (404).');
                            } else if (xhr.status == 500) {
                                toastr.error('Internal server error (500).');
                            } else {
                                toastr.error('Something went wrong while deleting country.');
                            }
                        }
                    });
                }
            });
        });
    </script>
    <!-- delete multiple records -->
    <script>
        $(document).on('click', 'input[type ="checkbox"][name="main_checkbox"]', function() {
            let checked = $(this).is(':checked');
            $('input[name="country_checkbox"]').prop('checked', checked);
            toggleDeleteButton();
        });
        $(document).on('change', 'input[type="checkbox"][name="country_checkbox"]', function() {
            let checked = $('input[name="country_checkbox"]:checked').length;
            if ($('input[name="country_checkbox"]').length == checked) {
                $('input[name="main_checkbox"]').prop('checked', true);
            } else {
                $('input[type ="checkbox"][name="main_checkbox"]').prop('checked', false);
            }
            toggleDeleteButton();
        });

        function toggleDeleteButton() {
            let checked = $('input[name="country_checkbox"]:checked').length;
            if (checked > 0) {
                $('#multipleDeleteBtn').text('Delete(' + checked + ')').removeClass('d-none');
            } else {
                $('#multipleDeleteBtn').addClass('d-none');
            }
        };
        $(document).on('click','button#multipleDeleteBtn',function(){
            let ids=[];
            $('input[type="checkbox"][name="country_checkbox"]:checked').each(function(){
                ids.push($(this).data('id'));
            });
            let url = "{{ route('multiple_delete') }}";
           if (ids.length >0) {
            swal.fire({
                title: 'Are you sure?',
                html: 'You want to delete selected countries.',
                showCancelButton: true,
                showCloseButton: true,
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#556ee6',
                cancelButtonColor: '#d33',
                width: 300,
                allowOutsideClick: false
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            ids: ids,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(data) {
                            if (data.status == 1) {
                                toastr.success(data.message, 'Success', {
                                    "closeButton": true,
                                    "progressBar": true,
                                    "positionClass": "toast-top-right",
                                });
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
                            if (xhr.status == 404) {
                                toastr.error('Countries not found (404).');
                            } else if (xhr.status == 500) {
                                toastr.error('Internal server error (500).');
                            } else {
                                toastr.error('Something went wrong while deleting countries.');
                            }
                        }
                    });
                }
            });
           };
        });
    </script>
</body>

</html>
