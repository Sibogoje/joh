<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - Journey of Hope for Girls and Women in Eswatini</title>
    <link rel="icon" type="image/png" href="logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <script src="nav.js?v=1.0"></script>
</head>
<body>
    <!-- Navigation will be loaded by nav.js?v=1.0 -->

    <!-- Hero Section -->
    <section class="bg-primary text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">Photo Gallery</h1>
                    <p class="lead">
                        Witness the transformation in action. Browse through our collection of photos 
                        showcasing community events, training sessions, and moments of empowerment.
                    </p>
                </div>
                <div class="col-lg-4">
                    <i class="fas fa-camera fa-10x text-warning opacity-75"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Filter -->
    <section class="py-4 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h5 class="mb-3">Filter by Category</h5>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary active" data-filter="all">All</button>
                        <button type="button" class="btn btn-outline-primary" data-filter="rising">One Billion Rising</button>
                        <button type="button" class="btn btn-outline-primary" data-filter="training">Training Sessions</button>
                        <button type="button" class="btn btn-outline-primary" data-filter="community">Community Circles</button>
                        <button type="button" class="btn btn-outline-primary" data-filter="advocacy">Advocacy Events</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Photo Gallery -->
    <section class="py-5">
        <div class="container">
            <div id="galleryContainer">
                <!-- Images will be loaded here dynamically -->
            </div>

            <!-- Call to Action -->
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <div class="card border-0 shadow bg-light">
                        <div class="card-body p-5">
                            <h4 class="fw-bold mb-3">Share Your Photos</h4>
                            <p class="lead mb-4">
                                Have photos from our events or activities? We'd love to feature them in our gallery.
                            </p>
                            <a href="contact.php" class="btn btn-primary btn-lg">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" class="img-fluid" style="max-height: 70vh;">
                    <p id="imageModalDescription" class="mt-3 text-muted"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer will be loaded by footer.js -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="footer.js?v=1.0"></script>
    <script>
        // Gallery loader class
        class GalleryLoader {
            constructor() {
                this.apiUrl = 'api/gallery.php';
                this.images = [];
                this.currentFilter = 'all';
                this.init();
            }

            async init() {
                await this.loadImages();
                this.renderGallery();
                this.setupEventListeners();
            }

            async loadImages() {
                try {
                    const response = await fetch(`${this.apiUrl}?_t=${Date.now()}`, {
                        method: 'GET',
                        headers: {
                            'Cache-Control': 'no-cache'
                        }
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        this.images = result.images;
                        console.log('Loaded images:', this.images);
                    } else {
                        console.error('Failed to load images:', result.message);
                        this.showFallbackContent();
                    }
                } catch (error) {
                    console.error('Error loading images:', error);
                    this.showFallbackContent();
                }
            }

            renderGallery() {
                const container = document.getElementById('galleryContainer');
                
                if (this.images.length === 0) {
                    this.showFallbackContent();
                    return;
                }

                // Group images by category
                const groupedImages = this.groupImagesByCategory();
                let html = '';

                // Render each category section
                Object.keys(groupedImages).forEach(category => {
                    if (this.currentFilter === 'all' || this.currentFilter === category) {
                        html += this.renderCategorySection(category, groupedImages[category]);
                    }
                });

                container.innerHTML = html;
            }

            groupImagesByCategory() {
                const grouped = {};
                
                this.images.forEach(image => {
                    if (!grouped[image.category]) {
                        grouped[image.category] = [];
                    }
                    grouped[image.category].push(image);
                });

                return grouped;
            }

            renderCategorySection(category, images) {
                const categoryInfo = this.getCategoryInfo(category);
                
                let html = `
                    <div class="row mb-5 category-section" data-category="${category}">
                        <div class="col-12">
                            <h3 class="mb-4">
                                ${categoryInfo.icon}
                                ${categoryInfo.title}
                            </h3>
                        </div>
                `;

                images.forEach(image => {
                    html += `
                        <div class="col-md-6 col-lg-4 mb-4 gallery-item" data-category="${image.category}">
                            <div class="card border-0 shadow">
                                <div class="position-relative overflow-hidden" style="cursor: pointer;" onclick="galleryLoader.showImageModal(${image.id})">
                                    <img src="${image.file_path}" class="card-img-top" style="height: 250px; object-fit: cover;" alt="${image.alt_text || image.title}">
                                    <div class="position-absolute top-0 end-0 m-2">
                                        ${this.getCategoryBadge(image.category)}
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title">${image.title}</h6>
                                    <p class="card-text small text-muted">${image.description || 'No description available'}</p>
                                </div>
                            </div>
                        </div>
                    `;
                });

                html += '</div>';
                return html;
            }

            getCategoryInfo(category) {
                const categories = {
                    rising: {
                        title: 'One Billion Rising 2024',
                        icon: '<i class="fas fa-fist-raised text-accent me-2"></i>'
                    },
                    training: {
                        title: 'Feminist Leadership Training',
                        icon: '<i class="fas fa-graduation-cap text-primary me-2"></i>'
                    },
                    community: {
                        title: 'Community Circles in Action',
                        icon: '<i class="fas fa-users text-warning me-2"></i>'
                    },
                    advocacy: {
                        title: 'Advocacy Events',
                        icon: '<i class="fas fa-bullhorn text-info me-2"></i>'
                    }
                };

                return categories[category] || {
                    title: 'Other Events',
                    icon: '<i class="fas fa-camera text-secondary me-2"></i>'
                };
            }

            getCategoryBadge(category) {
                const badges = {
                    rising: '<span class="badge bg-accent">Rising 2024</span>',
                    training: '<span class="badge bg-primary">Training</span>',
                    community: '<span class="badge bg-warning text-dark">Community</span>',
                    advocacy: '<span class="badge bg-info">Advocacy</span>'
                };
                return badges[category] || '<span class="badge bg-secondary">Event</span>';
            }

            setupEventListeners() {
                const filterButtons = document.querySelectorAll('[data-filter]');

                filterButtons.forEach(button => {
                    button.addEventListener('click', (e) => {
                        const filter = e.target.getAttribute('data-filter');
                        this.currentFilter = filter;
                        
                        // Update active button
                        filterButtons.forEach(btn => {
                            btn.classList.remove('active', 'btn-primary');
                            btn.classList.add('btn-outline-primary');
                        });
                        e.target.classList.remove('btn-outline-primary');
                        e.target.classList.add('active', 'btn-primary');
                        
                        // Re-render gallery with filter
                        this.renderGallery();
                    });
                });
            }

            showImageModal(imageId) {
                const image = this.images.find(img => img.id === imageId);
                if (!image) return;

                document.getElementById('imageModalTitle').textContent = image.title;
                document.getElementById('modalImage').src = image.file_path;
                document.getElementById('modalImage').alt = image.alt_text || image.title;
                document.getElementById('imageModalDescription').textContent = image.description || 'No description available';

                new bootstrap.Modal(document.getElementById('imageModal')).show();
            }

            showFallbackContent() {
                const container = document.getElementById('galleryContainer');
                container.innerHTML = `
                    <div class="row">
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-camera fa-4x text-muted mb-4"></i>
                            <h4 class="text-muted">Gallery Coming Soon</h4>
                            <p class="text-muted">We're currently updating our photo gallery. Please check back soon to see amazing photos from our events and activities!</p>
                            <a href="contact.php" class="btn btn-primary">Contact Us</a>
                        </div>
                    </div>
                `;
            }
        }

        // Initialize gallery loader when page loads
        let galleryLoader;
        document.addEventListener('DOMContentLoaded', function() {
            galleryLoader = new GalleryLoader();
        });
    </script>
</body>
</html>

