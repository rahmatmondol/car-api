<?php

// Callback function to display the page content
function product_inventory_page_content()
{

?>
    <div id="app">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1 class="p-2">Inventory Mangement </h1>
                </div>
            </div>

            <div class="row">
                <div class="col-3">
                    <select class="form-select" v-model="selected_category">
                        <option v-for="(category, index) in all_categories" :key="index" :value="category.id">{{ category.name }} ({{ category.count }})</option>
                    </select>
                </div>
                <div class="col-3">
                    <select class="form-select" aria-label="Default select example" v-model="stock_status">
                        <option value="outofstock">All Out of Stock</option>
                        <option value="instock">All In Stock</option>
                    </select>
                </div>
                <div class="col-3">
                    <button type="button" class="btn btn-sm btn-primary" @click="filter()">Filter</button>
                </div>
                <div class="col-3">
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#settings_modal">Settings</button>
                </div>
            </div>
            <div class="row">
                <div class="col">

                    <div class="wrap">

                        <table class="wp-list-table widefat fixed striped table-view-list posts">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Image</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">SKU</th>
                                    <th scope="col">Vendor Stock</th>
                                    <th scope="col">In Stock</th>
                                    <th scope="col">Sugested Price</th>
                                    <th scope="col">Categorys</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="product in products" :key="product.id">
                                    <th scope="row">{{ product.id }}</th>

                                    <td>
                                        <div class="row">
                                            <div class="col-6" v-for="image in product.images" :key="image.id">
                                                <img :src="image.thumbnail" width="50" class="rounded" alt="">
                                            </div>
                                        </div>
                                    </td>
                                    <td><a target="_blank" :href="product.permalink">{{ product.name }}</a></td>
                                    <td>{{ product.sku }}

                                    </td>
                                    <td>
                                        <div v-if="inProducts" v-for="(item, index) in inProducts" :key="index">
                                            <span class="badge text-bg-primary" v-if="item.sku == product.sku || item.sku == product.id">{{item.add_to_cart.maximum}}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge text-bg-primary" v-if="product.is_in_stock">{{product.add_to_cart.maximum}}</span>
                                        <span class="badge text-bg-danger" v-if="!product.is_in_stock">0</span>
                                    </td>

                                    <td v-html="product.price_html"></td>
                                    <td>
                                        <div v-for="category in product.categories" :key="category.id"><a href="">{{ category.name }}</a></div>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-dark" @click="view_product( product )" data-bs-toggle="modal" data-bs-target="#product_modal">import</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
            <div class="row" v-if="products.length > 19">
                <div class="col">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item" v-if="page > 1">
                                <p class="page-link" @click="back_page()">Previous</p>
                            </li>
                            <li class="page-item" v-for="pages in page">
                                <p class="page-link active" v-if="pages == page" @click="page(pages)">{{ pages }}</p>
                                <p class="page-link" v-if="pages !== page" @click="curent_page(pages)">{{ pages }}</p>
                            </li>
                            <li class="page-item" v-if="products.length !== pages">
                                <p class="page-link" @click="next_page()">next</p>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <!-- product import model -->
        <div class="modal fade modal-lg" id="product_modal" tabindex="-1" aria-labelledby="product_modal_Label" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <a target="_blank" :href="product.permalink">
                            <h1 class="modal-title fs-5" id="product_modal_Label">{{ product.name }}</h1>
                        </a>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- image section -->
                        <div class="row mb-3" v-if="product.images">
                            <div class="col" v-for="image in product.images" :key="image.id">
                                <img :src="image.thumbnail" class="rounded" alt="">
                            </div>
                        </div>
                        <!-- title section -->
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>

                                    <input type="text" class="form-control" id="title" aria-describedby="emailHelp" v-model="product.name">
                                    <div id="emailHelp" class="form-text">Leve as it is if you dont need to change</div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col">
                                <h5>Vendor</h5>
                                <span class="badge text-bg-primary" v-if="product.is_in_stock">in {{product.add_to_cart.maximum}} stock</span>
                                <span class="badge text-bg-danger" v-if="!product.is_in_stock">Out of Stock</span>
                                <hr>
                                <h5>Store</h5>
                                <span class="badge text-bg-primary" v-if="status">in {{status}} stock</span>
                                <span class="badge text-bg-danger" v-if="!status">Not In</span>
                            </div>
                        </div>
                        <hr>
                        <!-- category section -->
                        <div class="row">
                            <h5>Categories</h5>
                            <div class="col pt-4">
                                <div v-for="(api_cat, index) in product.categories" :key="index" v-if="product.categories">
                                    <span class="badge text-bg-primary">{{api_cat.name}}</span>
                                </div>
                            </div>
                            <div class="col pt-4">
                                <select class="form-select" multiple aria-label="Multiple select example" v-model="category">
                                    <option v-for="(cat, index) in categories" :key="index" :value="cat">{{cat.name}}</option>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <!-- price section -->
                        <div class="row">
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="cost_price" class="form-label">Cost Price </label>
                                    <a target="_blank" :href="product.permalink">Get cost price</a>
                                    <input type="text" class="form-control" id="cost_price" v-model="prices.cost_price" aria-describedby="emailHelp">
                                    <div id="emailHelp" class="form-text">Vendor Price</div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="suggested_price" class="form-label">Sugested Price</label>
                                    <input type="text" class="form-control" id="suggested_price" v-model="prices.suggested_price" aria-describedby="emailHelp" v-if="product.prices">
                                    <div id="emailHelp" class="form-text">From vendor</div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="regular_price" class="form-label">Regular Price</label>
                                    <input type="text" class="form-control" id="regular_price" v-model="prices.regular_price" aria-describedby="emailHelp" v-if="product.prices">
                                    <div id="emailHelp" class="form-text">Change if you want</div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="sale_price" class="form-label">Sale Price</label>
                                    <input type="text" class="form-control" id="sale_price" v-model="prices.sale_price" aria-describedby="emailHelp" v-if="product.prices">
                                    <div id="emailHelp" class="form-text">Set discount price</div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <!--Short description section -->
                        <div class="row">
                            <div class="col">
                                <h5>Short Description</h5>
                                <div class="short_description" v-html="product.short_description"></div>
                            </div>
                        </div>
                        <hr>
                        <!-- description section -->
                        <div class="row">
                            <div class="col">
                                <h5>Description</h5>
                                <div class="description" v-html="product.description"></div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <div class="alert alert-danger" v-if="error">
                            {{error}}
                        </div>
                        <div class="alert alert-success" role="alert" v-if="massage">
                            {{massage}}
                        </div>
                        <span class="badge text-bg-primary" v-if="product.is_in_stock">{{product.add_to_cart.maximum}} in stock</span>
                        <span class="badge text-bg-danger" v-if="!product.is_in_stock">Out of Stock</span>
                        <div class="alert alert-warning" role="alert" v-if="status">
                            This Product is already in stoke: {{status}} quantity
                        </div>
                        <button type="button" class="btn btn-primary" @click="update" v-if="status">Update</button>
                        <button type="button" class="btn btn-primary" @click="save" v-if="!status">Save It</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- inventory settings -->
        <div class="modal fade" id="settings_modal" tabindex="-1" aria-labelledby="settings_modal_Label" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <a target="_blank" :href="product.permalink">
                            <h1 class="modal-title fs-5" id="product_modal_Label">Settings</h1>
                        </a>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- url section -->
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="url" class="form-label">Vendor URL</label>
                                    <input type="url" class="form-control" id="url" aria-describedby="emailHelp" v-model="settins.vendor">
                                    <div id="emailHelp" class="form-text">Put vendor website url</div>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="cookie" class="form-label">Login Cookie</label>
                                    <input type="text" class="form-control" id="cookie" v-model="settins.cookie">
                                    <div id="emailHelp" class="form-text">Login Cookie</div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="key" class="form-label">API User key</label>
                                    <input type="text" class="form-control" id="key" aria-describedby="emailHelp" v-model="settins.user">
                                    <div id="emailHelp" class="form-text">Put your api key</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="secret" class="form-label">API Secret key</label>
                                    <input type="text" class="form-control" id="secret" aria-describedby="emailHelp" v-model="settins.key">
                                    <div id="emailHelp" class="form-text">Put your api Secret key</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" @click="setting_save">Save It</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script>
        const {
            createApp
        } = Vue

        createApp({
            data() {
                return {
                    settins: {
                        vendor: '<?php echo get_option('inventory_vendor') ?>',
                        cookie: '<?php echo get_option('inventory_cookie') ?>',
                        user: '<?php echo get_option('inventory_user') ?>',
                        key: '<?php echo get_option('inventory_key') ?>',
                    },
                    products: [],
                    inProducts: [],
                    page: 1,
                    stock_status: 'instock',
                    product: [],
                    categories: [],
                    selected_category: 650,
                    all_categories: [],
                    category: [],
                    api_url: '<?php echo site_url(); ?>/wp-json',
                    api_user: '<?php echo get_option('inventory_user') ?>',
                    api_pass: '<?php echo get_option('inventory_key') ?>',
                    loading: false,
                    error: '',
                    massage: '',
                    product_details: '',
                    status: '',
                    id: '',
                    prices: {
                        cost_price: 0,
                        suggested_price: 0,
                        regular_price: 0,
                        sale_price: '',
                    }

                }
            },
            methods: {
                setting_save() {
                    var requestOptions = {
                        method: 'post',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(this.settins),
                        redirect: 'follow'
                    };
                    fetch(`${this.api_url}/inventory/v1/settins`, requestOptions)
                        .then(response => response.json())
                        .then(result => {
                            window.location.reload()
                        })
                        .catch(error => {
                            console.error(error);
                        });
                },
                filter() {
                    this.page = 1;
                    this.getProducts()
                    this.getInnerProducts()
                },
                getProducts() {
                    this.products = [];
                    var requestOptions = {
                        method: 'GET',
                        redirect: 'follow',
                    };
                    fetch(`${this.api_url}/inventory/v1/products?page=${this.page}&category=${this.selected_category}&stock_status=${this.stock_status}`, requestOptions)
                        .then(response => response.json())
                        .then(result => this.products = result)
                        .catch(error => console.log('error', error));
                },
                getInnerProducts() {
                    this.products = [];
                    var requestOptions = {
                        method: 'GET',
                        redirect: 'follow',
                    };
                    fetch(`${this.api_url}/wc/store/products?per_page=100`, requestOptions)
                        .then(response => response.json())
                        .then(result => this.inProducts = result)
                        .catch(error => console.log('error', error));
                },
                get_all_categories() {
                    var requestOptions = {
                        method: 'GET',
                        redirect: 'follow',
                    };
                    fetch(`${this.api_url}/inventory/v1/products/categories`, requestOptions)
                        .then(response => response.json())
                        .then(result => this.all_categories = result)
                        .catch(error => console.log('error', error));
                },
                get_categories() {
                    var requestOptions = {
                        method: 'get',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                    };
                    fetch(`${this.api_url}/wc/store/products/categories?hide_empty=0`, requestOptions)
                        .then(response => response.json())
                        .then(result => {
                            if (result.length > 0) {
                                this.categories = result
                            }
                        })
                        .catch(error => {
                            this.error = error.message;
                        });
                },
                view_product(value) {
                    this.id = '';
                    this.error = '';
                    this.massage = '';
                    this.category = '';
                    this.prices = {
                        cost_price: '',
                        suggested_price: value.prices.regular_price.slice(0, -2),
                        regular_price: value.prices.regular_price.slice(0, -2),
                        sale_price: '',
                    }
                    this.product = value;
                    this.status = ''
                    this.get_in_product(value.sku == '' ? String(value.id) : value.sku);
                },
                next_page() {
                    this.page = this.page + 1;
                    this.getInnerProducts()
                    this.getProducts();
                },
                back_page() {
                    this.page = this.page - 1;
                    this.getInnerProducts()
                    this.getProducts();
                },
                curent_page(value) {
                    this.page = value;
                    this.getInnerProducts()
                    this.getProducts();
                },
                save() {
                    const credentials = `${this.api_user}:${this.api_pass}`;
                    const encodedCredentials = btoa(credentials);
                    let images = [];
                    for (let index = 0; index < this.product.images.length; index++) {
                        images.push({
                            "src": this.product.images[index].src,
                            "alt": this.product.images[index].alt,
                            "name": this.product.images[index].name,
                        })
                    }

                    const Product = {
                        "name": this.product.name,
                        "type": this.product.type,
                        "regular_price": this.prices.regular_price,
                        "sale_price": this.prices.regular_price,
                        "description": this.product.description,
                        "short_description": this.product.short_description,
                        "sku": this.product.sku == '' ? String(this.product.id) : this.product.sku,
                        "categories": this.category,
                        "tags": this.product.tags,
                        "images": images,
                        "stock_status": this.product.is_in_stock ? 'outofstock' : 'instock',
                        "manage_stock": true,
                        "stock_quantity": this.product.add_to_cart.maximum == 9999 ? 0 : this.product.add_to_cart.maximum,
                        "attributes": this.product.attributes,
                        "meta_data": [{
                                "key": "cost_price",
                                "value": this.prices.cost_price
                            },
                            {
                                "key": "product_url",
                                "value": this.product.permalink
                            },
                            {
                                "key": "suplyer_name",
                                "value": "dropshopbd"
                            }

                        ]
                    }

                    var requestOptions = {
                        method: 'post',
                        headers: {
                            'Authorization': `Basic ${encodedCredentials}`,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(Product),
                        redirect: 'follow'
                    };
                    fetch(`${this.api_url}/wc/v3/products`, requestOptions)
                        .then(response => response.json())
                        .then(result => {
                            if (result.sku == this.product.sku || result.sku == this.product.id) {
                                this.massage = 'Product imported successfully';
                                this.products = [];
                                this.inProducts = [];
                                this.getInnerProducts()
                                this.getProducts();
                            } else {
                                this.error = result.message;
                            }
                        })
                        .catch(error => {
                            this.loading = true;
                            this.error = error.message;
                        });
                },
                get_in_product(sku) {
                    var requestOptions = {
                        method: 'get',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                    };
                    if (sku) {
                        fetch(`${this.api_url}/wc/store/products?sku=${sku}`, requestOptions)
                            .then(response => response.json())
                            .then(result => {
                                if (result.length > 0) {
                                    this.id = result[0].id;
                                    if (result[0].add_to_cart.maximum < 9999) {
                                        this.status = result[0].add_to_cart.maximum;
                                    } else {
                                        this.status = '0';
                                    }
                                }
                            })
                            .catch(error => {
                                this.error = error.message;
                            });
                    } else {
                        this.status = false;
                    }

                },
                update() {
                    const credentials = `${this.api_user}:${this.api_pass}`;
                    const encodedCredentials = btoa(credentials);

                    const Product = {
                        "regular_price": this.prices.regular_price,
                        "sale_price": this.prices.regular_price,
                        "stock_status": this.product.is_in_stock ? 'outofstock' : 'instock',
                        "stock_quantity": this.product.add_to_cart.maximum == 9999 ? 0 : this.product.add_to_cart.maximum,
                    }

                    var requestOptions = {
                        method: 'PUT',
                        headers: {
                            'Authorization': `Basic ${encodedCredentials}`,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(Product),
                        redirect: 'follow'
                    };
                    fetch(`${this.api_url}/wc/v3/products/${this.id}`, requestOptions)
                        .then(response => response.json())
                        .then(result => {
                            this.massage = 'Product updated successfully';
                        })
                        .catch(error => {
                            this.loading = true;
                            this.error = error.message;
                        });
                }
            },
            mounted() {
                this.get_all_categories()
                this.getInnerProducts()
                this.getProducts();
                this.get_categories()
            },
        }).mount('#app')
    </script>
<?php
}
