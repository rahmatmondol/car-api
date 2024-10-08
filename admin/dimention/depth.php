<?php

// Callback function to display the page content
function depth_page_content()
{

?>
    <div id="app">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1 class="p-2" id="page-title">depth Mangement </h1>
                </div>
            </div>
            <div class="row">
                <div class="col ">
                    <button id="add-depth-button" class="btn btn-sm btn-primary">Add depth</button>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="wrap">
                        <table class="wp-list-table widefat fixed striped table-view-list posts">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Depth</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody id="all_depths">
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
                    <h1 class="modal-title fs-5" id="add-model-lavel">Add depth</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <label for="name" class="form-label">depth</label>
                                <input type="number" class="form-control" id="depth-name">
                                <input type="hidden" class="form-control" id="depth-id">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button id="add-depth-save" class="btn btn-primary">Save</button>
                    <button id="update-depth-save" class="btn btn-primary" style="display: none;">Update</button>
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
                                <input type="file" class="form-control" id="depth-fil">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="bulk-depth-save" class="btn btn-primary">Upload</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        jQuery(document).ready(function($) {
            var url = '<?php echo admin_url('admin-ajax.php'); ?>';

            const get_all_depths = (page) => {

                $.ajax({
                    url: 'admin-ajax.php',
                    type: 'POST',
                    data: {
                        action: 'get_all_depth',
                        page
                    },
                    success: function(response) {
                        // console.log(response.pagination);
                        if (response.success) {
                            $('#all_depths').html(response.result);
                            $('#pagination').html(response.pagination);
                        } else {
                            $('#all_depths').html('Not Found');
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
                get_all_depths(page);
            });

            const get_all_models = (id) => {
                var depth = $('#depth-id').val()
                $.ajax({
                    url: 'admin-ajax.php',
                    type: 'POST',
                    data: {
                        action: 'get_all_models',
                        id,
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#all_models').html(response.result);
                        } else {
                            $('#all_models').html('Not Found');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }

            //get all models
            get_all_depths(1);

            // add depth madal
            $('body').on('click', '#add-depth-button', function() {
                $('#add-model-lavel').text('Add Depth');
                $('#depth-name').val('');

                $('#add-depth-save').show();
                $('#update-depth-save').hide();
                $('#add-model').modal('show');
            });

            //add depth
            $('#add-depth-save').click(function() {
                const name = $('#depth-name').val();
                if (name !== '') {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            action: 'depth_add',
                            data: {
                                name
                            }
                        },
                        success: function(response) {
                            if (response.success) {
                                get_all_depths();
                                $('#add-model').modal('hide');
                                $('#depth-name').val('');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                        }
                    });
                } else {
                    alert('Please fill depth');
                }
            });

            //bulk depth
            $('#bulk-depth-save').click(function() {
                var fileInput = document.getElementById('depth-fil');
                var files = fileInput.files[0];
                var formData = new FormData();
                formData.append('json_file', files);
                formData.append('action', 'depth_add');
                $.ajax({
                    url: url, // WordPress AJAX endpoint
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            get_all_depths();
                            $('#add-model').modal('hide');
                            $('#depth-name').val('');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error uploading file:', error);
                        // Handle error
                    }
                });
            });

            //bulk upload depth modal
            $('body').on('click', '#add-bulk-depth-button', function() {
                $('#add-bulk-model').modal('show');
            });

            //depth edit modal
            $('body').on('click', '#depth-edit-button', function() {
                $('#add-model-lavel').text('Edit Depth');
                var id = $(this).attr('data-id');
                var name = $(this).attr('data-name');

                $('#depth-name').val(name);
                $('#depth-id').val(id);

                $('#add-depth-save').hide();
                $('#update-depth-save').show();
                $('#add-model').modal('show');
            });

            //update depth
            $('#update-depth-save').click(function() {
                const name = $('#depth-name').val();
                const id = $('#depth-id').val();
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        action: 'update_depth',
                        data: {
                            name,
                            id
                        }
                    },
                    success: function(response) {
                        console.log('Response:', response);
                        if (response.success) {
                            get_all_depths();
                            $('#add-model').modal('hide');
                            $('#depth-name').val('');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });

            //delete depth
            $('body').on('click', '#depth-delete-button', function() {
                var id = $(this).attr('data-id');
                if (confirm("Are you sure to delete this depth?")) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            action: 'delete_depth',
                            id
                        },
                        success: function(response) {
                            if (response.success) {
                                get_all_depths();
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
