// Navigation component for Journey of Hope website
document.addEventListener('DOMContentLoaded', function() {
    const navHTML = `
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top py-2">
            <div class="container">
                <a class="navbar-brand fw-bold" href="posts-manager.php">
                    <img src="logo.png" alt="Journey of Hope Logo" height="35" class="me-2">Journey of Hope
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link fw-bold" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link fw-bold" href="about.php">About</a></li>
                        <li class="nav-item"><a class="nav-link fw-bold" href="programs.php">Programs</a></li>
                        <li class="nav-item"><a class="nav-link fw-bold" href="get-involved.html.php">Get Involved</a></li>
                        <li class="nav-item"><a class="nav-link fw-bold" href="posts.php">Posts</a></li>
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
