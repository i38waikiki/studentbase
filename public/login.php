<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow-sm" style="width: 100%; max-width: 400px;">
        <div class="card-body p-4">

             <div class="text-center mb-3">
              <img src="../assets/logoR.png" width="50">
            </div>
      
            <h3 class="text-center mb-4">Login</h3>
 
            <form>
                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input 
                        type="email" 
                        class="form-control" 
                        id="email" 
                        placeholder="name@example.com"
                        required
                    >
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="password" 
                        placeholder="••••••••"
                        required
                    >
                </div>

                <!-- Login button -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-danger">
                        Login
                    </button>
                </div>
            </form>

            <!-- Forgot password -->
            <div class="text-center mt-3">
                <a href="forgot-password.php" class="text-decoration-none">
                    Forgot your password?
                </a>
            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
