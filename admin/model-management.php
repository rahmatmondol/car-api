<?php

function model_page_content()
{

?>
    <div id="app">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1 class="p-2" id="page-title">Model Mangement</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-3">
                    <select class="form-select select_brand" aria-label="Default select example">
                    </select>
                </div>
                <div class="col-3">
                    <select class="form-select" id="select_year" aria-label="Default select example">
                    </select>
                </div>
                <div class="col-2">
                    <button id="add-model-button" class="btn btn-sm btn-primary">Add Model</button>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="wrap">
                        <table class="wp-list-table widefat fixed striped table-view-list posts" id="models_table">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">brand</th>
                                    <th scope="col">Year</th>
                                    <th scope="col">model</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody id="all_model_years">
                            </tbody>
                        </table>
                    </div>
                    <nav aria-label="Page navigation example" id="pagination" data-page="">
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!--add Modal -->
    <div class="modal fade" id="add-model" tabindex="-1" aria-labelledby="add-model-lavel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="add-model-lavel">Add Model</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <select class="form-select mw-100 select_brand" aria-label="Default select example">
                            </select>
                            <select class="form-select mw-100 mt-3" id="all_years" aria-label="Default select example">
                            </select>
                            <div class="mt-3">
                                <label for="name" class="form-label">Model Name</label>
                                <input type="text" class="form-control" id="model_name">
                                <input type="hidden" class="form-control" id="model_id">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button id="add-model-save" class="btn btn-primary">Save</button>
                    <button id="update-model-save" class="btn btn-primary" style="display: none;">Update</button>
                </div>
            </div>
        </div>
    </div>

    <!--bulk Modal -->
    <div class="modal fade" id="add-bulk-model" tabindex="-1" aria-labelledby="add-model-lavel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="add-model-lavel">Add bulk Model</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <label for="name" class="form-label">json file</label>
                                <input type="file" class="form-control" id="model-fil">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="bulk-model-save" class="btn btn-primary">Upload</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src=" https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
    <script>
        jQuery(document).ready(function($) {
            var url = '<?php echo admin_url('admin-ajax.php'); ?>';

            // get all model
            const get_all_model = (id = '', page = 1) => {
                if ($.fn.dataTable.isDataTable('#models_table')) {
                    $('#models_table').DataTable().destroy();
                }
                $.ajax({
                    url,
                    type: 'POST',
                    data: {
                        action: 'get_all_models',
                        id,
                        page
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#all_model_years').html(response.result);
                            $('#pagination').html(response.pagination);
                            $('#pagination').attr('data-id', 1);
                            $('#models_table').DataTable({
                                ordering: false
                            });
                        } else {
                            $('#all_model_years').html('Not Found');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }

            // get all brands
            const get_all_brands_options = () => {
                var brand = $('#brand-id').val()

                $.ajax({
                    url: 'admin-ajax.php',
                    type: 'POST',
                    data: {
                        action: 'get_all_brands_options',
                    },
                    success: function(response) {
                        if (response.success) {
                            $('.select_brand').html(response.result);
                        } else {
                            $('.select_brand').html('<option value="">No brands Found</option>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }

            // get all years
            const get_all_year_options = (id = '') => {
                var model = $('#model-id').val()
                $.ajax({
                    url: 'admin-ajax.php',
                    type: 'POST',
                    data: {
                        action: 'get_all_year_options',
                        id
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#all_years').html(response.result);
                            $('#select_year').html(response.result);
                        } else {
                            $('#all_years').html('<option value="">No models Found</option>');
                            $('#select_year').html('<option value="">No models Found</option>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }

            //get all models
            get_all_model();
            //get all models 
            get_all_year_options();
            //get all brands
            get_all_brands_options();

            //pagination
            $('body').on('click', '.page-link', function(e) {
                e.preventDefault();
                var page = $(this).attr('href').split('paged=')[1];
                $('#pagination').attr('data-page', page);
                var year = $('#all_years').val()
                get_all_model(year, page);
            });
            // select brand
            $('body').on('change', '.select_brand', function() {
                var id = $(this).val();
                $('.select_brand').val(id);
                get_all_year_options(id)
            });

            // select brand
            $('body').on('change', '#select_year', function() {
                var page = $('#pagination').attr('data-page');
                var year = $(this).val();
                get_all_model(year, page);
                $('#all_years').val(year)
            });

            // add model madal
            $('body').on('click', '#add-model-button', function() {
                $('#add-model-save').show();
                $('#model_name').val('');
                $('#update-model-save').hide();
                $('#add-model').modal('show');
            });

            //add model
            $('#add-model-save').click(function() {
                const name = $('#model_name').val();
                const year_name = $('#all_years option:selected').text();
                const year_id = $('#all_years').val();
                const brand_name = $('.select_brand option:selected')[0].innerText;
                const brand_id = $('.select_brand').val();
                if (name !== '' && year_id !== '') {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            action: 'add_model',
                            data: {
                                name,
                                year_name,
                                year_id,
                                brand_id,
                                brand_name
                            }
                        },
                        success: function(response) {
                            if (response.success) {
                                var page = $('#pagination').attr('data-page');
                                get_all_model(year_id, page);
                                $('#add-model').modal('hide');
                                $('#select_year').val(year_id);
                                $('.select_model').val(model_id);
                                $('#model_name').val('');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                        }

                    });
                } else {
                    alert('Dont leave unfilled');
                }
            });

            //bulk
            $('#bulk-model-save').click(function() {
                var fileInput = document.getElementById('model-fil');
                var files = fileInput.files[0];
                var formData = new FormData();
                formData.append('json_file', files);
                formData.append('action', 'model_add');
                $.ajax({
                    url: url, // WordPress AJAX endpoint
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            get_all_model();
                            $('#add-model').modal('hide');
                            $('#model-year').val('');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error uploading file:', error);
                        // Handle error
                    }
                });
            });

            //bulk upload modal
            $('body').on('click', '#add-bulk-model-button', function() {
                $('#add-bulk-model').modal('show');
            });

            // edit modal
            $('body').on('click', '#year-edit-button', function() {
                $('#add-model-lavel').text('Edit model');
                var id = $(this).attr('data-id');
                var name = $(this).attr('data-name');
                var year = $(this).attr('data-year');
                var brand = $(this).attr('data-brand');

                $('#all_years').val(year);
                $('#model_name').val(name);
                $('#model_id').val(id);
                $('.select_brand').val(brand);

                $('#add-model-save').hide();
                $('#update-model-save').show();
                $('#add-model').modal('show');
            });

            //update
            $('#update-model-save').click(function() {
                const name = $('#model_name').val();
                const year_name = $('#all_years option:selected').text();
                const year_id = $('#all_years').val();
                const brand_name = $('.select_brand option:selected')[0].innerText;
                const brand_id = $('.select_brand').val();
                const id = $('#model_id').val();

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        action: 'update_model',
                        data: {
                            id,
                            name,
                            year_name,
                            year_id,
                            brand_id,
                            brand_name
                        }
                    },
                    success: function(response) {
                        console.log('Response:', response);
                        if (response.success) {
                            get_all_model(year_id);
                            $('#select_year').val(year_id);
                            $('.select_brand').val(brand_id);
                            $('#add-model').modal('hide');
                            $('#model-year').val('');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });

            //delete
            $('body').on('click', '#year-delete-button', function() {
                var id = $(this).attr('data-id');
                const model_id = $('#all-models').val();
                if (confirm("Are you sure to delete this model?")) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            action: 'delete_model',
                            id
                        },
                        success: function(response) {
                            if (response.success) {
                                get_all_model(model_id);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                        }
                    });
                }
            });

        });
    </script>
<?php
}
