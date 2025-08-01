<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Journey of Hope for Girls and Women in Eswatini</title>
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
                    <h1 class="display-4 fw-bold mb-4">Contact Us</h1>
                    <p class="lead">
                        Get in touch with Journey of Hope for Girls and Women in Eswatini. 
                        We're here to support, empower, and transform lives together.
                    </p>
                </div>
                <div class="col-lg-4">
                    <i class="fas fa-envelope fa-10x text-warning opacity-75"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Information and Form -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Contact Information -->
                <div class="col-lg-4 mb-5">
                    <h3 class="fw-bold mb-4">Get in Touch</h3>
                    
                    <div class="card border-0 shadow mb-4">
                        <div class="card-body p-4">
                            <h5 class="card-title">
                                <i class="fas fa-user-circle text-primary me-2"></i>
                                Founding Director
                            </h5>
                            <h6 class="fw-bold">Ms. Colani Hlatjwako</h6>
                            <hr>
                            <p class="mb-2">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                <a href="mailto:journeyofhopeeswatini@gmail.com">journeyofhopeeswatini@gmail.com</a>
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                <a href="mailto:colanibhlatjwako@gmail.com">colanibhlatjwako@gmail.com</a>
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-phone text-primary me-2"></i>
                                <a href="tel:+26876269383">+268 76269383</a>
                            </p>
                        </div>
                    </div>
                    
                    <div class="card border-0 shadow">
                        <div class="card-body p-4">
                            <h5 class="card-title">
                                <i class="fas fa-clock text-primary me-2"></i>
                                Office Hours
                            </h5>
                            <p class="mb-1"><strong>Monday - Friday:</strong> 8:00 AM - 5:00 PM</p>
                            <p class="mb-1"><strong>Saturday:</strong> 9:00 AM - 1:00 PM</p>
                            <p class="mb-0"><strong>Sunday:</strong> Closed</p>
                            <hr>
                            <p class="small text-muted mb-0">
                                <i class="fas fa-info-circle me-1"></i>
                                Emergency support available 24/7 through our Pillars of Support network
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Form -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow">
                        <div class="card-body p-5">
                            <h3 class="fw-bold mb-4">Send Us a Message</h3>
                            
                            <form>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="firstName" class="form-label">First Name *</label>
                                        <input type="text" class="form-control" id="firstName" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="lastName" class="form-label">Last Name *</label>
                                        <input type="text" class="form-control" id="lastName" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address *</label>
                                        <input type="email" class="form-control" id="email" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject *</label>
                                    <select class="form-select" id="subject" required>
                                        <option value="">Choose a subject...</option>
                                        <option value="join-circle">Join a Community Circle</option>
                                        <option value="volunteer">Volunteer Opportunities</option>
                                        <option value="training">Training Sessions</option>
                                        <option value="support">Request Support</option>
                                        <option value="partnership">Partnership Inquiry</option>
                                        <option value="one-billion-rising">One Billion Rising</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="region" class="form-label">Region in Eswatini</label>
                                    <select class="form-select" id="region">
                                        <option value="">Select your region...</option>
                                        <option value="hhohho">Hhohho</option>
                                        <option value="manzini">Manzini</option>
                                        <option value="shiselweni">Shiselweni</option>
                                        <option value="lubombo">Lubombo</option>
                                        <option value="other">Other/International</option>
                                    </select>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="message" class="form-label">Message *</label>
                                    <textarea class="form-control" id="message" rows="5" required placeholder="Please tell us how we can help you or how you'd like to get involved..."></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="newsletter">
                                        <label class="form-check-label" for="newsletter">
                                            I would like to receive updates about Journey of Hope programs and events
                                        </label>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Send Message
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Emergency Support -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <div class="alert alert-warning border-0 shadow">
                        <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                        <h4 class="fw-bold">Need Immediate Support?</h4>
                        <p class="mb-3">
                            If you or someone you know is experiencing gender-based violence or needs urgent support, 
                            our Pillars of Support volunteers are available 24/7.
                        </p>
                        <p class="fw-bold mb-0">
                            <i class="fas fa-phone me-2"></i>
                            Emergency Hotline: <a href="tel:+26876269383" class="text-dark">+268 76269383</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Community Circles Locations -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5 display-5 fw-bold">Find a Community Circle Near You</h2>
            <div class="row">
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card border-0 shadow text-center">
                        <div class="card-body p-4">
                            <i class="fas fa-map-marker-alt fa-3x text-primary mb-3"></i>
                            <h5>Hhohho Region</h5>
                            <p class="small text-muted">Multiple circles available</p>
                            <a href="mailto:journeyofhopeeswatini@gmail.com?subject=Hhohho Community Circle" class="btn btn-outline-primary btn-sm">Contact for Details</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card border-0 shadow text-center">
                        <div class="card-body p-4">
                            <i class="fas fa-map-marker-alt fa-3x text-primary mb-3"></i>
                            <h5>Manzini Region</h5>
                            <p class="small text-muted">Multiple circles available</p>
                            <a href="mailto:journeyofhopeeswatini@gmail.com?subject=Manzini Community Circle" class="btn btn-outline-primary btn-sm">Contact for Details</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card border-0 shadow text-center">
                        <div class="card-body p-4">
                            <i class="fas fa-map-marker-alt fa-3x text-primary mb-3"></i>
                            <h5>Shiselweni Region</h5>
                            <p class="small text-muted">Multiple circles available</p>
                            <a href="mailto:journeyofhopeeswatini@gmail.com?subject=Shiselweni Community Circle" class="btn btn-outline-primary btn-sm">Contact for Details</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card border-0 shadow text-center">
                        <div class="card-body p-4">
                            <i class="fas fa-map-marker-alt fa-3x text-primary mb-3"></i>
                            <h5>Lubombo Region</h5>
                            <p class="small text-muted">Multiple circles available</p>
                            <a href="mailto:journeyofhopeeswatini@gmail.com?subject=Lubombo Community Circle" class="btn btn-outline-primary btn-sm">Contact for Details</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <p class="lead">
                    <strong>42 Community Circles</strong> across all four regions of Eswatini
                </p>
                <p class="text-muted">
                    Safe spaces where women and girls can engage freely without discrimination or intimidation
                </p>
            </div>
        </div>
    </section>

    <!-- Footer will be loaded by footer.js -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="footer.js?v=1.0"></script>
</body>
</html>
                    