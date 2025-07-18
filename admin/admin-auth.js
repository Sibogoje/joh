// Admin authentication system with PHP backend
class AdminAuth {
    constructor() {
        this.apiUrl = '../api/auth.php';
        this.sessionKey = 'admin_session_id';
    }

    // Add cache-busting parameter to URL
    getCacheBustUrl(url) {
        const separator = url.includes('?') ? '&' : '?';
        return `${url}${separator}_cb=${Date.now()}&_r=${Math.random()}`;
    }

    async login(username, password) {
        try {
            const response = await fetch(this.getCacheBustUrl(this.apiUrl), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0'
                },
                body: JSON.stringify({
                    action: 'login',
                    username: username,
                    password: password
                })
            });

            const result = await response.json();
            
            if (result.success) {
                sessionStorage.setItem(this.sessionKey, result.session_id);
                sessionStorage.setItem('admin_user', JSON.stringify(result.user));
                return true;
            }
            
            return false;
        } catch (error) {
            console.error('Login error:', error);
            return false;
        }
    }

    async logout() {
        try {
            const sessionId = sessionStorage.getItem(this.sessionKey);
            if (sessionId) {
                await fetch(this.getCacheBustUrl(this.apiUrl), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Cache-Control': 'no-cache, no-store, must-revalidate',
                        'Pragma': 'no-cache'
                    },
                    body: JSON.stringify({
                        action: 'logout',
                        session_id: sessionId
                    })
                });
            }
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            // Clear all cache-related storage
            sessionStorage.clear();
            localStorage.clear();
            window.location.href = 'index.html';
        }
    }

    async validateSession() {
        const sessionId = sessionStorage.getItem(this.sessionKey);
        if (!sessionId) return false;

        try {
            const response = await fetch(this.getCacheBustUrl(this.apiUrl), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0'
                },
                body: JSON.stringify({
                    action: 'validate',
                    session_id: sessionId
                })
            });

            const result = await response.json();
            
            if (result.success) {
                sessionStorage.setItem('admin_user', JSON.stringify(result.user));
                return true;
            } else {
                // Don't automatically logout, just return false
                console.log('Session validation failed:', result.message);
                sessionStorage.removeItem(this.sessionKey);
                sessionStorage.removeItem('admin_user');
                return false;
            }
        } catch (error) {
            console.error('Session validation error:', error);
            return false;
        }
    }

    getSessionId() {
        return sessionStorage.getItem(this.sessionKey);
    }

    getCurrentUser() {
        const user = sessionStorage.getItem('admin_user');
        return user ? JSON.parse(user) : null;
    }

    async requireAuth() {
        const isValid = await this.validateSession();
        if (!isValid) {
            // Only redirect if we're not already on the login page
            const currentPath = window.location.pathname;
            if (!currentPath.includes('index.html') && 
                window.location.pathname.indexOf('/admin/') !== -1 && 
                !window.location.pathname.endsWith('/admin/') &&
                !window.location.pathname.endsWith('/admin/index.html')) {
                window.location.href = 'index.html';
            }
        }
        return isValid;
    }
}

// Initialize auth
const auth = new AdminAuth();

// Login form handler
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const alert = document.getElementById('loginAlert');
            
            // Show loading state
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Logging in...';
            submitBtn.disabled = true;

            try {
                const success = await auth.login(username, password);
                
                if (success) {
                    window.location.href = 'dashboard.html';
                } else {
                    document.getElementById('loginErrorMessage').textContent = 'Invalid username or password';
                    alert.classList.remove('d-none');
                    setTimeout(() => {
                        alert.classList.add('d-none');
                    }, 5000);
                }
            } catch (error) {
                console.error('Login failed:', error);
                document.getElementById('loginErrorMessage').textContent = 'Network error: ' + error.message;
                alert.classList.remove('d-none');
            } finally {
                // Restore button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    }
});