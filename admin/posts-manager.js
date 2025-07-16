// Posts management system
class PostsManager {
    constructor() {
        this.posts = this.loadPosts();
        this.currentEditId = null;
        this.init();
    }

    init() {
        auth.requireAuth();
        this.renderPostsList();
        this.setupEventListeners();
        
        // Set default date to today
        document.getElementById('postDate').value = new Date().toISOString().split('T')[0];
    }

    loadPosts() {
        const savedPosts = localStorage.getItem('journeyOfHopePosts');
        if (savedPosts) {
            return JSON.parse(savedPosts);
        }
        
        // Default posts
        return [
            {
                id: 1,
                title: "One Billion Rising 2024: A Resounding Success Across Eswatini",
                content: "This year's One Billion Rising events across all four regions of Eswatini marked a significant milestone in our fight against gender-based violence...",
                excerpt: "Thousands of women and girls rose together, dancing for justice and transformation.",
                category: "rising",
                status: "published",
                date: "2024-02-14",
                author: "Colani Hlatjwako"
            },
            {
                id: 2,
                title: "New Community Circle Launched in Lubombo",
                content: "We're excited to announce the establishment of our newest community circle in the Lubombo region...",
                excerpt: "Bringing our total to 42 circles across Eswatini.",
                category: "community",
                status: "published",
                date: "2024-03-15",
                author: "Admin"
            },
            {
                id: 3,
                title: "Feminist Leadership Training Success",
                content: "Our latest feminist leadership training session empowered 50 women with knowledge about their rights...",
                excerpt: "Empowering women with knowledge about their rights and governance principles.",
                category: "training",
                status: "draft",
                date: "2024-03-08",
                author: "Admin"
            }
        ];
    }

    savePosts() {
        localStorage.setItem('journeyOfHopePosts', JSON.stringify(this.posts));
    }

    renderPostsList() {
        const tbody = document.getElementById('postsTableBody');
        tbody.innerHTML = '';

        this.posts.forEach(post => {
            const statusBadge = post.status === 'published' 
                ? '<span class="badge bg-success">Published</span>'
                : '<span class="badge bg-warning text-dark">Draft</span>';

            const categoryBadge = this.getCategoryBadge(post.category);

            tbody.innerHTML += `
                <tr>
                    <td>
                        <strong>${post.title}</strong>
                        <br><small class="text-muted">${post.excerpt || 'No excerpt'}</small>
                    </td>
                    <td>${categoryBadge}</td>
                    <td>${statusBadge}</td>
                    <td>${new Date(post.date).toLocaleDateString()}</td>
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

    editPost(id) {
        const post = this.posts.find(p => p.id === id);
        if (!post) return;

        this.currentEditId = id;
        document.getElementById('editorTitle').textContent = 'Edit Post';
        document.getElementById('postTitle').value = post.title;
        document.getElementById('postContent').value = post.content;
        document.getElementById('postCategory').value = post.category;
        document.getElementById('postStatus').value = post.status;
        document.getElementById('postDate').value = post.date;
        document.getElementById('postExcerpt').value = post.excerpt || '';
        
        document.getElementById('postsListView').classList.add('d-none');
        document.getElementById('postEditorView').classList.remove('d-none');
    }

    savePost() {
        const formData = {
            title: document.getElementById('postTitle').value,
            content: document.getElementById('postContent').value,
            category: document.getElementById('postCategory').value,
            status: document.getElementById('postStatus').value,
            date: document.getElementById('postDate').value,
            excerpt: document.getElementById('postExcerpt').value,
            author: 'Admin'
        };

        if (this.currentEditId) {
            // Update existing post
            const postIndex = this.posts.findIndex(p => p.id === this.currentEditId);
            this.posts[postIndex] = { ...this.posts[postIndex], ...formData };
        } else {
            // Create new post
            const newId = Math.max(...this.posts.map(p => p.id), 0) + 1;
            this.posts.push({ id: newId, ...formData });
        }

        this.savePosts();
        this.showPostsList();
        this.showAlert('Post saved successfully!', 'success');
    }

    deletePost(id) {
        if (confirm('Are you sure you want to delete this post?')) {
            this.posts = this.posts.filter(p => p.id !== id);
            this.savePosts();
            this.renderPostsList();
            this.showAlert('Post deleted successfully!', 'success');
        }
    }

    showPostsList() {
        document.getElementById('postEditorView').classList.add('d-none');
        document.getElementById('postsListView').classList.remove('d-none');
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
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);

        // Auto remove after 3 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.parentNode.removeChild(alertDiv);
            }
        }, 3000);
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
