<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-dark">
            <h4 class="text-white">Orders Details</h4>
        </div>
        <div class="card-body">
            <h5>Current Orders</h5>
            <div class="order-items">
                <ul id="current-orders-list" class="list-group">
                    <!-- Pesanan akan muncul di sini -->
                </ul>
            </div>
            <hr>
            <div>
                <p><strong>Sub Total:</strong> <span id="subtotal">IDR 0.00</span></p>
                <p><strong>Tax:</strong> <span id="tax">IDR 0.00</span></p>
                <p><strong>Total:</strong> <span id="total">IDR 0.00</span></p>
            </div>
            <hr>
            <h5>Payment Method</h5>
            <form id="order-form" method="POST" action="{{ route('sales') }}">
                @csrf
                <input type="hidden" name="order_data" id="order-data">
                <div class="row ml-1 mb-2" >
                    <div class="form-check col-md-6">
                        <input class="form-check-input" type="radio" name="payment_method" id="cash" value="1">
                        <label class="form-check-label" for="cash">Cash</label>
                    </div>
                    <div class="form-check col-md-6">
                        <input class="form-check-input" type="radio" name="payment_method" id="transfer" value="2">
                        <label class="form-check-label" for="transfer">Transfer</label>
                    </div>
                </div>
            </form>
            <button id="proceed-to-payment" class="btn btn-warning btn-block">Proceed to Payment</button>
        </div>
    </div>
</div>
