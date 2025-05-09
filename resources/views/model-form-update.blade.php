<!-- Modal -->
<div class="modal fade" id="modal-form" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
    data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <form class="modal-content" action="{{ route('update') }}" method="POST" id="update_country_form">
            <div class="text-center my-3 modal-loader" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            @method('PUT')
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Country</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @csrf
                <input type="hidden" name="country_id">
                <div class="form-group">
                    <label for="name">Country name</label>
                    <input type="text" class="form-control" id="name" name="country_name"
                        placeholder="Enter country name">
                    <span class="text-danger error-text country_name_error"></span>
                </div>
                <div class="form-group">
                    <label for="capital">Capital city</label>
                    <input type="text" class="form-control" id="capital" name="capital_city"
                        placeholder="Enter capital city">
                    <span class="text-danger error-text capital_city_error"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</div>
