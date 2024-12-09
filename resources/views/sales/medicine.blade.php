<div class="card mb-3">
    <div class="card-header">
        <h5>Available Medicines</h5>
        <div class="card-header-right">
            <div class="btn-group card-option">
                <button type="button" class="btn dropdown-toggle btn-icon" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <i class="feather icon-more-horizontal"></i>
                </button>
                <ul class="list-unstyled card-option dropdown-menu dropdown-menu-right">
                    <li class="dropdown-item full-card"><a href="#!"><span><i
                                    class="feather icon-maximize"></i>
                                maximize</span><span style="display:none"><i class="feather icon-minimize"></i>
                                Restore</span></a>
                    </li>
                    <li class="dropdown-item minimize-card"><a href="#!"><span><i
                                    class="feather icon-minus"></i> collapse</span><span style="display:none"><i
                                    class="feather icon-plus"></i> expand</span></a></li>
                    <li class="dropdown-item reload-card"><a href="#!"><i
                                class="feather icon-refresh-cw"></i>
                            reload</a></li>
                    <li class="dropdown-item close-card"><a href="#!"><i class="feather icon-trash"></i>
                            remove</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Search Bar -->
        <form action="{{ route('sales') }}" method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control"
                    placeholder="Search products by name..." value="{{ request('search') }}">
                <button class="btn btn-primary" type="submit">
                    <i class="fe fe-search"></i> Search
                </button>
            </div>
        </form>

        <!-- Product List -->
        <div class="row">
            @forelse ($products as $product)
                <div class="col-md-4 mb-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <img src="{{ asset('storage/purchases/' . $product->purchase->image) }}"
                                alt="{{ $product->purchase->name }}" class="img-fluid mb-3"
                                style="max-height: 100px; object-fit: contain;">
                            <h6 class="card-title text-primary"><strong>{{ $product->purchase->name }}</strong>
                            </h6>
                            <p class="text-muted">Stock: {{ $product->purchase->quantity }}</p>
                            <p><strong>{{ AppSettings::get('app_currency', 'IDR') }}
                                    {{ number_format($product->price, 2) }}</strong></p>
                            <button class="btn btn-warning btn-sm add-to-cart" data-id="{{ $product->id }}"
                                data-name="{{ $product->purchase->name }}" data-price="{{ $product->price }}">
                                <i class="fa fa-plus"></i> Add
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-muted text-center">No products found.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>