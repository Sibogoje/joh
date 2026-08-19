// Reading Materials management system
class ReadingMaterialsManager {
    constructor() {
        this.apiUrl = '../api/reading_materials.php';
        this.materials = [];
        this.currentEditId = null;
        this.init();
    }

    async init() {
        await auth.requireAuth();
        await this.loadMaterials();
        this.renderMaterialsList();
        this.setupEventListeners();
        
        // Set default date to today
        document.getElementById('materialDate').value = new Date().toISOString().split('T')[0];
    }

    async loadMaterials() {
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
                this.materials = result.materials;
                console.log('Loaded reading materials from database:', this.materials);
                return true;
            } else {
                console.error('Failed to load reading materials:', result.message);
                this.showAlert(result.message || 'Failed to load reading materials', 'danger');
                return false;
            }
        } catch (error) {
            console.error('Load reading materials error:', error);
            this.showAlert('Failed to load reading materials', 'danger');
            return false;
        }
    }

    renderMaterialsList() {
        const tbody = document.getElementById('materialsTableBody');
        tbody.innerHTML = '';

        if (this.materials.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
                        No reading materials found. <a href="#" onclick="showNewMaterialForm()">Create your first material</a>
                    </td>
                </tr>
            `;
            return;
        }

        this.materials.forEach(material => {
            const statusBadge = material.status === 'published' 
                ? '<span class="badge bg-success">Published</span>'
                : '<span class="badge bg-warning text-dark">Draft</span>';

            const categoryBadge = this.getCategoryBadge(material.category);
            const materialDate = material.published_at || material.created_at;
            const formattedDate = new Date(materialDate).toLocaleDateString();

            tbody.innerHTML += `
                <tr>
                    <td>
                        <strong>${material.title}</strong>
                        <br><small class="text-muted">${material.excerpt || 'No excerpt'}</small>
                    </td>
                    <td>${categoryBadge}</td>
                    <td>${statusBadge}</td>
                    <td>${formattedDate}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary me-1" onclick="materialsManager.editMaterial(${material.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="materialsManager.deleteMaterial(${material.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }

    getCategoryBadge(category) {
        const badges = {
            guides: '<span class="badge bg-primary">Guides</span>',
            reports: '<span class="badge bg-info">Reports</span>',
            training: '<span class="badge bg-warning text-dark">Training</span>',
            stories: '<span class="badge bg-success">Stories</span>',
            resources: '<span class="badge bg-secondary">Resources</span>',
            policy: '<span class="badge bg-danger">Policy</span>',
            other: '<span class="badge bg-dark">Other</span>'
        };
        return badges[category] || '<span class="badge bg-light text-dark">Other</span>';
    }

    setupEventListeners() {
        const form = document.getElementById('materialForm');
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveMaterial();
        });
    }

    showNewMaterialForm() {
        this.currentEditId = null;
        document.getElementById('editorTitle').textContent = 'Create New Material';
        document.getElementById('materialForm').reset();
        document.getElementById('materialDate').value = new Date().toISOString().split('T')[0];
        document.getElementById('materialsListView').classList.add('d-none');
        document.getElementById('materialEditorView').classList.remove('d-none');
    }

    async editMaterial(id) {
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
                const material = result.material;
                this.currentEditId = id;
                
                document.getElementById('editorTitle').textContent = 'Edit Material';
                document.getElementById('materialTitle').value = material.title;
                document.getElementById('materialContent').value = material.content;
                document.getElementById('materialCategory').value = material.category;
                document.getElementById('materialStatus').value = material.status;
                document.getElementById('materialDate').value = material.published_at ? material.published_at.split(' ')[0] : '';
                document.getElementById('materialFileName').value = material.file_name || '';
                document.getElementById('materialFilePath').value = material.file_path || '';
                document.getElementById('materialExcerpt').value = material.excerpt || '';
                
                document.getElementById('materialsListView').classList.add('d-none');
                document.getElementById('materialEditorView').classList.remove('d-none');
            } else {
                this.showAlert('Failed to load reading material for editing', 'danger');
            }
        } catch (error) {
            console.error('Edit material error:', error);
            this.showAlert('Failed to load reading material for editing', 'danger');
        }
    }

    async saveMaterial() {
        const formData = {
            title: document.getElementById('materialTitle').value,
            content: document.getElementById('materialContent').value,
            category: document.getElementById('materialCategory').value,
            status: document.getElementById('materialStatus').value,
            published_at: document.getElementById('materialDate').value,
            file_name: document.getElementById('materialFileName').value,
            file_path: document.getElementById('materialFilePath').value,
            excerpt: document.getElementById('materialExcerpt').value,
            session_id: auth.getSessionId()
        };

        try {
            let response;
            
            if (this.currentEditId) {
                // Update existing material
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
                // Create new material
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
                await this.loadMaterials(); // Refresh from server
                this.showMaterialsList();
                this.showAlert('Reading material saved successfully!', 'success');
            } else {
                this.showAlert(result.message || 'Failed to save reading material', 'danger');
            }
        } catch (error) {
            console.error('Save material error:', error);
            this.showAlert('Failed to save reading material', 'danger');
        }
    }

    async deleteMaterial(id) {
        if (confirm('Are you sure you want to delete this reading material?')) {
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
                    await this.loadMaterials();
                    this.renderMaterialsList();
                    this.showAlert('Reading material deleted successfully!', 'success');
                } else {
                    this.showAlert(result.message || 'Failed to delete reading material', 'danger');
                }
            } catch (error) {
                console.error('Delete material error:', error);
                this.showAlert('Failed to delete reading material', 'danger');
            }
        }
    }

    async showMaterialsList() {
        document.getElementById('materialEditorView').classList.add('d-none');
        document.getElementById('materialsListView').classList.remove('d-none');
        await this.loadMaterials();
        this.renderMaterialsList();
    }

    previewMaterial() {
        const title = document.getElementById('materialTitle').value;
        const content = document.getElementById('materialContent').value;
        
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
function showNewMaterialForm() {
    materialsManager.showNewMaterialForm();
}

function showMaterialsList() {
    materialsManager.showMaterialsList();
}

function previewMaterial() {
    materialsManager.previewMaterial();
}

// Initialize when page loads
let materialsManager;
document.addEventListener('DOMContentLoaded', function() {
    materialsManager = new ReadingMaterialsManager();
});
