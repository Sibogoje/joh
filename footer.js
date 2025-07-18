// Footer component for Journey of Hope website
document.addEventListener('DOMContentLoaded', function() {
    const footerHTML = `
        <footer class="bg-dark text-white py-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Journey of Hope for Girls and Women</h5>
                        <p>Turning Pain into Power</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p>Email: journeyofhopeeswatini@gmail.com</p>
                        <p>Phone: +268 76269383</p>
                    </div>
                </div>
                <hr>
                <div class="text-center" style="color: #fff;">
                    <p>&copy; ${new Date().getFullYear()} Journey of Hope for Girls and Women in Eswatini. All rights reserved.</p>
                    <p class="small text-white">
                        <i class="fas fa-heart text-danger"></i> 
                        Developed with love by Siboniso Sibandze
                    </p>
                </div>
            </div>
        </footer>
    `;
    
    // Insert footer at the end of body
    document.body.insertAdjacentHTML('beforeend', footerHTML);
});
