<div class="card">
    <h3 class="card-header">
        IPG Checkout
    </h3>
    <div class="card-body">
        <div class="card-text">
            <p><b>Checkout ID</b> <br /> {$checkout_id} </p>
            <p><b>Trace ID</b> <br /> {$trace_id} </p>
            <p><b>Payment Method Type</b> <br /> {$paymentMethodType} </p>
            <p><b>Order ID</b> <br /> {$status['orderId']} </p>
            <p><b>Transaction TYPE</b> <br /> {$status['transactionType']} </p>
            <p><b>Subtotal</b> <br /> {$status['approvedAmount']['components']['subtotal']} {$status['approvedAmount']['currency']} </p>
            <p><b>Shipping</b> <br /> {$status['approvedAmount']['components']['shipping']} {$status['approvedAmount']['currency']} </p>
            <p><b>Transaction Status</b> <br /> {$status['transactionStatus']} </p>
            <p><b>IPG Transaction ID</b> <br /> {$status['ipgTransactionDetails']['ipgTransactionId']} </p>
            <p><b>Approval Code</b> <br /> {$status['ipgTransactionDetails']['approvalCode']} </p>
            <p><b>API Trace ID</b> <br /> {$status['ipgTransactionDetails']['apiTraceId']} </p>
        </div>
    </div>
</div>