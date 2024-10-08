<?php

// Callback function to display the page content
function api_settings_page_content()
{

?>
    <div id="app">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1 class="p-2" id="page-title">APi Acount Settings</h1>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">API acount setting</h5>
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="api_url" class="form-label">Vendor URL</label>
                                        <input type="url" class="form-control" id="api_url" value="<?php echo get_option('api_url') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="api_user_name" class="form-label">User Name</label>
                                        <input type="text" class="form-control" id="api_user_name" value="<?php echo get_option('api_user_name') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="api_password" class="form-label">Password</label>
                                        <input type="text" class="form-control" id="api_password" value="<?php echo get_option('api_password') ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="list_price" class="form-label">Incress price list in persantage (%)</label>
                                        <input type="number" class="form-control" id="list_price" value="<?php echo get_option('list_price') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col">
                                    <div class="spinner-border" role="status" style="display: none;">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <button type="button" class="btn btn-primary" id="save_setting">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        jQuery(document).ready(function($) {
            const ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>'

            $('#save_setting').on('click', function() {
                $('.spinner-border').show();
                $(this).hide();
                var url = $('#api_url').val();
                var user_name = $('#api_user_name').val();
                var password = $('#api_password').val();
                var list_price = $('#list_price').val();
                $.ajax({
                    url: ajax_url,
                    type: 'POST',
                    data: {
                        action: 'inventory_settings_save',
                        data: {
                            url,
                            user_name,
                            password,
                            list_price
                        }
                    },
                    success: function(response) {
                        if (response.success) {
                            $('.spinner-border').hide();
                            $('#save_setting').text('Setting saved');
                            $('#save_setting').show();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });

        });
    </script>
<?php
}
