<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Journey of Hope</title>
    <link rel="icon" type="image/png" href="../logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../styles.css" rel="stylesheet">
    <link href="admin-styles.css" rel="stylesheet">
</head>
<body>
    <!-- Sidebar -->
    <div class="admin-sidebar">
        <a href="dashboard.php" class="sidebar-brand">
            <img src="../logo.png" alt="Logo" height="30" class="me-2">
            <strong>Admin Panel</strong>
        </a>
        <ul class="sidebar-nav">
            <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
            <li><a href="posts-manager.php"><i class="fas fa-edit"></i>Manage Posts</a></li>
            <li><a href="gallery-manager.php"><i class="fas fa-images"></i>Manage Gallery</a></li>
            <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i>View Website</a></li>
            <li><a href="#" onclick="auth.logout()"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="admin-content">
        <div class="admin-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 fw-bold">Dashboard</h1>
                <div>
                    <span class="text-muted">Welcome back, Admin</span>
                    <button class="btn btn-outline-danger btn-sm ms-3" onclick="auth.logout()">
                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card border-0 shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Posts</div>
                                <div class="h5 mb-0 fw-bold text-gray-800" id="totalPosts">Loading...</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-edit fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card border-0 shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-success text-uppercase mb-1">Gallery Images</div>
                                <div class="h5 mb-0 fw-bold text-gray-800" id="totalImages">Loading...</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-images fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card border-0 shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-warning text-uppercase mb-1">Draft Posts</div>
                                <div class="h5 mb-0 fw-bold text-gray-800" id="draftPosts">Loading...</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-alt fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card border-0 shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-info text-uppercase mb-1">Published Posts</div>
                                <div class="h5 mb-0 fw-bold text-gray-800" id="publishedPosts">Loading...</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-lg-12 mb-4">
                <div class="card border-0 shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <a href="posts-manager.php?action=new" class="btn btn-admin-primary w-100 mb-2">
                                    <i class="fas fa-plus me-2"></i>Create New Post
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="gallery-manager.php?action=upload" class="btn btn-outline-primary w-100 mb-2">
                                    <i class="fas fa-upload me-2"></i>Upload Images
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="../index.php" target="_blank" class="btn btn-outline-secondary w-100 mb-2">
                                    <i class="fas fa-eye me-2"></i>Preview Website
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Posts Preview -->
        <div class="card border-0 shadow">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-newspaper me-2"></i>Recent Posts</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-admin">
                            <tr>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="recentPostsTable">
                            <tr>
                                <td colspan="4" class="text-center">Loading recent posts...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="posts-manager.php" class="btn btn-primary">View All Posts</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="admin-auth.js?v=1.0"></script>
    <script>
        // DEBUG: Show session info
        document.addEventListener('DOMContentLoaded', function() {
            // If the URL does NOT end with .html, force it to .html (hard redirect)
            if (
                window.location.pathname.match(/\/dashboard($|\/)/)
                && !window.location.pathname.endsWith('.html')
            ) {
                // Always use absolute path to avoid relative path issues
                window.location.replace(window.location.origin + window.location.pathname.replace(/\/dashboard\/?$/, '/dashboard.php'));
                return;
            }

            const sessionId = sessionStorage.getItem('admin_session_id');
            const user = sessionStorage.getItem('admin_user');
            console.log('Dashboard sessionId:', sessionId);
            console.log('Dashboard user:', user);
            if (!sessionId) {
                alert('No admin session found. You will be redirected to login.');
                window.location.href = 'index.php';
            }
        });

        // Dashboard data loader
        class DashboardLoader {
            constructor() {
                this.postsApi = '../api/posts.php';
                this.galleryApi = '../api/gallery.php';
                this.init();
            }

            async init() {
                await auth.requireAuth();
                await this.loadDashboardData();
                document.getElementById('lastUpdated').textContent = new Date().toLocaleDateString();
            }

            async loadDashboardData() {
                try {
                    await Promise.all([
                        this.loadPostsStats(),
                        this.loadGalleryStats(),
                        this.loadRecentPosts()
                    ]);
                } catch (error) {
                    console.error('Error loading dashboard data:', error);
                }
            }

            async loadPostsStats() {
                try {
                    const sessionId = auth.getSessionId();
                    const response = await fetch(`${this.postsApi}?session_id=${sessionId}&_t=${Date.now()}`, {
                        headers: { 'Cache-Control': 'no-cache' }
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        const posts = result.posts;
                        const totalPosts = posts.length;
                        const draftPosts = posts.filter(p => p.status === 'draft').length;
                        const publishedPosts = posts.filter(p => p.status === 'published').length;

                        document.getElementById('totalPosts').textContent = totalPosts;
                        document.getElementById('draftPosts').textContent = draftPosts;
                        document.getElementById('publishedPosts').textContent = publishedPosts;
                    } else {
                        this.showError('totalPosts', 'Error');
                        this.showError('draftPosts', 'Error');
                        this.showError('publishedPosts', 'Error');
                    }
                } catch (error) {
                    console.error('Error loading posts stats:', error);
                    this.showError('totalPosts', 'Error');
                    this.showError('draftPosts', 'Error');
                    this.showError('publishedPosts', 'Error');
                }
            }

            async loadGalleryStats() {
                try {
                    const sessionId = auth.getSessionId();
                    const response = await fetch(`${this.galleryApi}?session_id=${sessionId}&_t=${Date.now()}`, {
                        headers: { 'Cache-Control': 'no-cache' }
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        const totalImages = result.images.length;
                        document.getElementById('totalImages').textContent = totalImages;
                    } else {
                        this.showError('totalImages', 'Error');
                    }
                } catch (error) {
                    console.error('Error loading gallery stats:', error);
                    this.showError('totalImages', 'Error');
                }
            }

            async loadRecentPosts() {
                try {
                    const sessionId = auth.getSessionId();
                    const response = await fetch(`${this.postsApi}?session_id=${sessionId}&_t=${Date.now()}`, {
                        headers: { 'Cache-Control': 'no-cache' }
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        const recentPosts = result.posts.slice(0, 5); // Get latest 5 posts
                        this.renderRecentPosts(recentPosts);
                    } else {
                        this.showRecentPostsError();
                    }
                } catch (error) {
                    console.error('Error loading recent posts:', error);
                    this.showRecentPostsError();
                }
            }

            renderRecentPosts(posts) {
                const tbody = document.getElementById('recentPostsTable');
                
                if (posts.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center text-muted">No posts found. <a href="posts-manager.php">Create your first post</a></td>
                        </tr>
                    `;
                    return;
                }

                let html = '';
                posts.forEach(post => {
                    const statusBadge = post.status === 'published' 
                        ? '<span class="badge bg-success">Published</span>'
                        : '<span class="badge bg-warning text-dark">Draft</span>';
                    
                    const postDate = post.published_at || post.created_at;
                    const formattedDate = new Date(postDate).toLocaleDateString();

                    html += `
                        <tr>
                            <td>
                                <strong>${post.title}</strong>
                                ${post.excerpt ? `<br><small class="text-muted">${this.truncateText(post.excerpt, 60)}</small>` : ''}
                            </td>
                            <td>${statusBadge}</td>
                            <td>${formattedDate}</td>
                            <td>
                                <a href="posts-manager.php" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </td>
                        </tr>
                    `;
                });

                tbody.innerHTML = html;
            }

            truncateText(text, maxLength) {
                if (text.length <= maxLength) return text;
                return text.substr(0, maxLength) + '...';
            }

            showError(elementId, message) {
                document.getElementById(elementId).textContent = message;
                document.getElementById(elementId).classList.add('text-danger');
            }

            showRecentPostsError() {
                document.getElementById('recentPostsTable').innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center text-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error loading recent posts
                        </td>
                    </tr>
                `;
            }
        }

        // Initialize dashboard when page loads
        document.addEventListener('DOMContentLoaded', function() {
            new DashboardLoader();
        });
    </script>
</body>
</html>

