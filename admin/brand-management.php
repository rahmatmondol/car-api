<?php

// Callback function to display the page content
function brand_page_content()
{

?>
    <div id="app">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1 class="p-2" id="page-title">brands Mangement </h1>
                </div>
            </div>
            <div class="row">
                <div class="col ">
                    <button id="bulk-delete-button" class="btn btn-sm btn-danger" style="display: none;">Delete</button>
                    <button id="add-brand-button" class="btn btn-sm btn-primary">Add Brand</button>
                    <button id="add-bulk-brand-button" class="btn btn-sm btn-primary">Add bulk Brand</button>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="wrap">
                        <table class="wp-list-table widefat fixed striped table-view-list posts" id="brands_table">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 40px;">
                                        <div class="form-check" style="margin: 0px 0px -10px 0px;">
                                            <input class="form-check-input" type="checkbox" value="all" id="select_all">
                                            <label class="form-check-label" for="all">
                                            </label>
                                        </div>
                                    </th>
                                    <th scope="col">ID</th>
                                    <th scope="col">brand</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody id="all_brands">
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
                    <h1 class="modal-title fs-5" id="add-model-lavel">Add Model</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <label for="name" class="form-label">Model Name</label>
                                <input type="text" class="form-control" id="brand-name">
                                <input type="hidden" class="form-control" id="brand-id">
                                <input type="hidden" class="form-control" id="model-id">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer brands">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button id="add-brand-save" class="btn btn-primary">Save</button>
                    <button id="update-brand-save" class="btn btn-primary" style="display: none;">Update</button>
                </div>
                <div class="modal-footer models" style="display: none;">
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
                                <input type="file" class="form-control" id="brand-fil">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="bulk-brand-save" class="btn btn-primary">Upload</button>
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

            const get_all_brands = (page) => {
                if ($.fn.dataTable.isDataTable('#brands_table')) {
                    $('#brands_table').DataTable().destroy();
                }
                $.ajax({
                    url: 'admin-ajax.php',
                    type: 'POST',
                    data: {
                        action: 'get_all_brands',
                        page
                    },
                    success: function(response) {
                        // console.log(response.pagination);
                        if (response.success) {
                            $('#select_all').prop('checked', false);
                            $('#bulk-delete-button').hide();
                            $('#all_brands').html(response.result);
                            $('#pagination').html(response.pagination);

                            $('#brands_table').DataTable({
                                ordering: false
                            });
                        } else {
                            $('#all_brands').html('Not Found');
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
                get_all_brands(page);
            });

            const get_all_models = (id) => {
                var brand = $('#brand-id').val()
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
            get_all_brands(1);

            // add brand madal
            $('body').on('click', '#add-brand-button', function() {
                $('#add-model-lavel').text('Add Brand');
                $('#brand-name').val('');

                $('#add-brand-save').show();
                $('#update-brand-save').hide();
                $('#add-model').modal('show');
            });

            //add brand
            $('#add-brand-save').click(function() {
                const name = $('#brand-name').val();
                if (name !== '') {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            action: 'brand_add',
                            data: {
                                name
                            }
                        },
                        success: function(response) {
                            if (response.success) {
                                get_all_brands();
                                $('#add-model').modal('hide');
                                $('#brand-name').val('');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                        }
                    });
                } else {
                    alert('Please input brand name');
                }
            });

            //bulk brand
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
                            get_all_brands();
                            $('#add-bulk-model').modal('hide');
                            $('#brand-name').val('');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error uploading file:', error);
                        // Handle error
                    }
                });
            });

            //bulk upload brand modal
            $('body').on('click', '#add-bulk-brand-button', function() {
                $('#add-bulk-model').modal('show');
            });

            //brand edit modal
            $('body').on('click', '#brand-edit-button', function() {
                $('#add-model-lavel').text('Edit Brand');
                var id = $(this).attr('data-id');
                var name = $(this).attr('data-name');

                $('#brand-name').val(name);
                $('#brand-id').val(id);

                $('#add-brand-save').hide();
                $('#update-brand-save').show();
                $('#add-model').modal('show');
            });

            //update brand
            $('#update-brand-save').click(function() {
                const name = $('#brand-name').val();
                const id = $('#brand-id').val();
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        action: 'update_brand',
                        data: {
                            name,
                            id
                        }
                    },
                    success: function(response) {
                        console.log('Response:', response);
                        if (response.success) {
                            get_all_brands();
                            $('#add-model').modal('hide');
                            $('#brand-name').val('');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });

            //delete brand
            $('body').on('click', '#brand-delete-button', function() {
                var id = $(this).attr('data-id');
                if (confirm("Are you sure to delete this brand?")) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            action: 'delete_brand',
                            ids
                        },
                        success: function(response) {
                            if (response.success) {
                                get_all_brands();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                        }
                    });
                }
            });

            //bulk seleck
            $('body').on('change', '#select_all', function() {
                let all = $('.all_check');
                if ($(this).prop('checked')) {
                    all.prop('checked', true);
                } else {
                    all.prop('checked', false);
                    $('#bulk-delete-button').hide();
                }

                let ids = [];
                for (let index = 0; index < all.length; index++) {
                    if (all[index].checked) {
                        ids.push(all[index].value);
                    }
                }

                if (ids.length > 0) {
                    $('#bulk-delete-button').show().text(`Delete (${all.length})`);
                } else {
                    $('#bulk-delete-button').hide();
                }
            })

            //seleck
            $('body').on('change', '.all_check', function() {
                let all = $('.all_check');
                let ids = [];
                for (let index = 0; index < all.length; index++) {
                    if (all[index].checked) {
                        ids.push(all[index].value);
                    }
                }
                if (ids.length > 0) {
                    $('#bulk-delete-button').show().text(`Delete (${ids.length})`);
                } else {
                    $('#bulk-delete-button').hide();
                }

            })

            //bulk delete
            $('body').on('click', '#bulk-delete-button', function() {
                let all = $('.all_check');
                let ids = [];
                for (let index = 0; index < all.length; index++) {
                    if (all[index].checked) {
                        ids.push(all[index].value);
                    }
                }
                if (confirm("Are you sure to delete this brand?")) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            action: 'delete_brand',
                            ids
                        },
                        success: function(response) {
                            if (response.success) {
                                get_all_brands();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                        }
                    });
                }
            })

        });
    </script>
    <style>
        .form-check-input:checked {
            background: #fff !important;
        }
    </style>
<?php
}
