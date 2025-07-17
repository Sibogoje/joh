// Gallery management system
class GalleryManager {
    constructor() {
        this.apiUrl = '../api/gallery.php';
        this.images = [];
        this.selectedFiles = [];
        this.currentEditId = null;
        this.init();
    }

    async init() {
        await auth.requireAuth();
        await this.loadImages();
        this.renderGallery();
        this.setupEventListeners();
    }

    async loadImages() {
        try {
            const sessionId = auth.getSessionId();
            const url = `${this.apiUrl}?session_id=${sessionId}&_t=${Date.now()}`;
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate'
                }
            });

            const result = await response.json();
            
            if (result.success) {
                this.images = result.images;
                console.log('Loaded images from database:', this.images);
                return true;
            } else {
                console.error('Failed to load images:', result.message);
                this.showAlert(result.message || 'Failed to load images', 'danger');
                return false;
            }
        } catch (error) {
            console.error('Load images error:', error);
            this.showAlert('Failed to load images', 'danger');
            return false;
        }
    }

    renderGallery() {
        const grid = document.getElementById('galleryGrid');
        const filter = document.getElementById('categoryFilter').value;
        
        let filteredImages = this.images;
        if (filter !== 'all') {
            filteredImages = this.images.filter(img => img.category === filter);
        }

        if (filteredImages.length === 0) {
            grid.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="fas fa-images fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No images found</h5>
                    <p class="text-muted">Upload some images to get started!</p>
                </div>
            `;
            return;
        }

        let html = '';
        filteredImages.forEach(image => {
            html += `
                <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                    <div class="card gallery-item border-0 shadow">
                        <div class="position-relative">
                            <img src="${image.file_path}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="${image.alt_text || image.title}">
                            <div class="overlay">
                                <button class="btn btn-light btn-sm me-2" onclick="galleryManager.editImage(${image.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="galleryManager.deleteImage(${image.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <span class="position-absolute top-0 end-0 m-2">
                                ${this.getCategoryBadge(image.category)}
                            </span>
                        </div>
                        <div class="card-body p-3">
                            <h6 class="card-title mb-1">${image.title}</h6>
                            <p class="card-text small text-muted mb-2">${image.description || 'No description'}</p>
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                ${new Date(image.created_at).toLocaleDateString()}
                            </small>
                        </div>
                    </div>
                </div>
            `;
        });

        grid.innerHTML = html;
    }

    getCategoryBadge(category) {
        const badges = {
            rising: '<span class="badge bg-dark">Rising</span>',
            training: '<span class="badge bg-warning">Training</span>',
            community: '<span class="badge bg-primary">Community</span>',
            advocacy: '<span class="badge bg-info">Advocacy</span>'
        };
        return badges[category] || '<span class="badge bg-secondary">Other</span>';
    }

    setupEventListeners() {
        // Category filter
        document.getElementById('categoryFilter').addEventListener('change', () => {
            this.renderGallery();
        });

        // File input change
        document.getElementById('fileInput').addEventListener('change', (e) => {
            this.handleFileSelection(e.target.files);
        });

        // Drag and drop
        const uploadArea = document.getElementById('uploadArea');
        
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            this.handleFileSelection(e.dataTransfer.files);
        });
    }

    handleFileSelection(files) {
        this.selectedFiles = Array.from(files).filter(file => file.type.startsWith('image/'));
        
        if (this.selectedFiles.length === 0) {
            this.showAlert('Please select valid image files', 'warning');
            return;
        }

        this.showFilePreview();
    }

    showFilePreview() {
        const preview = document.getElementById('uploadPreview');
        const grid = document.getElementById('previewGrid');
        
        let html = '';
        this.selectedFiles.forEach((file, index) => {
            const url = URL.createObjectURL(file);
            html += `
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card">
                        <img src="${url}" class="card-img-top" style="height: 150px; object-fit: cover;">
                        <div class="card-body p-2">
                            <small class="text-muted">${file.name}</small>
                            <button class="btn btn-sm btn-outline-danger float-end" onclick="galleryManager.removePreviewFile(${index})">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        grid.innerHTML = html;
        preview.classList.remove('d-none');
    }

    removePreviewFile(index) {
        this.selectedFiles.splice(index, 1);
        if (this.selectedFiles.length === 0) {
            document.getElementById('uploadPreview').classList.add('d-none');
        } else {
            this.showFilePreview();
        }
    }

    async uploadImages() {
        if (this.selectedFiles.length === 0) {
            this.showAlert('Please select images to upload', 'warning');
            return;
        }

        const category = document.getElementById('uploadCategory').value;
        const uploadBtn = document.getElementById('uploadBtn');
        const originalText = uploadBtn.innerHTML;
        
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Uploading...';
        uploadBtn.disabled = true;

        try {
            for (let i = 0; i < this.selectedFiles.length; i++) {
                const file = this.selectedFiles[i];
                await this.uploadSingleImage(file, category);
            }

            await this.loadImages();
            this.renderGallery();
            
            // Close modal and reset
            bootstrap.Modal.getInstance(document.getElementById('uploadModal')).hide();
            this.resetUploadForm();
            
            this.showAlert(`${this.selectedFiles.length} image(s) uploaded successfully!`, 'success');
        } catch (error) {
            console.error('Upload error:', error);
            this.showAlert('Failed to upload images', 'danger');
        } finally {
            uploadBtn.innerHTML = originalText;
            uploadBtn.disabled = false;
        }
    }

    async uploadSingleImage(file, category) {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('category', category);
        formData.append('title', file.name.split('.')[0]);
        formData.append('session_id', auth.getSessionId());

        const response = await fetch(this.apiUrl, {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        if (!result.success) {
            throw new Error(result.message || 'Upload failed');
        }
    }

    resetUploadForm() {
        document.getElementById('fileInput').value = '';
        document.getElementById('uploadPreview').classList.add('d-none');
        this.selectedFiles = [];
    }

    async editImage(id) {
        const image = this.images.find(img => img.id === id);
        if (!image) return;

        this.currentEditId = id;
        
        document.getElementById('editImagePreview').src = image.file_path;
        document.getElementById('editImageTitle').value = image.title;
        document.getElementById('editImageDescription').value = image.description || '';
        document.getElementById('editImageCategory').value = image.category;
        
        new bootstrap.Modal(document.getElementById('editImageModal')).show();
    }

    async saveImageEdit() {
        if (!this.currentEditId) return;

        const data = {
            id: this.currentEditId,
            title: document.getElementById('editImageTitle').value,
            description: document.getElementById('editImageDescription').value,
            category: document.getElementById('editImageCategory').value,
            session_id: auth.getSessionId()
        };

        try {
            const response = await fetch(this.apiUrl, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            
            if (result.success) {
                await this.loadImages();
                this.renderGallery();
                bootstrap.Modal.getInstance(document.getElementById('editImageModal')).hide();
                this.showAlert('Image updated successfully!', 'success');
            } else {
                this.showAlert(result.message || 'Failed to update image', 'danger');
            }
        } catch (error) {
            console.error('Update image error:', error);
            this.showAlert('Failed to update image', 'danger');
        }
    }

    async deleteImage(id) {
        if (!confirm('Are you sure you want to delete this image?')) return;

        try {
            const response = await fetch(this.apiUrl, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: id,
                    session_id: auth.getSessionId()
                })
            });

            const result = await response.json();
            
            if (result.success) {
                await this.loadImages();
                this.renderGallery();
                this.showAlert('Image deleted successfully!', 'success');
            } else {
                this.showAlert(result.message || 'Failed to delete image', 'danger');
            }
        } catch (error) {
            console.error('Delete image error:', error);
            this.showAlert('Failed to delete image', 'danger');
        }
    }

    showAlert(message, type) {
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

        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.parentNode.removeChild(alertDiv);
            }
        }, 5000);
    }
}

// Initialize when page loads
let galleryManager;
document.addEventListener('DOMContentLoaded', function() {
    galleryManager = new GalleryManager();
});
