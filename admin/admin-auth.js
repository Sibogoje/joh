// Admin authentication system
class AdminAuth {
    constructor() {
        this.credentials = {
            username: 'admin',
            password: 'journey2024!' // In production, use proper authentication
        };
    }

    login(username, password) {
        if (username === this.credentials.username && password === this.credentials.password) {
            sessionStorage.setItem('adminLoggedIn', 'true');
            sessionStorage.setItem('adminLoginTime', new Date().getTime());
            return true;
        }
        return false;
    }

    logout() {
        sessionStorage.removeItem('adminLoggedIn');
        sessionStorage.removeItem('adminLoginTime');
        window.location.href = 'index.html';
    }

    isLoggedIn() {
        const loggedIn = sessionStorage.getItem('adminLoggedIn');
        const loginTime = sessionStorage.getItem('adminLoginTime');
        
        if (!loggedIn) return false;
        
        // Check if session is older than 2 hours
        const currentTime = new Date().getTime();
        const sessionAge = currentTime - parseInt(loginTime);
        const maxAge = 2 * 60 * 60 * 1000; // 2 hours
        
        if (sessionAge > maxAge) {
            this.logout();
            return false;
        }
        
        return true;
    }

    requireAuth() {
        if (!this.isLoggedIn()) {
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
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const alert = document.getElementById('loginAlert');
            
            if (auth.login(username, password)) {
                window.location.href = 'dashboard.html';
            } else {
                alert.classList.remove('d-none');
                setTimeout(() => {
                    alert.classList.add('d-none');
                }, 5000);
            }
        });
    }
});
