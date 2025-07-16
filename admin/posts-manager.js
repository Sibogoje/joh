// Posts management system
class PostsManager {
    constructor() {
        this.apiUrl = '../api/posts.php';
        this.posts = [];
        this.currentEditId = null;
        this.init();
    }

    async init() {
        await auth.requireAuth();
        await this.loadPosts();
        this.renderPostsList();
        this.setupEventListeners();
        
        // Set default date to today
        document.getElementById('postDate').value = new Date().toISOString().split('T')[0];
    }

    async loadPosts() {
        try {
            const sessionId = auth.getSessionId();
            const url = `${this.apiUrl}?session_id=${sessionId}&_t=${Date.now()}`;
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0'
                }
            });

            const result = await response.json();
            
            if (result.success) {
                this.posts = result.posts;
                console.log('Loaded posts from database:', this.posts); // Debug log
                return true;
            } else {
                console.error('Failed to load posts:', result.message);
                this.showAlert(result.message || 'Failed to load posts', 'danger');
                return false;
            }
        } catch (error) {
            console.error('Load posts error:', error);
            this.showAlert('Failed to load posts', 'danger');
            return false;
        }
    }

    renderPostsList() {
        const tbody = document.getElementById('postsTableBody');
        tbody.innerHTML = '';

        if (this.posts.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
                        No posts found. <a href="#" onclick="showNewPostForm()">Create your first post</a>
                    </td>
                </tr>
            `;
            return;
        }

        this.posts.forEach(post => {
            const statusBadge = post.status === 'published' 
                ? '<span class="badge bg-success">Published</span>'
                : '<span class="badge bg-warning text-dark">Draft</span>';

            const categoryBadge = this.getCategoryBadge(post.category);
            const postDate = post.published_at || post.created_at;
            const formattedDate = new Date(postDate).toLocaleDateString();

            tbody.innerHTML += `
                <tr>
                    <td>
                        <strong>${post.title}</strong>
                        <br><small class="text-muted">${post.excerpt || 'No excerpt'}</small>
                    </td>
                    <td>${categoryBadge}</td>
                    <td>${statusBadge}</td>
                    <td>${formattedDate}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary me-1" onclick="postsManager.editPost(${post.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="postsManager.deletePost(${post.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }

    getCategoryBadge(category) {
        const badges = {
            community: '<span class="badge bg-primary">Community Impact</span>',
            training: '<span class="badge bg-warning text-dark">Training</span>',
            advocacy: '<span class="badge bg-info">Advocacy</span>',
            economic: '<span class="badge bg-success">Economic</span>',
            partnership: '<span class="badge bg-secondary">Partnership</span>',
            transformation: '<span class="badge bg-danger">Transformation</span>',
            rising: '<span class="badge bg-dark">One Billion Rising</span>'
        };
        return badges[category] || '<span class="badge bg-light text-dark">Other</span>';
    }

    setupEventListeners() {
        const form = document.getElementById('postForm');
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.savePost();
        });
    }

    showNewPostForm() {
        this.currentEditId = null;
        document.getElementById('editorTitle').textContent = 'Create New Post';
        document.getElementById('postForm').reset();
        document.getElementById('postDate').value = new Date().toISOString().split('T')[0];
        document.getElementById('postsListView').classList.add('d-none');
        document.getElementById('postEditorView').classList.remove('d-none');
    }

    async editPost(id) {
        try {
            const sessionId = auth.getSessionId();
            const url = `${this.apiUrl}?id=${id}&session_id=${sessionId}&_t=${Date.now()}`;
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate'
                }
            });

            const result = await response.json();
            
            if (result.success) {
                const post = result.post;
                this.currentEditId = id;
                
                document.getElementById('editorTitle').textContent = 'Edit Post';
                document.getElementById('postTitle').value = post.title;
                document.getElementById('postContent').value = post.content;
                document.getElementById('postCategory').value = post.category;
                document.getElementById('postStatus').value = post.status;
                document.getElementById('postDate').value = post.published_at ? post.published_at.split(' ')[0] : '';
                document.getElementById('postExcerpt').value = post.excerpt || '';
                
                document.getElementById('postsListView').classList.add('d-none');
                document.getElementById('postEditorView').classList.remove('d-none');
            } else {
                this.showAlert('Failed to load post for editing', 'danger');
            }
        } catch (error) {
            console.error('Edit post error:', error);
            this.showAlert('Failed to load post for editing', 'danger');
        }
    }

    async savePost() {
        const formData = {
            title: document.getElementById('postTitle').value,
            content: document.getElementById('postContent').value,
            category: document.getElementById('postCategory').value,
            status: document.getElementById('postStatus').value,
            published_at: document.getElementById('postDate').value,
            excerpt: document.getElementById('postExcerpt').value,
            session_id: auth.getSessionId()
        };

        try {
            let response;
            
            if (this.currentEditId) {
                // Update existing post
                formData.id = this.currentEditId;
                response = await fetch(this.apiUrl, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Cache-Control': 'no-cache'
                    },
                    body: JSON.stringify(formData)
                });
            } else {
                // Create new post
                response = await fetch(this.apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Cache-Control': 'no-cache'
                    },
                    body: JSON.stringify(formData)
                });
            }

            const result = await response.json();
            
            if (result.success) {
                await this.loadPosts(); // Refresh posts from server
                this.showPostsList();
                this.showAlert('Post saved successfully!', 'success');
            } else {
                this.showAlert(result.message || 'Failed to save post', 'danger');
            }
        } catch (error) {
            console.error('Save post error:', error);
            this.showAlert('Failed to save post', 'danger');
        }
    }

    async deletePost(id) {
        if (confirm('Are you sure you want to delete this post?')) {
            try {
                const response = await fetch(this.apiUrl, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Cache-Control': 'no-cache'
                    },
                    body: JSON.stringify({
                        id: id,
                        session_id: auth.getSessionId()
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    await this.loadPosts(); // Refresh posts from server
                    this.renderPostsList();
                    this.showAlert('Post deleted successfully!', 'success');
                } else {
                    this.showAlert(result.message || 'Failed to delete post', 'danger');
                }
            } catch (error) {
                console.error('Delete post error:', error);
                this.showAlert('Failed to delete post', 'danger');
            }
        }
    }

    async showPostsList() {
        document.getElementById('postEditorView').classList.add('d-none');
        document.getElementById('postsListView').classList.remove('d-none');
        await this.loadPosts(); // Refresh posts when returning to list
        this.renderPostsList();
    }

    previewPost() {
        const title = document.getElementById('postTitle').value;
        const content = document.getElementById('postContent').value;
        
        if (!title || !content) {
            this.showAlert('Please fill in title and content to preview', 'warning');
            return;
        }

        // Open preview in new window
        const previewWindow = window.open('', '_blank');
        previewWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Preview: ${title}</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <link href="../styles.css" rel="stylesheet">
            </head>
            <body class="bg-light">
                <div class="container py-5">
                    <div class="card shadow">
                        <div class="card-body p-5">
                            <h1 class="mb-4">${title}</h1>
                            <div style="white-space: pre-wrap;">${content}</div>
                        </div>
                    </div>
                </div>
            </body>
            </html>
        `);
    }

    showAlert(message, type) {
        // Create and show alert
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.top = '20px';
        alertDiv.style.right = '20px';
        alertDiv.style.zIndex = '9999';
        alertDiv.style.minWidth = '300px';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.parentNode.removeChild(alertDiv);
            }
        }, 5000);
    }
}

// Global functions for HTML onclick events
function showNewPostForm() {
    postsManager.showNewPostForm();
}

function showPostsList() {
    postsManager.showPostsList();
}

function previewPost() {
    postsManager.previewPost();
}

// Initialize when page loads
let postsManager;
document.addEventListener('DOMContentLoaded', function() {
    postsManager = new PostsManager();
});
