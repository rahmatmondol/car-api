<?php

function type_page_content()
{

?>
    <div id="app">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1 class="p-2" id="page-title">Type Mangement</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                    <button id="add-model-button" class="btn btn-sm btn-primary">Add Type</button>
                </div>
                <div class="col">
                    <select class="form-select" id="select_model" aria-label="Default select example">
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="wrap">
                        <table class="wp-list-table widefat fixed striped table-view-list posts">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Model</th>
                                    <th scope="col">Width</th>
                                    <th scope="col">Depth</th>
                                    <th scope="col">Inch</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody id="all_model_models">
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
                    <h1 class="modal-title fs-5" id="add-model-lavel">Add Type</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <select class="form-select mw-100" id="all_models" aria-label="Default select example">
                            </select>
                            <div class="mt-3">
                                <label for="name" class="form-label">Model Name</label>
                                <input type="text" class="form-control" id="model_name">
                                <input type="hidden" class="form-control" id="model_id">
                                <input type="hidden" class="form-control" id="id">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="mt-3">
                                <label for="width" class="form-label">Width</label>
                                <select class="form-select mw-100" id="width" aria-label="Default select example">
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mt-3">
                                <label for="depth" class="form-label">Depth</label>
                                <select class="form-select mw-100" id="depth" aria-label="Default select example">
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mt-3">
                                <label for="inch" class="form-label">Inch</label>
                                <select class="form-select mw-100" id="inch" aria-label="Default select example">
                                </select>
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


    <script>
        jQuery(document).ready(function($) {
            var url = '<?php echo admin_url('admin-ajax.php'); ?>';

            const get_all_types = (id = '') => {
                $.ajax({
                    url,
                    type: 'POST',
                    data: {
                        action: 'get_all_types',
                        id
                    },
                    success: function(response) {
                        // console.log(response.pagination);
                        if (response.success) {
                            $('#all_model_models').html(response.result);
                            $('#pagination').html(response.pagination);
                        } else {
                            $('#all_model_models').html('Not Found');
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
                get_all_types(page);
            });

            // get all models options
            const get_all_model_options = () => {
                var model = $('#model-id').val()
                $.ajax({
                    url: 'admin-ajax.php',
                    type: 'POST',
                    data: {
                        action: 'get_all_model_options',
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#all_models').html(response.result);
                            $('#select_model').html(response.result);
                        } else {
                            $('#all_models').html('<option value="">No models Found</option>');
                            $('#select_model').html('<option value="">No models Found</option>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }

            // get all width options
            const get_all_widht_options = () => {
                $.ajax({
                    url: 'admin-ajax.php',
                    type: 'POST',
                    data: {
                        action: 'get_all_width_options',
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#width').html(response.result);
                        } else {
                            $('#width').html('<option value="">No models Found</option>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }

            // get all depth options
            const get_all_depth_options = () => {
                $.ajax({
                    url: 'admin-ajax.php',
                    type: 'POST',
                    data: {
                        action: 'get_all_depth_options',
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#depth').html(response.result);
                        } else {
                            $('#depth').html('<option value="">No models Found</option>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }

            // get all inch options
            const get_all_inch_options = () => {
                $.ajax({
                    url: 'admin-ajax.php',
                    type: 'POST',
                    data: {
                        action: 'get_all_inch_options',
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#inch').html(response.result);
                        } else {
                            $('#inch').html('<option value="">No models Found</option>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }

            get_all_types();
            get_all_model_options();
            get_all_widht_options();
            get_all_depth_options();
            get_all_inch_options();

            // select
            $('body').on('change', '#select_model', function() {
                var model = $(this).val()
                get_all_types(model);
                $('#all_models').val(model)
            });

            // add model madal
            $('body').on('click', '#add-model-button', function() {
                $('#add-model-save').show();
                $('#model_name').val('');

                $('#width').val('');
                $('#inch').val('');
                $('#depth').val('');

                $('#update-model-save').hide();
                $('#add-model').modal('show');
            });

            //add model
            $('#add-model-save').click(function() {
                const name = $('#model_name').val();
                const model_name = $('#all_models option:selected').text();
                const model_id = $('#all_models').val();
                const width_name = $('#width option:selected').text();
                const width_id = $('#width').val();
                const inch_name = $('#inch option:selected').text();
                const inch_id = $('#inch').val();
                const depth_name = $('#depth option:selected').text();
                const depth_id = $('#depth').val();

                if (name !== '' && model_id !== '' && model_id !== '' && width_id !== '' && inch_id !== '' && depth_id !== '') {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            action: 'add_type',
                            data: {
                                name,
                                model_name,
                                model_id,
                                width_name,
                                width_id,
                                inch_name,
                                inch_id,
                                depth_name,
                                depth_id
                            }
                        },
                        success: function(response) {
                            if (response.success) {
                                get_all_types(model_id);
                                $('#add-model').modal('hide');
                                $('#model_name').val('');
                                $('#select_model').val(model_id);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                        }
                    });
                }else{
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
                            $('#model-model').val('');
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
            $('body').on('click', '#type-edit-button', function() {
                $('#add-model-lavel').text('Edit model');
                const id = $(this).attr('data-id');
                const name = $(this).attr('data-name');
                const model_id = $(this).attr('data-model');
                const width_id = $(this).attr('data-width');
                const inch_id = $(this).attr('data-inch');
                const depth_id = $(this).attr('data-depth');

                $('#all_models').val(model_id);
                $('#model_name').val(name);
                $('#model_id').val(id);
                $('#depth').val(depth_id);
                $('#width').val(width_id);
                $('#inch').val(inch_id);

                $('#add-model-save').hide();
                $('#update-model-save').show();
                $('#add-model').modal('show');
            });

            //update
            $('#update-model-save').click(function() {
                const name = $('#model_name').val();
                const model_name = $('#all_models option:selected').text();
                const model_id = $('#all_models').val();
                const id = $('#model_id').val();
                const width_name = $('#width option:selected').text();
                const width_id = $('#width').val();
                const inch_name = $('#inch option:selected').text();
                const inch_id = $('#inch').val();
                const depth_name = $('#depth option:selected').text();
                const depth_id = $('#depth').val();
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        action: 'update_type',
                        data: {
                            id,
                            name,
                            model_name,
                            model_id,
                            width_name,
                            width_id,
                            inch_name,
                            inch_id,
                            depth_name,
                            depth_id
                        }
                    },
                    success: function(response) {
                        console.log('Response:', response);
                        if (response.success) {
                            get_all_types(model_id);
                            $('#select_model').val(model_id);
                            $('#add-model').modal('hide');
                            $('#model-model').val('');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });

            //delete
            $('body').on('click', '#type-delete-button', function() {
                var id = $(this).attr('data-id');
                const model_id = $('#all-models').val();
                if (confirm("Are you sure to delete this model?")) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            action: 'delete_type',
                            id
                        },
                        success: function(response) {
                            if (response.success) {
                                get_all_types(model_id);
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
