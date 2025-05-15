<div class="container">
        <div class="order-details">
            <h2>Order Details</h2>
            <div class="details-details">
                <p><strong>Order ID:</strong> <?= $orderDetails['order_id'] ?></p>
                <p><strong>Customer Name:</strong> <?= $orderDetails['customer_name'] ?></p>
                <p><strong>Email:</strong> <?= $orderDetails['email'] ?></p>
                <p><strong>Date:</strong> <?= $orderDetails['created_at'] ?></p>
                <p><strong>Ticket Amount:</strong> <?= count($tickets) ?></p>
                <p><strong>Payment Method:</strong> <?= ucfirst($orderDetails['payment_method']) ?></p>
                <p><strong>Total Amount:</strong> $<?= $orderDetails['total'] ?></p>
            </div>
        </div>
        <hr>
        <div class="tickets-container">
            <?php foreach($tickets as $ticket): ?>
                <div class="ticket">
                    <img src="<?= $ticket['event_image'] ?>" alt="Event Image" width="80">
                    <h3>Seat:<?= (!empty($ticket['seat_label']) && $ticket['seat_label'] != '-') ? " {$ticket['seat_label']}" : "" ?></h3>
                    <p><strong>Event:</strong> <?= $ticket['event_name'] ?></p>
                    <p><strong>Date:</strong> <?= $ticket['event_date'] ?></p>
                    <p><strong>Location:</strong> <?= $ticket['location'] ?></p>
                    <p><strong>Price:</strong> $<?= $ticket['price'] ?></p>
                    <p><strong>Ticket ID:</strong> <?= $ticket['ticket_id'] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>