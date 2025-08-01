<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Journey of Hope</title>
    <link rel="icon" type="image/png" href="../logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../styles.css" rel="stylesheet">
    <link href="admin-styles.css" rel="stylesheet">
</head>
<body class="admin-login">
    <div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
        <div class="row w-100">
            <div class="col-md-4 mx-auto">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <img src="../logo.png" alt="Journey of Hope Logo" height="60" class="mb-3">
                            <h3 class="fw-bold text-primary">Admin Panel</h3>
                            <p class="text-muted">Journey of Hope Management</p>
                        </div>
                        
                        <form id="loginForm">
                            <div class="mb-3">
                                <label for="username" class="form-label fw-bold">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" class="form-control" id="username" required value="admin">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label fw-bold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" id="password" required placeholder="journey2024!">
                                </div>
                                <small class="form-text text-muted"></small>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 fw-bold" id="loginBtn">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>
                        </form>
                        
                        <div id="loginAlert" class="alert alert-danger mt-3 d-none">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <span id="loginErrorMessage">Invalid username or password.</span>
                        </div>

                        <!-- Debug info for development -->
                        <div id="debugInfo" class="mt-3 p-2 bg-light rounded" style="display: none;">
                            <small class="text-muted">
                                <strong>Debug Info:</strong><br>
                                <span id="debugApiUrl"></span><br>
                                <span id="debugResponse"></span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="admin-auth.js?v=1.0?v=1.0"></script>

    <!-- Development helper script -->
    <script>
        // Enable debug mode (remove in production)
        const DEBUG_MODE = true;
        
        if (DEBUG_MODE) {
            document.getElementById('debugInfo').style.display = 'block';
            document.getElementById('debugApiUrl').textContent = '../api/auth.php';
            
            // Test database connection
            async function testConnection() {
                try {
                    const response = await fetch('../api/auth.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            action: 'test'
                        })
                    });
                    
                    const result = await response.json();
                    console.log('Connection test result:', result);
                    document.getElementById('debugResponse').textContent = JSON.stringify(result);
                } catch (error) {
                    console.error('Connection test failed:', error);
                    document.getElementById('debugResponse').textContent = 'Error: ' + error.message;
                }
            }
            
            testConnection();
            
            // Override login form completely for debugging
            const loginForm = document.getElementById('loginForm');
            loginForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const username = document.getElementById('username').value;
                const password = document.getElementById('password').value;
                
                console.log('Direct login attempt:', { username, password });
                
                try {
                    const response = await fetch('../api/auth.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            action: 'login',
                            username: username,
                            password: password
                        })
                    });
                    
                    const result = await response.json();
                    console.log('Direct login result:', result);
                    document.getElementById('debugResponse').textContent = 'Login: ' + JSON.stringify(result);
                    
                    if (result.success) {
                        // Store session manually
                        sessionStorage.setItem('admin_session_id', result.session_id);
                        sessionStorage.setItem('admin_user', JSON.stringify(result.user));
                        // Always redirect to dashboard.php (with extension)
                        window.location.href = 'dashboard.php';
                    } else {
                        document.getElementById('loginErrorMessage').textContent = result.message || 'Login failed';
                        document.getElementById('loginAlert').classList.remove('d-none');
                    }
                } catch (error) {
                    console.error('Direct login error:', error);
                    document.getElementById('debugResponse').textContent = 'Error: ' + error.message;
                    document.getElementById('loginAlert').classList.remove('d-none');
                }
            });
        }
    </script>
</body>
</html>
