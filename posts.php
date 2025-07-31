<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts - Journey of Hope for Girls and Women in Eswatini</title>
    <link rel="icon" type="image/png" href="logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <script src="nav.js?v=1.1"></script>
</head>
<body>
    <!-- Navigation will be loaded by nav.js?v=1.1 -->

    <!-- Hero Section -->
    <section class="bg-primary text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">Latest Posts & Updates</h1>
                    <p class="lead">
                        Stay informed about our latest activities, success stories, and community impact. 
                        Read about the transformation happening across Eswatini.
                    </p>
                </div>
                <div class="col-lg-4">
                    <i class="fas fa-newspaper fa-10x text-warning opacity-75"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Posts -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Featured Post -->
                <div class="col-lg-8 mb-5">
                    <div id="featuredPost">
                        <!-- Featured post will be loaded here -->
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Quick Links</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2"><a href="programs.php" class="text-decoration-none">Our Programs</a></li>
                                <li class="mb-2"><a href="get-involved.html.php" class="text-decoration-none">Get Involved</a></li>
                                <li class="mb-2"><a href="contact.php" class="text-decoration-none">Contact Us</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="card border-0 shadow">
                        <div class="card-body">
                            <h5 class="card-title">Follow Our Impact</h5>
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <div class="bg-light p-3 rounded">
                                        <h4 class="text-primary mb-0">42</h4>
                                        <small>Circles</small>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="bg-light p-3 rounded">
                                        <h4 class="text-primary mb-0">4</h4>
                                        <small>Regions</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Posts Grid -->
            <div class="row mt-5">
                <div class="col-12">
                    <h3 class="mb-4">Recent Updates</h3>
                </div>
                
                <div id="postsGrid" class="col-12">
                    <!-- Posts will be loaded here -->
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
                        Subscribe to our newsletter to receive the latest updates about our work 
                        and the impact we're making together.
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
    <script src="footer.js?v=1.0"></script>
    <script>
        // Posts loader class
        class PostsLoader {
            constructor() {
                this.apiUrl = 'api/posts.php';
                this.posts = [];
                this.init();
            }

            async init() {
                await this.loadPosts();
                this.renderPosts();
            }

            async loadPosts() {
                try {
                    const response = await fetch(`${this.apiUrl}?_t=${Date.now()}`, {
                        method: 'GET',
                        headers: {
                            'Cache-Control': 'no-cache'
                        }
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        // Filter only published posts
                        this.posts = result.posts.filter(post => post.status === 'published');
                        console.log('Loaded posts:', this.posts);
                    } else {
                        console.error('Failed to load posts:', result.message);
                        this.showFallbackContent();
                    }
                } catch (error) {
                    console.error('Error loading posts:', error);
                    this.showFallbackContent();
                }
            }

            renderPosts() {
                if (this.posts.length === 0) {
                    this.showFallbackContent();
                    return;
                }

                // Render featured post (first/latest post)
                this.renderFeaturedPost();
                
                // Render posts grid (remaining posts)
                this.renderPostsGrid();
            }

            renderFeaturedPost() {
                const featuredPost = this.posts[0];
                if (!featuredPost) return;

                const featuredContainer = document.getElementById('featuredPost');
                const publishedDate = new Date(featuredPost.published_at || featuredPost.created_at);
                
                featuredContainer.innerHTML = `
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-5">
                            <span class="badge bg-primary mb-3">Featured</span>
                            <h2 class="card-title mb-3">${featuredPost.title}</h2>
                            <p class="text-muted mb-3">
                                <i class="fas fa-calendar me-2"></i>${publishedDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })} | 
                                <i class="fas fa-user me-2"></i>By ${featuredPost.author_name || 'Admin'}
                            </p>
                            <p class="card-text lead mb-4">
                                ${featuredPost.excerpt || this.truncateText(featuredPost.content, 200)}
                            </p>
                            <div class="card-text mb-4">
                                ${this.truncateText(featuredPost.content, 500)}
                            </div>
                            <button class="btn btn-primary" onclick="postsLoader.showFullPost(${featuredPost.id})">Read Full Story</button>
                        </div>
                    </div>
                `;
            }

            renderPostsGrid() {
                const postsContainer = document.getElementById('postsGrid');
                const recentPosts = this.posts.slice(1, 7); // Skip featured post, show next 6

                if (recentPosts.length === 0) {
                    postsContainer.innerHTML = '<div class="col-12 text-center"><p class="text-muted">No additional posts available.</p></div>';
                    return;
                }

                let postsHTML = '';
                
                recentPosts.forEach(post => {
                    const publishedDate = new Date(post.published_at || post.created_at);
                    const categoryBadge = this.getCategoryBadge(post.category);
                    
                    postsHTML += `
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-0 shadow">
                                <div class="card-body">
                                    ${categoryBadge}
                                    <h5 class="card-title">${post.title}</h5>
                                    <p class="text-muted small mb-3">${publishedDate.toLocaleDateString()}</p>
                                    <p class="card-text">
                                        ${post.excerpt || this.truncateText(post.content, 120)}
                                    </p>
                                    <button class="btn btn-outline-primary btn-sm" onclick="postsLoader.showFullPost(${post.id})">Read More</button>
                                </div>
                            </div>
                        </div>
                    `;
                });

                postsContainer.innerHTML = postsHTML;
            }

            getCategoryBadge(category) {
                const badges = {
                    community: '<span class="badge bg-primary mb-2">Community Impact</span>',
                    training: '<span class="badge bg-warning text-dark mb-2">Training</span>',
                    advocacy: '<span class="badge bg-info mb-2">Advocacy</span>',
                    economic: '<span class="badge bg-success mb-2">Economic Empowerment</span>',
                    partnership: '<span class="badge bg-secondary mb-2">Partnership</span>',
                    transformation: '<span class="badge bg-danger mb-2">Transformation</span>',
                    rising: '<span class="badge bg-dark mb-2">One Billion Rising</span>'
                };
                return badges[category] || '<span class="badge bg-light text-dark mb-2">Update</span>';
            }

            truncateText(text, maxLength) {
                if (text.length <= maxLength) return text;
                return text.substr(0, maxLength) + '...';
            }

            showFullPost(postId) {
                const post = this.posts.find(p => p.id === postId);
                if (!post) return;

                // Create modal to show full post
                const modal = document.createElement('div');
                modal.className = 'modal fade';
                modal.innerHTML = `
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">${post.title}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-muted mb-3">
                                    <i class="fas fa-calendar me-2"></i>${new Date(post.published_at || post.created_at).toLocaleDateString()} | 
                                    <i class="fas fa-user me-2"></i>By ${post.author_name || 'Admin'}
                                </p>
                                <div style="white-space: pre-wrap; line-height: 1.6;">${post.content}</div>
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
                document.getElementById('featuredPost').innerHTML = `
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-5">
                            <span class="badge bg-primary mb-3">Featured</span>
                            <h2 class="card-title mb-3">Welcome to Journey of Hope</h2>
                            <p class="card-text lead mb-4">
                                Empowering women and girls across Eswatini through community circles, 
                                advocacy, and transformative leadership training.
                            </p>
                            <p class="card-text">
                                Our 42 community circles across all four regions work together to create 
                                safe spaces for women to engage, learn, and grow.
                            </p>
                            <a href="about.php" class="btn btn-primary">Learn More About Us</a>
                        </div>
                    </div>
                `;

                document.getElementById('postsGrid').innerHTML = `
                    <div class="col-12 text-center">
                        <p class="text-muted">Posts are currently being updated. Please check back soon!</p>
                    </div>
                `;
            }
        }

        // Initialize posts loader when page loads
        let postsLoader;
        document.addEventListener('DOMContentLoaded', function() {
            postsLoader = new PostsLoader();
        });
    </script>
</body>
</html>
