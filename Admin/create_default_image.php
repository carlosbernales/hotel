<?php
// Create a 800x600 image
$image = imagecreatetruecolor(800, 600);

// Define colors
$bg_color = imagecolorallocate($image, 240, 240, 240);  // Light gray
$text_color = imagecolorallocate($image, 100, 100, 100);  // Dark gray
$border_color = imagecolorallocate($image, 200, 200, 200);  // Medium gray

// Fill background
imagefilledrectangle($image, 0, 0, 800, 600, $bg_color);

// Add border
imagerectangle($image, 0, 0, 799, 599, $border_color);

// Add text
$text = "Room Image Not Available";
$font = 5;  // Built-in font
$text_width = imagefontwidth($font) * strlen($text);
$text_height = imagefontheight($font);

// Center the text
$x = (800 - $text_width) / 2;
$y = (600 - $text_height) / 2;

// Draw the text
imagestring($image, $font, $x, $y, $text, $text_color);

// Create directories if they don't exist
if (!file_exists('assets/img/rooms')) {
    mkdir('assets/img/rooms', 0777, true);
}

// Save the image
imagejpeg($image, 'assets/img/rooms/default.jpg', 90);
imagedestroy($image);

echo "Default image created successfully in assets/img/rooms/default.jpg";
?> 