let table = $("table#countries")
    .DataTable({
        processing: true,
        info: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        aLengthMenu: [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, "All"],
        ],
        ajax: "{{ route('countries') }}",
        columns: [
            {
                data: "checkbox",
                name: "checkbox",
                orderable: false,
                searchable: false,
            },
            {
                data: null,
                name: "index",
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
            },
            {
                data: "country_name",
                name: "country_name",
            },
            {
                data: "capital_city",
                name: "capital_city",
            },
            {
                data: "actions",
                name: "actions",
                orderable: false,
                searchable: false,
            },
        ],
    })
    .on("draw", function () {
        $('input[name="country_checkbox"]').prop("checked", false);
        $('input[name="main_checkbox"]').prop("checked", false);
        $("#multipleDeleteBtn").addClass("d-none");
    });
