@extends('layouts.app')

@push('page-css')
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('jambasangsang/assets/select2/css/select2.min.css') }}">
@endpush


@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-9 col-auto">
                    <div class="page-header-title">
                        <h3 class="m-b-10">Add Sales</h3>
                    </div>
                </div>
                <div class="col-sm-3 col">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="feather icon-home"></i>
                                Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Add Sales</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            @include('sales.medicine')

            <!-- Recent Sales -->
            <div class="card">
                <div class="card-header">
                    <h5>Added Sales</h5>
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
                    <div class="table-responsive">
                        <table id="datatable-export" class="table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>Medicine Name</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total Price</th>
                                    <th>Date</th>
                                    <th class="action-btn">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sales as $sale)
                                    @if (!empty($sale->product->purchase))
                                        <tr>
                                            <td>{{ $sale->product->purchase->name }}</td>
                                            <td>{{ $sale->quantity }}</td>
                                            <td>{{ AppSettings::get('app_currency', '$') }} {{ $sale->product->price }}
                                            </td>
                                            <td>{{ AppSettings::get('app_currency', '$') }} {{ $sale->total_price }}</td>
                                            <td>{{ date_format(date_create($sale->created_at), 'd M, Y') }}</td>
                                            <td>
                                                <div class="actions">
                                                    @can('update-sales')
                                                        @if ($sale->product->purchase->quantity != 0)
                                                            <a data-id="{{ $sale->id }}"
                                                                data-product="{{ $sale->product_id }}"
                                                                data-quantity="{{ $sale->quantity }}"
                                                                class="btn btn-sm btn-info editbtn" href="javascript:void(0);">
                                                                <i class="fe fe-pencil"></i> Edit
                                                            </a>
                                                        @else
                                                            <label class="badge badge-danger"> Out of Stock</label>
                                                        @endif
                                                    @endcan
                                                    @can('destroy-sale')
                                                        <a data-id="{{ $sale->id }}" href="javascript:void(0);"
                                                            class="btn btn-sm btn-danger deletebtn" data-toggle="modal">
                                                            <i class="fe fe-trash"></i> Delete
                                                        </a>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /Recent sales -->

        </div>
        @can('create-sales')
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Add Sale</h5>
                        <div class="card-header-right">
                            <a href="#" id="add_new" class="btn btn-primary float-right ">Add New</a>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('sales.create')
                    </div>
                </div>
            </div>
        @endcan
    </div>
    <!-- Delete Modal -->
    <x-modals.delete :route="'sales'" :title="'Product Sale'" />
    <!-- /Delete Modal -->

    <div class="modal fade" id="invoiceModal" tabindex="-1" role="dialog" aria-labelledby="invoiceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="invoiceModalLabel">Invoice</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="invoice-content">
                        <h4 class="text-center">Invoice</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody id="invoice-items">
                                <!-- Items akan diisi melalui JavaScript -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                                    <td id="invoice-subtotal">Rp. 0.00</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Tax (10%):</strong></td>
                                    <td id="invoice-tax">Rp. 0.00</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                    <td id="invoice-total">Rp. 0.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close"
                        id="close-invoice-modal">Close</button>
                    <button id="print-invoice" type="button" class="btn btn-primary">Print</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('page-js')
    <!-- Select2 js-->
    <script src="{{ asset('jambasangsang/assets/select2/js/select2.min.js') }}"></script>
    <script>
        document.getElementById('print-invoice').addEventListener('click', () => {
            const printContent = document.getElementById('invoice-content').innerHTML;
            const apotekName = "Apotek Diana ";
            const apotekAddress = "Desa Tanggeran, RT 005 / RW 01, Sruweng, Kebumen";
            const win = window.open('', '', 'width=900,height=700');
            win.document.write(`
                <html>
                    <head>
                        <title>Invoice</title>
                        <style>
                            /* Add your custom styles here */
                            body { font-family: Arial, sans-serif; }
                            .container { margin: 20px; }
                            .text-center { text-align: center; }
                            .table-responsive { overflow-x: auto; }
                            table { width: 100%; border-collapse: collapse; }
                            th, td { border: 1px solid #ddd; padding: 8px; }
                            th { background-color: #f2f2f2; }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            <h3 class="text-center">${apotekName}</h3>
                            <p class="text-center">${apotekAddress}</p>
                            <div class="table-responsive">
                                ${printContent}
                            </div>
                        </div>
                    </body>
                </html>
            `);
            win.document.close();
            win.print();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ordersList = document.getElementById('current-orders-list');

            // Function to update the totals
            function updateTotals() {
                let total = 0;

                document.querySelectorAll('#current-orders-list .total-price').forEach(priceElement => {
                    const priceText = priceElement.textContent.replace(/,/g, '');
                    total += parseFloat(priceText);
                });

                // Update the subtotal display
                document.querySelector('span#subtotal').textContent = `Rp. ${total.toLocaleString()}`;
                const tax = total * 0.1; // Example tax rate of 10%
                document.querySelector('span#tax').textContent = `Rp. ${tax.toLocaleString()}`;
                document.querySelector('span#total').textContent = `Rp. ${(total + tax).toLocaleString()}`;
            }

            // Event listener for "Add to Cart" buttons
            document.querySelectorAll('.add-to-cart').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-id');
                    const productName = this.getAttribute('data-name');
                    const productPrice = parseFloat(this.getAttribute('data-price'));

                    // Check if product already exists in the list
                    let existingItem = document.getElementById(`order-item-${productId}`);
                    if (existingItem) {
                        // If it exists, just increment the quantity
                        let quantityInput = existingItem.querySelector('.quantity-input');
                        let currentQuantity = parseInt(quantityInput.value);
                        quantityInput.value = currentQuantity + 1;

                        // Update the total price
                        let totalPriceElement = existingItem.querySelector('.total-price');
                        totalPriceElement.textContent =
                            `${(productPrice * (currentQuantity + 1)).toLocaleString()}`;
                        updateTotals();
                        return;
                    }

                    // If not, add the product to the list
                    const orderItem = document.createElement('li');
                    orderItem.id = `order-item-${productId}`;
                    orderItem.classList.add('list-group-item', 'd-flex', 'justify-content-between',
                        'align-items-center');
                    orderItem.innerHTML = `
                        <div class="d-flex align-items-center px-2 w-50">
                        ${productName}
                        </div>
                        <div class="quantity-controls d-flex align-items-center justify-content-center">
                        <button class="btn btn-secondary btn-sm decrement-quantity" data-id="${productId}" data-price="${productPrice}">-</button>
                        <input type="number" class="quantity-input mx-1 p-1 w-25" value="1" min="1" readonly>
                        <button class="btn btn-secondary btn-sm increment-quantity" data-id="${productId}" data-price="${productPrice}">+</button>
                        </div>
                        <strong><span class="total-price mx-2">${productPrice.toLocaleString()}</span></strong>
                        <button class="btn btn-danger btn-sm remove-item" data-id="${productId}">
                        <i class="fa fa-trash"></i>
                        </button>
                    `;

                    // Add to the list
                    ordersList.appendChild(orderItem);

                    // Event listener for the "Remove" button
                    orderItem.querySelector('.remove-item').addEventListener('click', function() {
                        document.getElementById(`order-item-${productId}`).remove();
                        updateTotals();
                    });

                    // Event listener for the "+" (increment) button
                    orderItem.querySelector('.increment-quantity').addEventListener('click',
                        function() {
                            let quantityInput = orderItem.querySelector('.quantity-input');
                            let currentQuantity = parseInt(quantityInput.value);
                            quantityInput.value = currentQuantity + 1;

                            // Update the total price
                            let totalPriceElement = orderItem.querySelector('.total-price');
                            totalPriceElement.textContent =
                                `${(productPrice * (currentQuantity + 1)).toLocaleString()}`;
                            updateTotals();
                        });

                    // Event listener for the "-" (decrement) button
                    orderItem.querySelector('.decrement-quantity').addEventListener('click',
                        function() {
                            let quantityInput = orderItem.querySelector('.quantity-input');
                            let currentQuantity = parseInt(quantityInput.value);

                            if (currentQuantity > 1) {
                                quantityInput.value = currentQuantity - 1;

                                // Update the total price
                                let totalPriceElement = orderItem.querySelector('.total-price');
                                totalPriceElement.textContent =
                                    `${(productPrice * (currentQuantity - 1)).toLocaleString()}`;
                                updateTotals();
                            }
                        });

                    // Update the totals
                    updateTotals();
                });
            });

            // Function to collect order data
            function collectOrderData() {
                let orders = [];
                document.querySelectorAll('#current-orders-list li').forEach(item => {
                    const productId = item.id.replace('order-item-', '');
                    const quantity = item.querySelector('.quantity-input').value;
                    orders.push({
                        productId,
                        quantity
                    });
                });
                return orders;
            }

            // Event listener for "Proceed to Payment" button
            document.getElementById('proceed-to-payment').addEventListener('click', function() {
                const orderItems = document.querySelectorAll('#current-orders-list .list-group-item');
                const invoiceItems = document.getElementById('invoice-items');
                invoiceItems.innerHTML = '';

                orderItems.forEach(item => {
                    const itemName = item.querySelector('div').textContent.trim();
                    const quantity = item.querySelector('.quantity-input').value;
                    const price = item.querySelector('.total-price').textContent.trim();

                    const row = `<tr>
                                <td>${itemName}</td>
                                <td>${quantity}</td>
                                <td>${price}</td>
                                <td>${price}</td>
                            </tr>`;
                    invoiceItems.innerHTML += row;
                });

                document.getElementById('invoice-subtotal').textContent = document.getElementById(
                    'subtotal').textContent;
                document.getElementById('invoice-tax').textContent = document.getElementById('tax')
                    .textContent;
                document.getElementById('invoice-total').textContent = document.getElementById('total')
                    .textContent;




                const orderData = collectOrderData();
                const paymentMethod = document.querySelector('input[name="payment_method"]:checked');

                if (!paymentMethod) {
                    alert('Please select a payment method!');
                    return;
                }

                const formData = new FormData();
                formData.append('_token', document.querySelector('input[name="_token"]').value);
                // Kirim order_data sebagai array
                formData.append('order_data', JSON.stringify(orderData));
                formData.append('payment_method', paymentMethod.value);

                // Debug untuk memastikan data terkirim dengan benar
                console.log('Sending data:', {
                    order_data: orderData,
                    payment_method: paymentMethod.value
                });

                fetch(document.getElementById('order-form').action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {

                            $('#invoiceModal').modal('show');
                        } else {
                            alert(data.message || 'There was an error placing the order.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('There was an error processing your request.');
                    });
            });

            // Tambahkan updateTotals di event listener add/remove
            document.querySelectorAll('.add-to-cart').forEach(button => {
                button.addEventListener('click', function() {
                    updateTotals();
                });
            });
            document.querySelectorAll('.remove-item').forEach(button => {
                button.addEventListener('click', function() {
                    updateTotals();
                });
            });

            $(document).ready(function() {
                $('#datatable-export').on('click', '.editbtn', function() {
                    event.preventDefault();
                    var id = $(this).data('id');
                    var product = $(this).data('product');
                    var quantity = $(this).data('quantity');
                    $('#edit_id').val(id);
                    $(".edit_product").val(product).trigger('change');
                    console.log(product)
                    $('.edit_quantity').val(quantity);
                    $('.btn-block').text("Update Changes");
                });

                $('#add_new').on('click', function() {
                    event.preventDefault();
                    $('#edit_id').val('');
                    $(".edit_product").val('').trigger('change');
                    $('.edit_quantity').val(1);
                    $('.btn-block').text("Save Changes");
                });
            });
        });
    </script>
    <script>
        document.getElementById('close-invoice-modal').addEventListener('click', () => {
            location.reload(); // Reload the page when the close button is clicked
        });
    </script>
@endpush
