<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Manager - Journey of Hope Admin</title>
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
            <li><a href="posts-manager.php"><i class="fas fa-edit"></i>Manage Posts</a></li>
            <li><a href="gallery-manager.php" class="active"><i class="fas fa-images"></i>Manage Gallery</a></li>
            <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i>View Website</a></li>
            <li><a href="#" onclick="auth.logout()"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="admin-content">
        <div class="admin-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 fw-bold">Gallery Manager</h1>
                <button class="btn btn-admin-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i class="fas fa-upload me-2"></i>Upload Images
                </button>
            </div>
        </div>

        <!-- Category Filter -->
        <div class="card border-0 shadow mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">Filter by Category</h5>
                    </div>
                    <div class="col-md-6">
                        <select class="form-select" id="categoryFilter">
                            <option value="all">All Categories</option>
                            <option value="rising">One Billion Rising</option>
                            <option value="training">Training Sessions</option>
                            <option value="community">Community Circles</option>
                            <option value="advocacy">Advocacy Events</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gallery Grid -->
        <div class="row" id="galleryGrid">
            <!-- Images will be loaded here -->
        </div>
    </div>

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Images</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="upload-area" id="uploadArea">
                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                        <h5>Drag & Drop Images Here</h5>
                        <p class="text-muted">or click to select files</p>
                        <input type="file" id="fileInput" multiple accept="image/*" class="d-none">
                        <button type="button" class="btn btn-admin-primary" onclick="document.getElementById('fileInput').click()">
                            Select Files
                        </button>
                    </div>
                    
                    <div id="uploadPreview" class="mt-4 d-none">
                        <h6>Selected Images:</h6>
                        <div class="row" id="previewGrid"></div>
                        
                        <div class="mt-3">
                            <label for="uploadCategory" class="form-label">Category</label>
                            <select class="form-select" id="uploadCategory">
                                <option value="rising">One Billion Rising</option>
                                <option value="training">Training Sessions</option>
                                <option value="community">Community Circles</option>
                                <option value="advocacy">Advocacy Events</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-admin-primary" id="uploadBtn" onclick="galleryManager.uploadImages()">
                        <i class="fas fa-upload me-2"></i>Upload Images
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Image Modal -->
    <div class="modal fade" id="editImageModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <img id="editImagePreview" class="img-fluid mb-3" style="max-height: 300px;">
                    <div class="mb-3">
                        <label for="editImageTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="editImageTitle">
                    </div>
                    <div class="mb-3">
                        <label for="editImageDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editImageDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editImageCategory" class="form-label">Category</label>
                        <select class="form-select" id="editImageCategory">
                            <option value="rising">One Billion Rising</option>
                            <option value="training">Training Sessions</option>
                            <option value="community">Community Circles</option>
                            <option value="advocacy">Advocacy Events</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-admin-primary" onclick="galleryManager.saveImageEdit()">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="admin-auth.js?v=1.0"></script>
    <script src="gallery-manager.js?v=1.0"></script>
</body>
</html>
