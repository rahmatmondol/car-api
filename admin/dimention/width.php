<?php

// Callback function to display the page content
function width_page_content()
{

?>
    <div id="app">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1 class="p-2" id="page-title">width Mangement </h1>
                </div>
            </div>
            <div class="row">
                <div class="col ">
                    <button id="add-width-button" class="btn btn-sm btn-primary">Add width</button>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="wrap">
                        <table class="wp-list-table widefat fixed striped table-view-list posts">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">width</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody id="all_widths">
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
                    <h1 class="modal-title fs-5" id="add-model-lavel">Add width</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <label for="name" class="form-label">width</label>
                                <input type="number" class="form-control" id="width-name">
                                <input type="hidden" class="form-control" id="width-id">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button id="add-width-save" class="btn btn-primary">Save</button>
                    <button id="update-width-save" class="btn btn-primary" style="display: none;">Update</button>
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
                                <input type="file" class="form-control" id="width-file">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="bulk-width-save" class="btn btn-primary">Upload</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        jQuery(document).ready(function($) {
            var url = '<?php echo admin_url('admin-ajax.php'); ?>';

            const get_all_width = (page) => {

                $.ajax({
                    url: 'admin-ajax.php',
                    type: 'POST',
                    data: {
                        action: 'get_all_width',
                        page
                    },
                    success: function(response) {
                        // console.log(response.pagination);
                        if (response.success) {
                            $('#all_widths').html(response.result);
                            $('#pagination').html(response.pagination);
                        } else {
                            $('#all_widths').html('Not Found');
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
                get_all_width(page);
            });

            //get all models
            get_all_width(1);

            // add width madal
            $('body').on('click', '#add-width-button', function() {
                $('#add-model-lavel').text('Add Depth');
                $('#width-name').val('');

                $('#add-width-save').show();
                $('#update-width-save').hide();
                $('#add-model').modal('show');
            });

            //add width
            $('#add-width-save').click(function() {
                const name = $('#width-name').val();
                if (name !== '') {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            action: 'width_add',
                            data: {
                                name
                            }
                        },
                        success: function(response) {
                            if (response.success) {
                                get_all_width();
                                $('#add-model').modal('hide');
                                $('#width-name').val('');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                        }
                    });
                } else {
                    alert('Please fill width');
                }
            });

            //bulk width
            $('#bulk-width-save').click(function() {
                var fileInput = document.getElementById('width-fil');
                var files = fileInput.files[0];
                var formData = new FormData();
                formData.append('json_file', files);
                formData.append('action', 'width_add');
                $.ajax({
                    url: url, // WordPress AJAX endpoint
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            get_all_width();
                            $('#add-model').modal('hide');
                            $('#width-name').val('');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error uploading file:', error);
                        // Handle error
                    }
                });
            });

            //bulk upload width modal
            $('body').on('click', '#add-bulk-width-button', function() {
                $('#add-bulk-model').modal('show');
            });

            //width edit modal
            $('body').on('click', '#width-edit-button', function() {
                $('#add-model-lavel').text('Edit Depth');
                var id = $(this).attr('data-id');
                var name = $(this).attr('data-name');

                $('#width-name').val(name);
                $('#width-id').val(id);

                $('#add-width-save').hide();
                $('#update-width-save').show();
                $('#add-model').modal('show');
            });

            //update width
            $('#update-width-save').click(function() {
                const name = $('#width-name').val();
                const id = $('#width-id').val();
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        action: 'update_width',
                        data: {
                            name,
                            id
                        }
                    },
                    success: function(response) {
                        console.log('Response:', response);
                        if (response.success) {
                            get_all_width();
                            $('#add-model').modal('hide');
                            $('#width-name').val('');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });

            //delete width
            $('body').on('click', '#width-delete-button', function() {
                var id = $(this).attr('data-id');
                if (confirm("Are you sure to delete this width?")) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            action: 'delete_width',
                            id
                        },
                        success: function(response) {
                            if (response.success) {
                                get_all_width();
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
