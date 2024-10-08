<?php

// Callback function to display the page content
function inch_page_content()
{

?>
    <div id="app">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1 class="p-2" id="page-title">inch Mangement </h1>
                </div>
            </div>
            <div class="row">
                <div class="col ">
                    <button id="add-inch-button" class="btn btn-sm btn-primary">Add inch</button>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="wrap">
                        <table class="wp-list-table widefat fixed striped table-view-list posts">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Inch</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody id="all_inchs">
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
                    <h1 class="modal-title fs-5" id="add-model-lavel">Add inch</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <label for="name" class="form-label">Inch</label>
                                <input type="number" class="form-control" id="inch-name">
                                <input type="hidden" class="form-control" id="inch-id">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button id="add-inch-save" class="btn btn-primary">Save</button>
                    <button id="update-inch-save" class="btn btn-primary" style="display: none;">Update</button>
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
                                <input type="file" class="form-control" id="inch-fil">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="bulk-inch-save" class="btn btn-primary">Upload</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        jQuery(document).ready(function($) {
            var url = '<?php echo admin_url('admin-ajax.php'); ?>';

            const get_all_inches = (page) => {

                $.ajax({
                    url: 'admin-ajax.php',
                    type: 'POST',
                    data: {
                        action: 'get_all_inches',
                        page
                    },
                    success: function(response) {
                        // console.log(response.pagination);
                        if (response.success) {
                            $('#all_inchs').html(response.result);
                            $('#pagination').html(response.pagination);
                        } else {
                            $('#all_inchs').html('Not Found');
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
                get_all_inches(page);
            });


            //get all models
            get_all_inches(1);

            // add inch madal
            $('body').on('click', '#add-inch-button', function() {
                $('#add-model-lavel').text('Add Inch');
                $('#inch-name').val('');

                $('#add-inch-save').show();
                $('#update-inch-save').hide();
                $('#add-model').modal('show');
            });

            //add inch
            $('#add-inch-save').click(function() {
                const name = $('#inch-name').val();
                if (name !== '') {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            action: 'add_inch',
                            data: {
                                name
                            }
                        },
                        success: function(response) {
                            if (response.success) {
                                get_all_inches();
                                $('#add-model').modal('hide');
                                $('#inch-name').val('');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                        }
                    });
                } else {
                    alert('Please fill inch');
                }
            });

            //bulk inch
            $('#bulk-inch-save').click(function() {
                var fileInput = document.getElementById('inch-fil');
                var files = fileInput.files[0];
                var formData = new FormData();
                formData.append('json_file', files);
                formData.append('action', 'inch_add');
                $.ajax({
                    url: url, // WordPress AJAX endpoint
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            get_all_inchs();
                            $('#add-model').modal('hide');
                            $('#inch-name').val('');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error uploading file:', error);
                        // Handle error
                    }
                });
            });

            //bulk upload inch modal
            $('body').on('click', '#add-bulk-inch-button', function() {
                $('#add-bulk-model').modal('show');
            });

            //inch edit modal
            $('body').on('click', '#inch-edit-button', function() {
                $('#add-model-lavel').text('Edit Inch');
                var id = $(this).attr('data-id');
                var name = $(this).attr('data-name');

                $('#inch-name').val(name);
                $('#inch-id').val(id);

                $('#add-inch-save').hide();
                $('#update-inch-save').show();
                $('#add-model').modal('show');
            });

            //update inch
            $('#update-inch-save').click(function() {
                const name = $('#inch-name').val();
                const id = $('#inch-id').val();
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        action: 'update_inch',
                        data: {
                            name,
                            id
                        }
                    },
                    success: function(response) {
                        console.log('Response:', response);
                        if (response.success) {
                            get_all_inches();
                            $('#add-model').modal('hide');
                            $('#inch-name').val('');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });

            //delete inch
            $('body').on('click', '#inch-delete-button', function() {
                var id = $(this).attr('data-id');
                if (confirm("Are you sure to delete this inch?")) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            action: 'delete_inch',
                            id
                        },
                        success: function(response) {
                            if (response.success) {
                                get_all_inches();
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
