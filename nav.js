// Navigation component for Journey of Hope website
document.addEventListener('DOMContentLoaded', function() {
    const navHTML = `
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top py-1">
            <div class="container">
                <a class="navbar-brand fw-bold" href="index.php">
                    <img src="logo.png" alt="Journey of Hope Logo" height="24" class="me-2">Journey of Hope
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link fw-bold" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link fw-bold" href="about.php">About</a></li>
                        <li class="nav-item"><a class="nav-link fw-bold" href="surveys.php">Research</a></li>
                        <li class="nav-item"><a class="nav-link fw-bold" href="programs.php">Programs</a></li>
                        <li class="nav-item"><a class="nav-link fw-bold" href="get-involved.php">Get Involved</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle fw-bold" href="#" id="mediaDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Media</a>
                            <ul class="dropdown-menu media-menu shadow-lg border-0 rounded-3 py-0 mt-2" aria-labelledby="mediaDropdown">
                                <li class="px-3 pt-3 pb-2 media-menu-header">
                                    <span class="text-uppercase fw-bold small text-primary">Media Center</span>
                                </li>
                                <li>
                                    <a class="dropdown-item media-menu-item" href="posts.php">
                                        <span class="media-icon bg-primary-subtle text-primary"><i class="fas fa-newspaper"></i></span>
                                        <span class="media-text">
                                            <strong class="d-block">Media</strong>
                                            <small class="text-muted">News, updates &amp; articles</small>
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item media-menu-item" href="reading-material.php">
                                        <span class="media-icon bg-warning-subtle text-warning"><i class="fas fa-book-open"></i></span>
                                        <span class="media-text">
                                            <strong class="d-block">Reading Material</strong>
                                            <small class="text-muted">Guides, reports &amp; resources</small>
                                        </span>
                                    </a>
                                </li>
                                <li class="media-menu-footer">
                                    <a class="dropdown-item text-center small fw-bold" href="gallery.php">
                                        <i class="fas fa-images me-1"></i>View Gallery
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item"><a class="nav-link fw-bold" href="gallery.php">Gallery</a></li>
                        <li class="nav-item"><a class="nav-link fw-bold" href="contact.php">Contact</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    `;
    
    // Insert navigation at the beginning of body
    document.body.insertAdjacentHTML('afterbegin', navHTML);
    
    // Set active page based on current URL
    const currentPage = window.location.pathname.split('/').pop() || 'index.php';
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPage || (currentPage === '' && href === 'index.php')) {
            link.classList.add('active');
        }
    });
});
