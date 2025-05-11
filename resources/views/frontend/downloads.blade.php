<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Downloads - SMM Tools</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4e44ff;
            --primary-dark: #3731c0;
            --secondary: #ff6b6b;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #00ca99;
        }
        
        body {
            background-color: #f5f7ff;
            font-family: 'Poppins', sans-serif;
            color: #444;
            overflow-x: hidden;
        }
        
        .page-header {
            background: #5844ff;
            color: white;
            padding: 40px 0 10px;
            position: relative;
            overflow: hidden;
            box-shadow: none;
        }
        
        .page-header::before {
            display: none;
        }
        
        .main-content {
            margin-top: 0;
            position: relative;
            z-index: 10;
        }
        
        .download-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            height: 100%;
            background: white;
        }
        
        .download-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(78, 68, 255, 0.15);
        }
        
        .card-body {
            padding: 30px;
        }
        
        .card-icon {
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-right: 20px;
            background: rgba(78, 68, 255, 0.1);
            color: var(--primary);
        }
        
        .card-footer {
            background: white;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            padding: 20px 30px 30px;
        }
        
        .btn-download {
            background: #5844ff;
            border: none;
            padding: 10px 25px;
            font-weight: 600;
            border-radius: 8px;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(78, 68, 255, 0.2);
            color: white;
            font-size: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-transform: none;
        }
        
        .btn-download:hover {
            background: #4733cf;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(78, 68, 255, 0.3);
            color: white;
        }
        
        .drive-folder-link {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 12px 20px;
            margin-top: 20px;
            margin-bottom: 15px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        
        .drive-folder-link a {
            color: white;
            font-weight: 500;
        }
        
        .drive-folder-link a:hover {
            color: #e9ecef;
        }
        
        .btn-white {
            background: white;
            color: #5844ff;
            border: none;
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }
        
        .btn-white:hover {
            background: #f8f9fa;
            color: #4733cf;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.12);
        }
        
        .feature-card {
            padding: 40px 30px;
            border-radius: 16px;
            background: white;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            height: 100%;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(78, 68, 255, 0.15);
            color: white;
        }
        
        .feature-card:hover::before {
            opacity: 1;
        }
        
        .feature-card:hover i, 
        .feature-card:hover h3 {
            color: white;
        }
        
        .feature-card i {
            font-size: 42px;
            transition: color 0.3s ease;
            color: var(--primary);
            margin-bottom: 20px;
        }
        
        .feature-card h3 {
            transition: color 0.3s ease;
        }
        
        .footer {
            background: var(--dark);
            color: #f8f9fa;
            padding: 50px 0 20px;
            margin-top: 80px;
        }
        
        .social-link {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transition: all 0.3s ease;
            margin: 0 5px;
        }
        
        .social-link:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }
        
        .alert-custom {
            border-radius: 16px;
            background: white;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 30px;
        }
        
        .alert-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(0, 202, 153, 0.1);
            color: var(--success);
            margin-right: 25px;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-weight: 600;
        }
        
        .section-title {
            position: relative;
            padding-bottom: 0;
            margin-bottom: 20px;
        }
        
        .section-title::after {
            display: none;
        }
        
        .get-started-banner {
            background: linear-gradient(135deg, #5844ff 0%, #4733cf 100%);
            color: white;
            padding: 30px 0;
            text-align: center;
            margin-bottom: 40px;
        }
        
        .get-started-banner h2 {
            margin: 0;
            font-weight: 600;
            font-size: 2.2rem;
        }
        
        .google-drive-icon {
            font-size: 1.5rem;
            margin-right: 10px;
            color: white;
        }
        
        .page-header h1 {
            font-size: 2.5rem;
        }
        
        .page-header .lead {
            font-size: 1.25rem !important;
        }
        
        // Add badge styling
        .badge {
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 6px;
        }
        
        .badge-light {
            background-color: #f5f7ff;
            color: #5844ff;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="page-header text-center">
        <div class="container">
            <div data-aos="fade-up" data-aos-duration="800">
                <h1 class="display-4 fw-bold mb-2">SMM Tools</h1>
                <p class="lead mb-3">Powerful tools to supercharge your social media management</p>
            </div>
            
            <div class="drive-folder-link" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
                <div class="row align-items-center">
                    <div class="col-8 mb-0">
                        <div class="d-flex align-items-center">
                            <i class="fab fa-google-drive google-drive-icon"></i>
                            <div>
                                <h4 class="m-0 fs-5">Access All Tools</h4>
                                <p class="m-0 opacity-75 small">View our complete collection in Google Drive</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <a href="{{ $mainFolderLink }}" target="_blank" class="btn btn-download btn-lg w-75">
                            Open Drive Folder
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Get Started Banner -->
    <div class="get-started-banner">
        <div class="container">
            <h2>Get Started Today</h2>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container main-content">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-8">
                <p class="lead">
                    Access our professional tools to help you manage your social media accounts more efficiently.
                </p>
            </div>
        </div>

        <div class="row">
            @foreach($downloadLinks as $key => $item)
            <div class="col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="download-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <div class="card-icon">
                                <i class="{{ $item['icon'] }} fa-2x"></i>
                            </div>
                            <div>
                                <h3>{{ $item['name'] }}</h3>
                            </div>
                        </div>
                        <p class="card-text fs-5">
                            {{ $item['description'] }}
                        </p>
                        <div class="d-flex flex-wrap mt-4">
                            <span class="badge bg-light text-primary me-2 mb-2 p-2">Easy Setup</span>
                            <span class="badge bg-light text-primary me-2 mb-2 p-2">Regular Updates</span>
                            <span class="badge bg-light text-primary me-2 mb-2 p-2">Free Support</span>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ $item['link'] }}" class="btn btn-download btn-lg w-75" target="_blank">
                            <i class="fas fa-download me-2"></i> DOWNLOAD NOW
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="row mt-5">
            <div class="col-12" data-aos="fade-up">
                <div class="alert-custom">
                    <div class="d-flex align-items-start">
                        <div class="alert-icon">
                            <i class="fas fa-info-circle fa-2x"></i>
                        </div>
                        <div>
                            <h4 class="mb-3">Installation & Support</h4>
                            <p class="mb-3 fs-5">
                                Our tools are designed to be easy to install and use. If you encounter any issues or need assistance, our support team is ready to help.
                            </p>
                            <div class="d-flex flex-wrap">
                                <a href="mailto:support@example.com" class="btn btn-outline-primary me-3 mb-2">
                                    <i class="fas fa-envelope me-2"></i> Contact Support
                                </a>
                                <a href="#" class="btn btn-outline-primary mb-2">
                                    <i class="fas fa-book me-2"></i> View Documentation
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container py-5 mt-4">
        <div class="row justify-content-center text-center mb-5" data-aos="fade-up">
            <div class="col-lg-8">
                <h2 class="section-title">Why Choose Our Tools</h2>
                <p class="lead">Built with performance, security, and usability in mind</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="0">
                <div class="feature-card text-center">
                    <i class="fas fa-rocket"></i>
                    <h3 class="mb-3">High Performance</h3>
                    <p>Our tools are optimized for speed and efficiency, using minimal system resources while delivering maximum results.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card text-center">
                    <i class="fas fa-shield-alt"></i>
                    <h3 class="mb-3">Enhanced Security</h3>
                    <p>Built with secure coding practices and regularly updated to ensure your data remains protected at all times.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-card text-center">
                    <i class="fas fa-cogs"></i>
                    <h3 class="mb-3">Customizable</h3>
                    <p>Easily adapt our tools to your workflow with extensive configuration options and flexible settings.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-card text-center">
                    <i class="fas fa-code-branch"></i>
                    <h3 class="mb-3">Regular Updates</h3>
                    <p>We continuously improve our tools with new features, bug fixes, and performance enhancements.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-card text-center">
                    <i class="fas fa-headset"></i>
                    <h3 class="mb-3">Dedicated Support</h3>
                    <p>Our support team is ready to assist you with any questions or issues you might encounter.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="500">
                <div class="feature-card text-center">
                    <i class="fas fa-user-friends"></i>
                    <h3 class="mb-3">Community</h3>
                    <p>Join our growing community of users to exchange tips, tricks, and best practices.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h4 class="mb-4">SMM Admin</h4>
                    <p>Providing powerful tools for social media management since 2023. Our mission is to help businesses and individuals grow their online presence.</p>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h4 class="mb-4">Quick Links</h4>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white opacity-75">Home</a></li>
                        <li class="mb-2"><a href="#" class="text-white opacity-75">About Us</a></li>
                        <li class="mb-2"><a href="#" class="text-white opacity-75">Services</a></li>
                        <li class="mb-2"><a href="#" class="text-white opacity-75">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h4 class="mb-4">Connect With Us</h4>
                    <div class="mb-4">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                    <p>Email: developer@aioinnovation.com</p>
                </div>
            </div>
            <hr class="mt-4 mb-4 opacity-25">
            <div class="text-center">
                <p class="mb-0">&copy; 2024 SMM Admin. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS animation
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 800,
                easing: 'ease-out-cubic',
                once: true
            });
        });
    </script>
</body>
</html> 