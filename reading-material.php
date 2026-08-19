<?php
$stylesVersion = filemtime(__DIR__ . '/styles.css');
$navVersion = filemtime(__DIR__ . '/nav.js');
$footerVersion = filemtime(__DIR__ . '/footer.js');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reading Material - Journey of Hope for Girls and Women in Eswatini</title>
    <link rel="icon" type="image/png" href="logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="styles.css?v=<?= $stylesVersion ?>" rel="stylesheet">
    <script src="nav.js?v=<?= $navVersion ?>"></script>
</head>
<body>
    <!-- Navigation will be loaded by nav.js -->

    <!-- Hero Section -->
    <section class="bg-primary text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">Reading Material</h1>
                    <p class="lead">
                        Explore guides, reports, training handbooks, and other resources 
                        that support empowerment, advocacy, and learning across Eswatini.
                    </p>
                </div>
                <div class="col-lg-4">
                    <i class="fas fa-book-open fa-10x text-warning opacity-75"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Reading Materials -->
    <section class="py-5">
        <div class="container">
            <!-- Featured Material -->
            <div class="row mb-5">
                <div class="col-12">
                    <div id="featuredMaterial">
                        <!-- Featured material will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- Materials Grid -->
            <div class="row mt-4">
                <div class="col-12">
                    <h3 class="mb-4">All Reading Materials</h3>
                </div>
                
                <div id="materialsGrid" class="col-12">
                    <!-- Materials will be loaded here -->
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Signup -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h3 class="fw-bold mb-4">Stay Updated</h3>
                    <p class="lead mb-4">
                        Subscribe to our newsletter to receive the latest resources and updates 
                        about our work and the impact we're making together.
                    </p>
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="email" class="form-control" placeholder="Enter your email">
                                <button class="btn btn-primary" type="button">Subscribe</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer will be loaded by footer.js -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="footer.js?v=<?= $footerVersion ?>"></script>
    <script>
        // Reading Materials loader class
        class MaterialsLoader {
            constructor() {
                this.apiUrl = 'api/reading_materials.php';
                this.materials = [];
                this.init();
            }

            async init() {
                await this.loadMaterials();
                this.renderMaterials();
            }

            async loadMaterials() {
                try {
                    const response = await fetch(`${this.apiUrl}?_t=${Date.now()}`, {
                        method: 'GET',
                        headers: {
                            'Cache-Control': 'no-cache'
                        }
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        // Filter only published materials
                        this.materials = result.materials.filter(material => material.status === 'published');
                        console.log('Loaded reading materials:', this.materials);
                    } else {
                        console.error('Failed to load reading materials:', result.message);
                        this.showFallbackContent();
                    }
                } catch (error) {
                    console.error('Error loading reading materials:', error);
                    this.showFallbackContent();
                }
            }

            renderMaterials() {
                if (this.materials.length === 0) {
                    this.showFallbackContent();
                    return;
                }

                // Render featured material (first/latest)
                this.renderFeaturedMaterial();
                
                // Render materials grid (remaining)
                this.renderMaterialsGrid();
            }

            renderFeaturedMaterial() {
                const featured = this.materials[0];
                if (!featured) return;

                const featuredContainer = document.getElementById('featuredMaterial');
                const publishedDate = new Date(featured.published_at || featured.created_at);
                
                featuredContainer.innerHTML = `
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-5">
                            <span class="badge bg-primary mb-3">Featured</span>
                            <h2 class="card-title mb-3">${featured.title}</h2>
                            <p class="text-muted mb-3">
                                <i class="fas fa-calendar me-2"></i>${publishedDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })} | 
                                <i class="fas fa-user me-2"></i>By ${featured.author_name || 'Admin'} | 
                                ${this.getCategoryBadge(featured.category)}
                            </p>
                            <p class="card-text lead mb-4">
                                ${featured.excerpt || this.truncateText(featured.content, 200)}
                            </p>
                            <div class="card-text mb-4">
                                ${this.truncateText(featured.content, 500)}
                            </div>
                            ${featured.file_path ? `
                                <a href="${featured.file_path}" target="_blank" class="btn btn-outline-primary me-2">
                                    <i class="fas fa-download me-2"></i>Download
                                </a>
                            ` : ''}
                            <button class="btn btn-primary" onclick="materialsLoader.showFullMaterial(${featured.id})">Read Full Material</button>
                        </div>
                    </div>
                `;
            }

            renderMaterialsGrid() {
                const materialsContainer = document.getElementById('materialsGrid');
                const remainingMaterials = this.materials.slice(1); // Skip featured

                if (remainingMaterials.length === 0) {
                    materialsContainer.innerHTML = '<div class="col-12 text-center"><p class="text-muted">No additional reading materials available.</p></div>';
                    return;
                }

                let materialsHTML = '';
                
                remainingMaterials.forEach(material => {
                    const publishedDate = new Date(material.published_at || material.created_at);
                    const categoryBadge = this.getCategoryBadge(material.category);
                    
                    materialsHTML += `
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-0 shadow">
                                <div class="card-body">
                                    ${categoryBadge}
                                    <h5 class="card-title">${material.title}</h5>
                                    <p class="text-muted small mb-3">${publishedDate.toLocaleDateString()}</p>
                                    <p class="card-text">
                                        ${material.excerpt || this.truncateText(material.content, 120)}
                                    </p>
                                    ${material.file_path ? `
                                        <a href="${material.file_path}" target="_blank" class="btn btn-outline-primary btn-sm me-1">
                                            <i class="fas fa-download me-1"></i>Download
                                        </a>
                                    ` : ''}
                                    <button class="btn btn-outline-primary btn-sm" onclick="materialsLoader.showFullMaterial(${material.id})">Read More</button>
                                </div>
                            </div>
                        </div>
                    `;
                });

                materialsContainer.innerHTML = materialsHTML;
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

            truncateText(text, maxLength) {
                if (!text) return '';
                if (text.length <= maxLength) return text;
                return text.substr(0, maxLength) + '...';
            }

            showFullMaterial(materialId) {
                const material = this.materials.find(m => m.id === materialId);
                if (!material) return;

                // Create modal to show full material
                const modal = document.createElement('div');
                modal.className = 'modal fade';
                modal.innerHTML = `
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">${material.title}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-muted mb-3">
                                    <i class="fas fa-calendar me-2"></i>${new Date(material.published_at || material.created_at).toLocaleDateString()} | 
                                    <i class="fas fa-user me-2"></i>By ${material.author_name || 'Admin'} | 
                                    ${this.getCategoryBadge(material.category)}
                                </p>
                                <div style="white-space: pre-wrap; line-height: 1.6;">${material.content}</div>
                                ${material.file_path ? `
                                    <div class="mt-4">
                                        <a href="${material.file_path}" target="_blank" class="btn btn-outline-primary">
                                            <i class="fas fa-download me-2"></i>Download
                                        </a>
                                    </div>
                                ` : ''}
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                `;
                
                document.body.appendChild(modal);
                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();
                
                // Remove modal from DOM when hidden
                modal.addEventListener('hidden.bs.modal', function() {
                    document.body.removeChild(modal);
                });
            }

            showFallbackContent() {
                // Show static content if database fails
                document.getElementById('featuredMaterial').innerHTML = `
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-5">
                            <span class="badge bg-primary mb-3">Featured</span>
                            <h2 class="card-title mb-3">Our Reading Resources</h2>
                            <p class="card-text lead mb-4">
                                Discover guides, reports, and handbooks that support empowerment 
                                and learning across our community circles.
                            </p>
                            <p class="card-text">
                                Materials are currently being compiled. Please check back soon 
                                for our latest guides and resources.
                            </p>
                            <a href="about.php" class="btn btn-primary">Learn More About Us</a>
                        </div>
                    </div>
                `;

                document.getElementById('materialsGrid').innerHTML = `
                    <div class="col-12 text-center">
                        <p class="text-muted">Reading materials are currently being updated. Please check back soon!</p>
                    </div>
                `;
            }
        }

        // Initialize materials loader when page loads
        let materialsLoader;
        document.addEventListener('DOMContentLoaded', function() {
            materialsLoader = new MaterialsLoader();
        });
    </script>
</body>
</html>
