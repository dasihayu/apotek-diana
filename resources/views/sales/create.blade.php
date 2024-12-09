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
            <button id="proceed-to-payment" class="btn btn-warning btn-block">Proceed to Payment</button>
        </div>
        <form id="order-form" method="POST" action="{{ route('sales') }}" style="display: none;">
            @csrf
            <input type="hidden" name="order_data" id="order-data">
        </form>
    </div>
</div>
