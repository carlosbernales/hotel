<?php
require_once 'db_con.php';  // Change to the correct db file used in roomss.php

// Fetch about content
$about_query = "SELECT * FROM about_content WHERE id = 2";
$about_result = mysqli_query($con, $about_query);

if (!$about_result) {
    die("Query failed: " . mysqli_error($con));
}

$about_content = mysqli_fetch_assoc($about_result);

// If no content exists, set default values
if (!$about_content) {
    $about_content = [
        'title' => 'About Us',
        'description' => 'Welcome to Casa Estela, where comfort meets elegance.'
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($about_content['title']); ?> - Casa Estela</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .about-us {
            padding: 2rem;
            background-color: #fff;
            max-width: 1200px;
            margin: 80px auto 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .about-us-container {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            justify-content: center;
            min-height: 400px;
        }

        .about-us-image {
            flex: 1;
            max-width: 500px;
            min-width: 300px;
            position: relative;
            height: 400px;
        }

        .about-us-image img {
            width: 100%;
            height: 100%;
            box-shadow: 0 9px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

        .about-us-image img.active {
            opacity: 1;
        }

        .about-us-content {
            flex: 1;
            max-width: 600px;
            min-width: 300px;
            padding: 1rem;
        }

        .about-us-content h2 {
            color: #d4af37;
            text-align: center;
            margin: 0 0 1.5rem 0;
            font-size: 2rem;
            font-weight: 600;
        }

        .about-us-content p {
            margin: 0;
            color: #333;
            line-height: 1.8;
            font-size: 1.1rem;
        }

        .programmers-section {
            padding: 2rem;
            background-color: #fff;
            max-width: 1200px;
            margin: 20px auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .programmers-section h2 {
            color: #d4af37;
            text-align: center;
            margin: 0 0 2rem 0;
            font-size: 2rem;
            font-weight: 600;
        }

        .programmers {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            justify-content: center;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s ease-in-out;
        }

        .programmer-card {
            flex: 1;
            max-width: 280px;
            min-width: 250px;
            text-align: center;
            background-color: #f9f9f9;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .programmer-card:hover {
            transform: translateY(-5px);
        }

        .programmer-card img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin-bottom: 1.5rem;
            object-fit: cover;
            border: 3px solid #d4af37;
        }

        .programmer-card h3 {
            font-size: 1.3rem;
            color: #333;
            margin-bottom: 0.8rem;
            font-weight: 600;
        }

        .programmer-card p {
            font-size: 1rem;
            color: #666;
            margin: 0;
        }

        .programmers.show {
            opacity: 1;
            transform: translateY(0);
        }

        .location-section {
            padding: 2rem;
            background-color: #fff;
            max-width: 1200px;
            margin: 20px auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .location-section h2 {
            margin-bottom: 2rem;
        }

        .map-container {
            width: 100%;
            height: 400px;
            border-radius: 8px;
            overflow: hidden;
        }

        #map {
            width: 100%;
            height: 100%;
        }

        @media (max-width: 768px) {
            .about-us {
                margin: 60px auto 20px;
                padding: 1.5rem;
            }

            .about-us-container {
                flex-direction: column;
                min-height: auto;
                gap: 1.5rem;
            }

            .about-us-image {
                height: 300px;
                min-width: 100%;
            }

            .about-us-content {
                min-width: 100%;
                padding: 0;
            }

            .about-us-content h2 {
                font-size: 1.75rem;
                margin-bottom: 1rem;
            }

            .about-us-content p {
                font-size: 1rem;
                text-align: justify;
            }

            .programmers-section {
                padding: 1.5rem;
                margin: 20px 1rem;
            }

            .programmers-section h2 {
                font-size: 1.75rem;
                margin-bottom: 1.5rem;
            }

            .programmers {
                gap: 1.5rem;
            }

            .programmer-card {
                min-width: 100%;
                padding: 1.5rem;
            }

            .programmer-card img {
                width: 100px;
                height: 100px;
                margin-bottom: 1rem;
            }

            .programmer-card h3 {
                font-size: 1.2rem;
                margin-bottom: 0.5rem;
            }

            .programmer-card p {
                font-size: 0.9rem;
            }

            .location-section {
                padding: 1.5rem;
                margin: 20px 1rem;
            }

            .map-container {
                height: 300px;
            }
        }
    </style>
</head>
<body>
<?php include 'message_box.php'; ?>
    <?php include ('nav.php'); ?>

    <main>
        <section class="about-us">
            <div class="about-us-container">
                <div class="about-us-image">
                    <img src="images/garden.jpg" alt="Image 1" class="active" onerror="this.src='images/default.jpg'">
                    <img src="images/hall3.jpg" alt="Image 2" onerror="this.src='images/default.jpg'">
                    <img src="images/garden.jpg" alt="Image 3" onerror="this.src='images/default.jpg'">
                    <img src="images/hall.jpg" alt="Image 4" onerror="this.src='images/default.jpg'">
                    <img src="images/gard.jpg" alt="Image 5" onerror="this.src='images/default.jpg'">
                    <img src="images/garden1.jpg" alt="Image 6" onerror="this.src='images/default.jpg'">
                    <img src="images/family.jpg" alt="Image 7" onerror="this.src='images/default.jpg'">
                </div>
                <div class="about-us-content">
                    <h2><?php echo htmlspecialchars($about_content['title']); ?></h2>
                    <p><?php echo nl2br(htmlspecialchars($about_content['description'])); ?></p>
                </div>
            </div>
        </section>


        <!-- Add new location section -->
        <section class="location-section">
            <h2 class="text-center" style="color: #d4af37;">Our Location</h2>
            <div class="map-container">
                <img src="images/loc.png" alt="Map" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
            </div>
        </section>
    </main>
    <?php include('footer.php'); ?>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Slideshow functionality
            const images = document.querySelectorAll(".about-us-image img");
            let currentIndex = 0;

            setInterval(() => {
                images[currentIndex].classList.remove("active");
                currentIndex = (currentIndex + 1) % images.length;
                images[currentIndex].classList.add("active");
            }, 3000);

            // Programmers section animation
            const programmersSection = document.getElementById("programmers");
            if (programmersSection) {
                const observer = new IntersectionObserver(
                    (entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                programmersSection.classList.add("show");
                            }
                        });
                    },
                    { threshold: 0.5 }
                );

                observer.observe(programmersSection);
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fix image paths for hosted environment
        document.addEventListener('DOMContentLoaded', function() {
            // For hosted site using the public_path/Admin/Customer/aa structure
            const fullUrl = window.location.href;
            const baseUrl = fullUrl.substring(0, fullUrl.indexOf('/Admin/') + 7); // Get base up to /Admin/
            const customerPath = baseUrl + 'Customer/aa/';
            
            console.log('Site base URL:', baseUrl);
            console.log('Customer path:', customerPath);
            
            // Fix all image src attributes
            document.querySelectorAll('img').forEach(img => {
                const src = img.getAttribute('src');
                if (src && src.startsWith('images/')) {
                    img.src = customerPath + src;
                    console.log('Updated image path:', img.src);
                }
                
                // Add error handler for images
                img.onerror = function() {
                    console.log('Image failed to load:', this.src);
                    this.src = customerPath + 'images/default.jpg';
                };
            });
        });
    </script>
</body>
</html>
