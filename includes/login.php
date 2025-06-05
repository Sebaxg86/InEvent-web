<section class="welcome-message">
    <h2>Login into your account</h2>
</section>

<div class="login-container">
    <div class="login-form">
        <form id="login-form" action="app/controllers/login_process.php" method="post">
            <label for="email">Email Address:</label><br>
            <input type="email" id="email" name="email" required><br><br>
    
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>
    
            <button class="btn" type="submit">Login</button>
        </form>
        <p>Don't have an InEvent account? 
            <a href="register.php?form=register">Create one</a>
        </p>
    </div>
</div>
