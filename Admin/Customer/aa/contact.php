<?php
// Start session and output buffering at the very beginning, before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();

// Remove authentication check - page is now public
require 'db_con.php';

// Fetch contact page content from database
try {
    // Use the existing PDO connection from db_con.php
    $sql = "SELECT * FROM page_content WHERE page_name = 'contact'";
    $stmt = $pdo->query($sql);
    
    // Default values in case database fetch fails
    $heroTitle = "Get in Touch";
    $heroSubtitle = "We'd love to hear from you. Send us a message and we'll respond as soon as possible.";
    $sectionTitle = "Contact Us";
    $sectionIntro = "Whether you have questions about our accommodations, want to make a special request, or need any assistance, our team is here to help. Reach out through any of the following channels.";
    
    // If data exists in database, use it
    if ($stmt && $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $heroTitle = $row['hero_title'] ?? $heroTitle;
        $heroSubtitle = $row['hero_subtitle'] ?? $heroSubtitle;
        $sectionTitle = $row['section_title'] ?? $sectionTitle;
        $sectionIntro = $row['section_intro'] ?? $sectionIntro;
    }
    
    // Fetch contact information from database
    $sql = "SELECT * FROM contact_info WHERE active = 1 ORDER BY display_order";
    $contactStmt = $pdo->query($sql);
    $contactInfo = [];
    
    if ($contactStmt) {
        $contactInfo = $contactStmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    // Log the error but continue with default values
    error_log("Database error: " . $e->getMessage());
    // We'll use the default values defined above
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casa Estela - Contact Us</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #d4af37;
            --secondary-color: #856f11;
            --text-color: #333;
            --bg-light: #f8f9fa;
            --white: #ffffff;
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-color);
            padding-top: 76px;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('images/bg.jpg');
            background-size: cover;
            background-position: center;
            color: var(--white);
            padding: 100px 0;
            text-align: center;
            margin-bottom: 50px;
        }

        .hero-content h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .hero-content p {
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Contact Section */
        .contact-container {
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            margin: -50px auto 50px;
            position: relative;
            z-index: 1;
            max-width: 1200px;
        }

        .contact-section {
            padding: 50px;
            border: none;
        }

        .section-title {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 30px;
            text-align: center;
            font-weight: 700;
        }

        .contact-intro {
            text-align: center;
            max-width: 800px;
            margin: 0 auto 40px;
            color: #666;
            line-height: 1.6;
        }

        /* Contact Form */
        .contact-form {
            background: var(--white);
            padding: 30px;
            border-radius: 15px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--text-color);
        }

        .form-control {
            border: 2px solid #eee;
            border-radius: 10px;
            padding: 12px;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
        }

        .btn-submit {
            background: var(--primary-color);
            color: var(--white);
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            transition: var(--transition);
            width: 100%;
        }

        .btn-submit:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        /* Contact Info */
        .contact-info {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 40px;
            border-radius: 15px;
            color: var(--white);
        }

        .contact-info-item {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            transition: var(--transition);
        }

        .contact-info-item:hover {
            transform: translateX(10px);
        }

        .contact-info-item i {
            font-size: 24px;
            margin-right: 15px;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .contact-info-item a {
            color: var(--white);
            text-decoration: none;
            font-size: 1.1rem;
            transition: var(--transition);
        }

        .contact-info-item a:hover {
            opacity: 0.8;
        }

        /* Alert Styles */
        .alert {
            border-radius: 10px;
            margin-top: 20px;
        }

        /* Footer */
        .footer {
            background-color: var(--primary-color);
            color: var(--white);
            text-align: center;
            padding: 15px 0;
            margin-top: 50px;
            border-radius: 0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }

            .contact-section {
                padding: 30px 20px;
            }

            .contact-info {
                margin-top: 30px;
            }

            .contact-info-item {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <?php include('nav.php'); ?>
    <?php include 'message_box.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1><?php echo htmlspecialchars($heroTitle); ?></h1>
                <p><?php echo htmlspecialchars($heroSubtitle); ?></p>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <div class="container">
        <div class="contact-container">
            <section class="contact-section">
                <h2 class="section-title"><?php echo htmlspecialchars($sectionTitle); ?></h2>
                <p class="contact-intro">
                    <?php echo htmlspecialchars($sectionIntro); ?>
                </p>
                <div class="row">
                    <!-- Contact Form -->
                    <div class="col-lg-7 mb-4 mb-lg-0">
                        <div class="contact-form">
                            <form id="contactForm" action="contactus.php" method="post">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="first_name">First Name</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" required 
                                               placeholder="Enter your first name">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="last_name">Last Name</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" required 
                                               placeholder="Enter your last name">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" required 
                                           placeholder="Enter your email address">
                                </div>
                                <div class="form-group">
                                    <label for="message">Message</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" required 
                                              placeholder="How can we help you?"></textarea>
                                </div>
                                <button type="submit" class="btn btn-submit">
                                    <i class="fas fa-paper-plane me-2"></i>Send Message
                                </button>
                            </form>
                            <div id="liveAlertPlaceholder"></div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="col-lg-5">
                        <div class="contact-info">
                            <?php if (!empty($contactInfo)): ?>
                                <?php foreach ($contactInfo as $info): ?>
                                    <div class="contact-info-item">
                                        <i class="<?php echo htmlspecialchars($info['icon_class']); ?>"></i>
                                        <a href="<?php echo htmlspecialchars($info['link']); ?>" 
                                           <?php echo $info['is_external'] ? 'target="_blank"' : ''; ?>>
                                            <?php echo htmlspecialchars($info['display_text']); ?>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- Fallback static content if no database entries -->
                                <div class="contact-info-item">
                                    <i class="fab fa-facebook"></i>
                                    <a href="https://web.facebook.com/casaestelahotelcafe" target="_blank">
                                        Casa Estela Boutique Hotel & Café
                                    </a>
                                </div>
                                <div class="contact-info-item">
                                    <i class="fas fa-envelope"></i>
                                    <a href="mailto:casaestelahotelcafe@gmail.com">
                                        casaestelahotelcafe@gmail.com
                                    </a>
                                </div>
                                <div class="contact-info-item">
                                    <i class="fas fa-phone"></i>
                                    <a href="tel:+09087474892">0908 747 4892</a>
                                </div>
                                <div class="contact-info-item">
                                    <i class="fab fa-twitter"></i>
                                    <a href="#" target="_blank">@casaestelahlcf</a>
                                </div>
                                <div class="contact-info-item">
                                    <i class="fab fa-instagram"></i>
                                    <a href="https://www.instagram.com/casaestelahotelcafe" target="_blank">
                                        @casaestelahotelcafe
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <?php include('footer.php'); ?> 

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Alert functionality
        const alertPlaceholder = document.getElementById('liveAlertPlaceholder');

        const showAlert = (message, type) => {
            const wrapper = document.createElement('div');
            wrapper.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
            alertPlaceholder.innerHTML = '';
            alertPlaceholder.append(wrapper);
        };

        // Form submission
        document.getElementById('contactForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            
            // Disable button and show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';

            const formData = new FormData(this);

            fetch('contactus.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    this.reset();
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                showAlert('Something went wrong. Please try again later.', 'danger');
                console.error('Error:', error);
            })
            .finally(() => {
                // Restore button state
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            });
        });
    </script>
</body>
</html>
