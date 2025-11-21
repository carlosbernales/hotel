<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Disable caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once 'db.php';

// Debug connection
echo "<!--\n";
echo "Connection status: " . ($con ? "Connected" : "Not connected") . "\n";
if (!$con) {
    echo "Connection error: " . mysqli_connect_error() . "\n";
}

// Fetch about content
$about_query = "SELECT * FROM about_content WHERE id = 2";
$about_result = mysqli_query($con, $about_query);

echo "Query: " . $about_query . "\n";
if (!$about_result) {
    echo "Query error: " . mysqli_error($con) . "\n";
} else {
    $about_content = mysqli_fetch_assoc($about_result);
    echo "Content found: ";
    var_dump($about_content);
}
echo "\n-->";

// If no content exists, set default values
if (!$about_content) {
    $about_content = [
        'title' => 'About Us',
        'description' => 'Welcome to Casa Estela, where comfort meets elegance.'
    ];
}

// Fetch active slideshow images
$slideshow_query = "SELECT * FROM about_slideshow WHERE is_active = 1 ORDER BY display_order";
$slideshow_result = mysqli_query($con, $slideshow_query);
if (!$slideshow_result) {
    die("Slideshow query failed: " . mysqli_error($con));
}

// Store images in array
$slideshow_images = [];
while ($row = mysqli_fetch_assoc($slideshow_result)) {
    $slideshow_images[] = $row;
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
            white-space: pre-line;
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
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>
    
    <main>
        <section class="about-us">
            <div class="about-us-container">
                <div class="about-us-image">
                    <?php if (!empty($slideshow_images)): ?>
                        <?php foreach ($slideshow_images as $index => $image): ?>
                            <img src="admin/<?php echo htmlspecialchars($image['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($image['alt_text']); ?>" 
                                 class="<?php echo $index === 0 ? 'active' : ''; ?>">
                        <?php endforeach; ?>
                    <?php else: ?>
                        <img src="images/default.jpg" alt="Casa Estela" class="active">
                    <?php endif; ?>
                </div>
                <div class="about-us-content">
                    <h2><?php echo htmlspecialchars($about_content['title']); ?></h2>
                    <p><?php echo nl2br(htmlspecialchars($about_content['description'])); ?></p>
                </div>
            </div>
        </section>
    </main>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const images = document.querySelectorAll(".about-us-image img");
        if (images.length > 1) {
            let currentIndex = 0;

            setInterval(() => {
                images[currentIndex].classList.remove("active");
                currentIndex = (currentIndex + 1) % images.length;
                images[currentIndex].classList.add("active");
            }, 3000);
        }
    });
    </script>
</body>
</html> 