// Function to display package image in modal
function showPackageImageModal(imageSrc, packageTitle) {
    // Get the modal elements
    const imageModal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalTitle = document.getElementById('imageModalLabel');
    
    // Set the image source and title
    modalImage.src = imageSrc;
    modalTitle.textContent = packageTitle || 'Package Image';
    
    // Show the modal using Bootstrap
    const bsModal = new bootstrap.Modal(imageModal);
    bsModal.show();
}

// Add click handlers to all package images on page load
document.addEventListener('DOMContentLoaded', function() {
    // Find all images with paths from uploads/table_packages
    const packageImages = document.querySelectorAll('img[src*="uploads/table_packages"]');
    
    // Add click handler to each image
    packageImages.forEach(function(image) {
        image.style.cursor = 'pointer';
        image.title = 'Click to view larger image';
        
        image.addEventListener('click', function() {
            // Get the package title from the alt attribute or nearby title if available
            let packageTitle = this.alt || 'Package Image';
            
            // Find nearby title if image is in a card
            const card = this.closest('.card');
            if (card) {
                const titleElement = card.querySelector('.card-title');
                if (titleElement) {
                    packageTitle = titleElement.textContent;
                }
            }
            
            showPackageImageModal(this.src, packageTitle);
        });
    });
});
