<section class="welcome-message">
    <h2>Create an Account</h2>
</section>

<div class="register-container">
    <div class="register-form">
        <form action="../app/controllers/register_process.php" method="post">
            <label for="fullname">Full Name:</label><br>
            <input type="text" id="fullname" name="fullname" required><br><br>
    
            <label for="email">Email Address:</label><br>
            <input type="email" id="email" name="email" required><br><br>
    
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>
    
            <label for="confirm-password">Confirm Password:</label><br>
            <input type="password" id="confirm-password" name="confirm-password" required>
    
            <button class="btn" type="submit">Register</button>
        </form>
        <p>Already have an account? 
            <a href="register.php?form=login">Login here</a>
        </p>
    </div>
</div>