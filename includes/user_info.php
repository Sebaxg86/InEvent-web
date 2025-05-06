<section class="welcome-message">
    <h2>Your Information</h2>
</section>

<div class="user-card">
    <div class="card">
        <div class="user-info">
            <h3>Basic Information</h3>
            <p><strong>Name:</strong> <?= htmlspecialchars($_SESSION['user']['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['user']['email']) ?></p>
        </div>
        <hr>
        <div class="user-purchases">
            <h3>Purchases</h3>
            <ul>
                <li>compra 1</li>
                <li>compra 2</li>
                <li>compra 3</li>
                <li>compra 4</li>
            </ul>
        </div>
    </div>
</div>