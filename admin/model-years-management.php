<?php

function model_years_page_content()
{

?>
    <div id="app">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1 class="p-2" id="page-title">Model Years Mangement</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <select class="form-select" id="select_brand" aria-label="Default select example">
                    </select>
                </div>
                
                <div class="col-2">
                    <button id="add-brand-button" class="btn btn-sm btn-primary">Add Brand Years</button>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="wrap">
                        <table class="wp-list-table widefat fixed striped table-view-list posts" id="model_years_table">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Year</th>
                                    <th scope="col">brand</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody id="all_model_years">
                            </tbody>
                        </table>
                    </div>
                    <nav aria-label="Page navigation example" id="pagination">
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
                    <h1 class="modal-title fs-5" id="add-model-lavel">Add Model Years</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col">
                            <select class="form-select mw-100" id="all-brands" aria-label="Default select example">
                            </select>
                            <div class="mt-3">
                                <label for="name" class="form-label">Model Name</label>
                                <input type="number" step="1" min="1950" value="1960" class="form-control" id="model-year">
                                <input type="hidden" class="form-control" id="brand-id">
                                <input type="hidden" class="form-control" id="model-id">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button id="add-brand-save" class="btn btn-primary">Save</button>
                    <button id="update-brand-save" class="btn btn-primary" style="display: none;">Update</button>
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

            const get_all_model_years = (id = '', page = 1) => {
                if ($.fn.dataTable.isDataTable('#model_years_table')) {
                    $('#model_years_table').DataTable().destroy();
                }
                $.ajax({
                    url,
                    type: 'POST',
                    data: {
                        action: 'get_all_models_years',
                        id,
                        page
                    },
                    success: function(response) {
                        // console.log(response.pagination);
                        if (response.success) {
                            $('#all_model_years').html(response.result);
                            $('#pagination').html(response.pagination);
                            $('#model_years_table').DataTable({
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

            $('body').on('click', '.page-link', function(e) {
                e.preventDefault();
                var page = $(this).attr('href').split('paged=')[1];
                var brand = $(this).val()
                get_all_model_years(brand, page);
            });

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
                            $('#all-brands').html(response.result);
                            $('#select_brand').html(response.result);
                        } else {
                            $('#all-brands').html('<option value="">No brands Found</option>');
                            $('#select_brand').html('<option value="">No brands Found</option>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }

            //get all models
            get_all_model_years();
            //get all brands
            get_all_brands_options();

            // select brand
            $('body').on('change', '#select_brand', function() {
                var brand = $(this).val()
                get_all_model_years(brand);
                $('#all-brands').val(brand)
            });

            // add brand madal
            $('body').on('click', '#add-brand-button', function() {
                $('#add-model-lavel').text('Add Brand Years');
                $('#add-brand-save').show();
                $('#update-brand-save').hide();
                $('#add-model').modal('show');
            });

            //add brand
            $('#add-brand-save').click(function() {
                const name = $('#model-year').val();
                const brand_name = $('#all-brands option:selected').text();
                const brand_id = $('#all-brands').val();
                if (name !== '' && brand_id !== '') {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            action: 'add_model_year',
                            data: {
                                name,
                                brand_name,
                                brand_id
                            }
                        },
                        success: function(response) {
                            if (response.success) {
                                get_all_model_years(brand_id);
                                $('#select_brand').val(brand_id);
                                $('#add-model').modal('hide');
                                $('#model-year').val('');
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
            $('#bulk-brand-save').click(function() {
                var fileInput = document.getElementById('brand-fil');
                var files = fileInput.files[0];
                var formData = new FormData();
                formData.append('json_file', files);
                formData.append('action', 'brand_add');
                $.ajax({
                    url: url, // WordPress AJAX endpoint
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            get_all_model_years();
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
            $('body').on('click', '#add-bulk-brand-button', function() {
                $('#add-bulk-model').modal('show');
            });

            // edit modal
            $('body').on('click', '#year-edit-button', function() {
                $('#add-model-lavel').text('Edit model Years');
                var id = $(this).attr('data-id');
                var name = $(this).attr('data-name');
                var brand = $(this).attr('data-brand');

                $('#model-year').val(name);
                $('#brand-id').val(id);
                $('#all-brands').val(brand);

                $('#add-brand-save').hide();
                $('#update-brand-save').show();
                $('#add-model').modal('show');
            });

            //update
            $('#update-brand-save').click(function() {
                const name = $('#model-year').val();
                const brand_name = $('#all-brands option:selected').text();
                const brand_id = $('#all-brands').val();
                const id = $('#brand-id').val();

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        action: 'update_model_year',
                        data: {
                            id,
                            name,
                            brand_name,
                            brand_id
                        }
                    },
                    success: function(response) {
                        console.log('Response:', response);
                        if (response.success) {
                            $('#select_brand').val(brand_id);
                            get_all_model_years(brand_id);
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
                const brand_id = $('#all-brands').val();
                if (confirm("Are you sure to delete this brand?")) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            action: 'delete_brand',
                            id
                        },
                        success: function(response) {
                            if (response.success) {
                                get_all_model_years(brand_id);
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
