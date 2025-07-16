// Admin authentication system with PHP backend
class AdminAuth {
    constructor() {
        this.apiUrl = '../api/auth.php';
        this.sessionKey = 'admin_session_id';
    }

    async login(username, password) {
        try {
            const response = await fetch(this.apiUrl, {
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
                await fetch(this.apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
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
            sessionStorage.removeItem(this.sessionKey);
            sessionStorage.removeItem('admin_user');
            window.location.href = 'index.html';
        }
    }

    async validateSession() {
        const sessionId = sessionStorage.getItem(this.sessionKey);
        if (!sessionId) return false;

        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
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
                this.logout();
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
            window.location.href = 'index.html';
        }
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
                    alert.classList.remove('d-none');
                    setTimeout(() => {
                        alert.classList.add('d-none');
                    }, 5000);
                }
            } catch (error) {
                console.error('Login failed:', error);
                alert.classList.remove('d-none');
            } finally {
                // Restore button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    }
});
