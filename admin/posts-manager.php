<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts Manager - Journey of Hope Admin</title>
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
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
            <li><a href="posts-manager.php" class="active"><i class="fas fa-edit"></i>Manage Posts</a></li>
            <li><a href="gallery-manager.php"><i class="fas fa-images"></i>Manage Gallery</a></li>
            <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i>View Website</a></li>
            <li><a href="#" onclick="auth.logout()"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="admin-content">
        <div class="admin-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 fw-bold">Posts Manager</h1>
                <button class="btn btn-admin-primary" onclick="showNewPostForm()">
                    <i class="fas fa-plus me-2"></i>New Post
                </button>
            </div>
        </div>

        <!-- Posts List -->
        <div id="postsListView">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-admin">
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="postsTableBody">
                                <!-- Posts will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Post Editor -->
        <div id="postEditorView" class="d-none">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0" id="editorTitle">Create New Post</h5>
                        <button class="btn btn-outline-secondary" onclick="showPostsList()">
                            <i class="fas fa-arrow-left me-2"></i>Back to Posts
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="postForm">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="postTitle" class="form-label fw-bold">Post Title</label>
                                    <input type="text" class="form-control" id="postTitle" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="postContent" class="form-label fw-bold">Content</label>
                                    <textarea class="form-control post-editor" id="postContent" rows="15" required></textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="postCategory" class="form-label fw-bold">Category</label>
                                    <select class="form-select" id="postCategory">
                                        <option value="community">Community Impact</option>
                                        <option value="training">Training</option>
                                        <option value="advocacy">Advocacy</option>
                                        <option value="economic">Economic Empowerment</option>
                                        <option value="partnership">Partnership</option>
                                        <option value="transformation">Transformation</option>
                                        <option value="rising">One Billion Rising</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="postStatus" class="form-label fw-bold">Status</label>
                                    <select class="form-select" id="postStatus">
                                        <option value="draft">Draft</option>
                                        <option value="published">Published</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="postDate" class="form-label fw-bold">Date</label>
                                    <input type="date" class="form-control" id="postDate">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="postExcerpt" class="form-label fw-bold">Excerpt</label>
                                    <textarea class="form-control" id="postExcerpt" rows="3" placeholder="Brief description of the post..."></textarea>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-admin-primary">
                                        <i class="fas fa-save me-2"></i>Save Post
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="previewPost()">
                                        <i class="fas fa-eye me-2"></i>Preview
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="admin-auth.js?v=1.0"></script>
    <script src="posts-manager.js"></script>
</body>
</html>
