<?php

// Callback function to display the page content
function product_inventory_page_content()
{
    $token      = get_option('token');
    $api_url    = get_option('api_url');
    $user_name  = get_option('api_user_name');
    $password   = get_option('api_password');
    if ($api_url == ''  && $user_name == '' && $password == '') {
        $url = admin_url('admin.php?page=settings');
        wp_redirect($url);
        exit;
    }
?>
    <div id="app">

        <div class="container">
            <div class="row">
                <div class="col">
                    <h1 class="p-2">Inventory Mangement </h1>
                </div>
            </div>

            <div class="row">
                <div class="col d-flex gap-2">
                    <div class="spinner-border" id="loading-bar" role="status" style="display: none;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-dark" style="display: none;" id="bulk_product_import">import</button>
                    <button type="button" class="btn btn-sm btn-primary" style="display: none;" id="bulk_product_update">update</button>
                    <select class="form-select" id="types">
                        <option value="Tire" selected>Tire</option>
                        <option value="Wheel">Wheel</option>
                        <option value="Accessory">Accessory</option>
                    </select>
                    <button type="button" class="btn btn-sm btn-primary" id="get_api_produts">filter</button>
                    <button type="button" class="btn btn-sm btn-warning" id="refresh" style="display: none;">Show Price</button>
                    <button type="button" class="btn btn-sm btn-warning" id="refresh_products">Refrash all products</button>
                    <h5><span class="badge text-bg-secondary total_count"></span></h5>
                </div>
            </div>
            <div class="row" style="margin: 10px 20px 6px 2px;">
                <div style="padding: 0; display:none" class="progress" role="progressbar" aria-label="Example with label" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar" id="progressBar" style="width:0%">0%</div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="wrap">
                        <table id="products_data_table" class="wp-list-table widefat fixed striped table-view-list posts">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 40px;">
                                        <div class="form-check" style="margin: 0px 0px -10px 0px;">
                                            <input class="form-check-input" type="checkbox" value="select_all" id="select_all">
                                            <label class="form-check-label" for="select_all">
                                            </label>
                                        </div>
                                    </th>
                                    <th scope="col">ID</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">image</th>
                                    <th scope="col">Brand</th>
                                    <th scope="col">Season</th>
                                    <th scope="col">type</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">List Price</th>
                                    <th scope="col">Stock</th>
                                    <th scope="col">Width</th>
                                    <th scope="col">Depth</th>
                                    <th scope="col">Inch</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody id="products">

                            </tbody>
                        </table>
                        <nav class="pt-2" id="pagination">
                        </nav>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src=" https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>

    <script>
        jQuery(document).ready(function($) {

            const ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>'
            const url = '<?php echo $api_url; ?>'
            let refrash = false;
            const user_name = '<?php echo $user_name; ?>'
            const password = '<?php echo $password; ?>'
            let api_products = [];
            let in_products = [];

            const consumerKey = 'ck_d046e8810271ff1e3a9bfac76a178df55bd551e0'
            const consumerSecret = 'cs_be7a59be4f3332f78637f62557f45b0a1cb3106d'
            const apiUrl = '<?php echo site_url(); ?>/wp-json/wc/v3/products';

            $('#refresh').click(function() {
                refrash = true;
                $.ajax({
                    url: ajax_url,
                    type: 'POST',
                    data: {
                        action: 'inventory_settings_save',
                        data: {
                            url,
                            user_name,
                            password,
                        }
                    },
                    success: function(response) {
                        if (response.success) {
                            get_api_products();
                            $('#refresh').hide();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });

            $('#refresh_products').click(function() {
                refrash = true;
                get_api_products();
            });

            const loading = '<tr class="placeholder-glow">' +
                '<td><span class="placeholder col-12 "></span></td>' +
                '<td><span class="placeholder col-12 "></span></td>' +
                '<td><span class="placeholder col-12"></span></td>' +
                '<td><span class="placeholder col-12"></span></td>' +
                '<td><span class="placeholder col-12"></span></td>' +
                '<td><span class="placeholder col-12"></span></td>' +
                '<td><span class="placeholder col-12"></span></td>' +
                '<td><span class="placeholder col-12"></span></td>' +
                '<td><span class="placeholder col-12"></span></td>' +
                '<td><span class="placeholder col-12"></span></td>' +
                '<td><span class="placeholder col-12"></span></td>' +
                '<td><span class="placeholder col-12"></span></td>' +
                '<td><span class="placeholder col-12"></span></td>' +
                '<td><span class="placeholder col-12"></span></td></tr>';

            //get all products from api
            const get_api_products = (page = 1) => {
                $('#products').html(loading);
                let ProductGroup = $('#types').val();
                $.ajax({
                    url: ajax_url,
                    type: 'POST',
                    cache: true,
                    data: {
                        action: 'inventory_get_products_form_api',
                        page,
                        ProductGroup,
                        refrash
                    },
                    success: function(response) {
                        // console.log(response);
                        if (response.success) {
                            if (response.result) {
                                refrash = false;
                                api_products = response.result;
                                in_products = response.in_products == false ? [] : response.in_products;
                                update_table_data(response.result);
                            } else {
                                refrash = true;
                                get_api_products();
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }
            get_api_products();

            const update_table_data = (products) => {

                if ($.fn.dataTable.isDataTable('#products_data_table')) {
                    $('#products_data_table').DataTable().destroy();
                }
                let product = '';
                products.forEach(function(value) {
                    if (value.Price == 0 && value.ListPrice == 0) {
                        $('#refresh').show();
                    }
                    let inport = '';

                    inport = `<button type="button"
                    class="import-${value.ProductNumber} 
                    btn btn-sm  
                    ${in_products.includes(value.ProductNumber) 
                    ? 'btn-primary' 
                    : 'btn-dark'}" 
                    data-id="${value.ProductNumber}"
                    id="${in_products.includes(value.ProductNumber) 
                    ? 'update' 
                    : 'import'}">`;

                    inport += in_products.includes(value.ProductNumber) ? 'Update' : 'Import';
                    inport += `</button>` +
                        `<div class="spinner-border" id="spinner-${value.ProductNumber}" role="status" style="display: none;">` +
                        `<span class="visually-hidden">Loading...</span>` +
                        `</div>`;


                    product += '<tr>' +
                        `<td><div class="form-check mt-2">` +
                        `<input class="form-check-input all_check" type="checkbox" value="${value.ProductNumber }" id="${value.ProductNumber }">` +
                        `<label class="form-check-label" for="${value.ProductNumber }"></label></div></td>` +
                        `<td>${value.ProductNumber }</td>` +
                        `<td>${value.Description} </td>` +
                        `<td><img src="${value.ImageUrl }" style="width: 51px;"/></td>` +
                        `<td>${value.Brand == null ? '-' : value.Brand}</td>` +
                        `<td>${value.Season == null ? '-' : value.Season}</td>` +
                        `<td>${value.TireType == null ? '-' : value.TireType}</td>` +
                        `<td>${value.Price == null ? '-' : 'SEK '+value.Price}</td>` +
                        `<td>${value.ListPrice == null ? '-' : 'SEK '+value.ListPrice}</td>` +
                        `<td>${value.AvailableStock == null ? '-' : value.AvailableStock}</td>` +
                        `<td>${value.Width == null ? '-' : value.Width}</td>` +
                        `<td>${value.Depth == null ? '-' : value.Depth}</td>` +
                        `<td>${value.Inch == null ? '-' : value.Inch}</td>` +
                        '<td>' +
                        inport +
                        '</td>' +
                        '</tr>';
                })

                $('#products').html(product);
                $('#products_data_table').DataTable({
                    ordering: false
                });
            }

            $('#get_api_produts').on('click', function() {
                get_api_products();
            })

            $('body').on('click', '.pagination .pagination-links', function(e) {
                e.preventDefault();
                let page = $(this).attr('data-page');
                get_api_products(page);
            })

            $('body').on('click', '.pagination .previous_page', function(e) {
                e.preventDefault();
                let page = $(this).attr('data-page');
                get_api_products(parseInt(page) - 1);
            })

            $('body').on('click', '.pagination .next-page', function(e) {
                e.preventDefault();
                let page = $(this).attr('data-page');
                get_api_products(parseInt(page) + 1);
            })

            //bulk seleck
            $('body').on('change', '#select_all', function() {
                let all = $('.all_check');
                let import_ids = [];
                let update_ids = [];

                if ($(this).prop('checked')) {
                    all.prop('checked', true);
                } else {
                    all.prop('checked', false);
                }


                for (let index = 0; index < all.length; index++) {
                    if (all[index].checked) {
                        let is_update = $(`.import-${all[index].value}`).text();
                        if (is_update == 'Update') {
                            update_ids.push(all[index].value);
                        } else {
                            import_ids.push(all[index].value);
                        }
                    }
                }

                if (import_ids.length > 0) {
                    $('#bulk_product_import').show().text(`Import (${import_ids.length})`);
                } else {
                    $('#bulk_product_import').hide();
                }

                if (update_ids.length > 0) {
                    $('#bulk_product_update').show().text(`Update (${update_ids.length})`);
                } else {
                    $('#bulk_product_update').hide();
                }

            })

            //seleck
            $('body').on('change', '.all_check', function() {
                let all = $('.all_check');
                let import_ids = [];
                let update_ids = [];

                for (let index = 0; index < all.length; index++) {
                    if (all[index].checked) {
                        let is_update = $(`.import-${all[index].value}`).text();
                        if (is_update == 'Update') {
                            update_ids.push(all[index].value);
                        } else {
                            import_ids.push(all[index].value);
                        }
                    }

                }
                if (import_ids.length > 0) {
                    $('#bulk_product_import').show().text(`Import (${import_ids.length})`);
                } else {
                    $('#bulk_product_import').hide();
                }

                if (update_ids.length > 0) {
                    $('#bulk_product_update').show().text(`Update (${update_ids.length})`);
                } else {
                    $('#bulk_product_update').hide();
                }


            })


            //single import
            $('body').on('click', '#import', function() {
                let id = $(this).attr('data-id');
                $(this).hide('slow');
                $(`#spinner-${id}`).show('slow');
                const products = api_products.filter(product => id.includes(product.ProductNumber));

                $.ajax({
                    url: ajax_url,
                    type: 'POST',
                    data: {
                        action: 'save_products',
                        products,
                    },
                    success: function(response) {
                        in_products = response.in_products
                        // update_table_data(api_products);
                        $('#select_all').prop('checked', false);
                        $('#bulk_product_import').hide();
                        $(`#spinner-${id}`).hide('slow');
                        $(`.import-${id}`).removeClass('btn-dark').addClass('btn-primary').text('Update').show('slow');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });

            })

            //single update
            $('body').on('click', '#update', function() {
                let id = $(this).attr('data-id');
                $(this).hide('slow');
                $(`#spinner-${id}`).show('slow');
                const products = api_products.filter(product => id.includes(product.ProductNumber));

                $.ajax({
                    url: ajax_url,
                    type: 'POST',
                    data: {
                        action: 'update_products',
                        products,
                    },
                    success: function(response) {
                        $('#select_all').prop('checked', false);
                        $('#bulk_product_import').hide();
                        $(`#spinner-${id}`).hide('slow');
                        $(`.import-${id}`).removeClass('btn-dark').addClass('btn-primary').text('Updated').show('slow');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });

            })

            //bulk import
            $('body').on('click', '#bulk_product_import', function() {
                $(this).hide('slow');
                $('#loading-bar').show('slow');
                let all = $('.all_check');
                let ids = [];
                for (let index = 0; index < all.length; index++) {
                    if (all[index].checked) {
                        let is_update = $(`.import-${all[index].value}`).text();
                        if (is_update == 'Import') {
                            ids.push(all[index].value);
                            $(`.import-${all[index].value}`).hide('slow');
                            $(`#spinner-${all[index].value}`).show();
                        }
                    }
                }
                let products = api_products.filter(product => ids.includes(product.ProductNumber));

                let currentIndex = 0;
                let totalProducts = products.length;

                function uploadProduct(index) {
                    if (index >= totalProducts) {
                        // All products uploaded
                        $('#select_all').prop('checked', false);
                        $('#loading-bar').hide('slow');
                        $('.spinner-border').hide();
                        $('.progress').hide();
                        all.prop('checked', false);
                        return;
                    }

                    let product = products[index];
                    let params = new URLSearchParams();
                    params.append('action', 'save_products');
                    for (let key in product) {
                        if (product.hasOwnProperty(key)) {
                            params.append(`products[0][${key}]`, product[key]);
                        }
                    }

                    $.ajax({
                        url: ajax_url,
                        type: 'POST',
                        data: params.toString(),
                        contentType: 'application/x-www-form-urlencoded',
                        success: function(response) {
                            let percentComplete = Math.round(((index + 1) / totalProducts) * 100);
                            $('#progressBar').css('width', percentComplete + '%');
                            $('#progressBar').text(percentComplete + '%');
                            $('.progress').show();
                            uploadProduct(index + 1);
                            let sku = response.sku
                            $(`#spinner-${sku}`).hide('slow');
                            $(`.import-${sku}`).removeClass('btn-dark').addClass('btn-primary').text('Update').show('slow');
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                            uploadProduct(index + 1);
                        }
                    });
                }

                uploadProduct(currentIndex);
            });

            //bulk update
            $('body').on('click', '#bulk_product_update', function() {
                $('#bulk_product_update').hide('slow');
                $('#loading-bar').show('slow');
                let all = $('.all_check');
                let ids = [];
                for (let index = 0; index < all.length; index++) {
                    if (all[index].checked) {
                        let is_update = $(`.import-${all[index].value}`).text();
                        if (is_update == 'Update') {
                            ids.push(all[index].value);
                            $(`.import-${all[index].value}`).hide('slow');
                            $(`#spinner-${all[index].value}`).show();
                        }
                    }
                }
                let products = api_products.filter(product => ids.includes(product.ProductNumber));

                let currentIndex = 0;
                let totalProducts = products.length;

                function uploadProduct(index) {
                    if (index >= totalProducts) {
                        // All products uploaded
                        $('#select_all').prop('checked', false);
                        $('#loading-bar').hide('slow');
                        $('.spinner-border').hide();
                        $('.progress').hide();
                        all.prop('checked', false);
                        return;
                    }

                    let product = products[index];
                    let params = new URLSearchParams();
                    params.append('action', 'update_products');
                    for (let key in product) {
                        if (product.hasOwnProperty(key)) {
                            params.append(`products[0][${key}]`, product[key]);
                        }
                    }

                    $.ajax({
                        url: ajax_url,
                        type: 'POST',
                        data: params.toString(),
                        contentType: 'application/x-www-form-urlencoded',
                        success: function(response) {
                            let percentComplete = Math.round(((index + 1) / totalProducts) * 100);
                            $('#progressBar').css('width', percentComplete + '%');
                            $('#progressBar').text(percentComplete + '%');
                            $('.progress').show();
                            uploadProduct(index + 1);
                            let sku = response.sku
                            $(`#spinner-${sku}`).hide('slow');
                            $(`.import-${sku}`).removeClass('btn-dark').addClass('btn-primary').text('Updated').show('slow');
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                            uploadProduct(index + 1);
                        }
                    });
                }

                uploadProduct(currentIndex);
            });

        });
    </script>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">

    <style>
        .form-check-input:checked {
            background: #fff !important;
        }
    </style>
<?php
}
