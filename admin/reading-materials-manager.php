<?php
$siteStylesVersion = filemtime(__DIR__ . '/../styles.css');
$adminStylesVersion = filemtime(__DIR__ . '/admin-styles.css');
$adminAuthVersion = filemtime(__DIR__ . '/admin-auth.js');
$materialsManagerVersion = filemtime(__DIR__ . '/reading-materials-manager.js');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reading Materials Manager - Journey of Hope Admin</title>
    <link rel="icon" type="image/png" href="../logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../styles.css?v=<?= $siteStylesVersion ?>" rel="stylesheet">
    <link href="admin-styles.css?v=<?= $adminStylesVersion ?>" rel="stylesheet">
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
            <li><a href="reading-materials-manager.php" class="active"><i class="fas fa-book-open"></i>Reading Materials</a></li>
            <li><a href="gallery-manager.php"><i class="fas fa-images"></i>Manage Gallery</a></li>
            <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i>View Website</a></li>
            <li><a href="#" onclick="auth.logout()"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="admin-content">
        <div class="admin-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 fw-bold">Reading Materials Manager</h1>
                <button class="btn btn-admin-primary" onclick="showNewMaterialForm()">
                    <i class="fas fa-plus me-2"></i>New Material
                </button>
            </div>
        </div>

        <!-- Materials List -->
        <div id="materialsListView">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-admin">
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Document</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="materialsTableBody">
                                <!-- Materials will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Material Editor -->
        <div id="materialEditorView" class="d-none">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0" id="editorTitle">Create New Material</h5>
                        <button class="btn btn-outline-secondary" onclick="showMaterialsList()">
                            <i class="fas fa-arrow-left me-2"></i>Back to Materials
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="materialForm">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="materialTitle" class="form-label fw-bold">Material Title</label>
                                    <input type="text" class="form-control" id="materialTitle" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="materialContent" class="form-label fw-bold">Content</label>
                                    <textarea class="form-control post-editor" id="materialContent" rows="15" required></textarea>
                                </div>

                                <div class="card border mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fw-bold"><i class="fas fa-paperclip me-2"></i>Document Upload</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="upload-area" id="materialUploadArea">
                                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                            <h6>Upload a Document</h6>
                                            <p class="text-muted small">PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, CSV (max 25MB)</p>
                                            <input type="file" id="materialFileInput" class="d-none">
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('materialFileInput').click()">
                                                <i class="fas fa-upload me-1"></i>Select Document
                                            </button>
                                        </div>
                                        <div id="materialFileInfo" class="d-none">
                                            <div class="d-flex align-items-center justify-content-between border rounded p-2">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-file-pdf text-danger fa-2x me-2"></i>
                                                    <div>
                                                        <strong id="materialFileNameDisplay"></strong>
                                                        <small class="text-muted d-block" id="materialFileSizeDisplay"></small>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="materialsManager.removeSelectedDocument()">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="materialExistingFile" class="mt-2 d-none">
                                            <div class="d-flex align-items-center justify-content-between border rounded p-2 bg-light">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-file-alt text-primary fa-2x me-2"></i>
                                                    <div>
                                                        <strong id="materialExistingFileName"></strong>
                                                        <small class="text-muted d-block">Current attached document</small>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="materialsManager.downloadDocument()">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="materialCategory" class="form-label fw-bold">Category</label>
                                    <select class="form-select" id="materialCategory">
                                        <option value="guides">Guides</option>
                                        <option value="reports">Reports</option>
                                        <option value="training">Training</option>
                                        <option value="stories">Stories</option>
                                        <option value="resources">Resources</option>
                                        <option value="policy">Policy</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="materialStatus" class="form-label fw-bold">Status</label>
                                    <select class="form-select" id="materialStatus">
                                        <option value="draft">Draft</option>
                                        <option value="published">Published</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="materialDate" class="form-label fw-bold">Date</label>
                                    <input type="date" class="form-control" id="materialDate">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="materialExcerpt" class="form-label fw-bold">Excerpt</label>
                                    <textarea class="form-control" id="materialExcerpt" rows="3" placeholder="Brief description of the material..."></textarea>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-admin-primary">
                                        <i class="fas fa-save me-2"></i>Save Material
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="previewMaterial()">
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
    <script src="admin-auth.js?v=<?= $adminAuthVersion ?>"></script>
    <script src="reading-materials-manager.js?v=<?= $materialsManagerVersion ?>"></script>
</body>
</html>
