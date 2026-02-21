<!-- Room Details Modal -->
<div id="roomDetailsModal" class="modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <div id="roomDetailsContent">
            <!-- Content will be loaded here via AJAX -->
            <div class="loading-spinner">
                <div class="spinner"></div>
                <p>Loading room details...</p>
            </div>
        </div>
    </div>
</div>

<style>
/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    overflow-y: auto;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.modal.show {
    display: block;
    opacity: 1;
}

.modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 30px;
    border-radius: 12px;
    width: 90%;
    max-width: 1000px;
    position: relative;
    transform: translateY(-50px);
    transition: transform 0.3s ease;
    box-shadow: 0 10px 50px rgba(0, 0, 0, 0.2);
}

.modal.show .modal-content {
    transform: translateY(0);
}

.close-modal {
    position: absolute;
    right: 25px;
    top: 15px;
    color: #7f8c8d;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.3s;
}

.close-modal:hover,
.close-modal:focus {
    color: #2c3e50;
    text-decoration: none;
}

.loading-spinner {
    text-align: center;
    padding: 40px 0;
}

.spinner {
    border: 4px solid rgba(0, 0, 0, 0.1);
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border-left-color: #3498db;
    animation: spin 1s linear infinite;
    margin: 0 auto 15px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        margin: 20px auto;
        padding: 20px 15px;
    }
}
</style>

<script>
// Global variable to store the modal
let roomDetailsModal;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize modal
    roomDetailsModal = document.getElementById('roomDetailsModal');
    const closeBtn = document.querySelector('.close-modal');
    
    // Close modal when clicking the X button
    closeBtn.addEventListener('click', closeModal);
    
    // Close modal when clicking outside the content
    window.addEventListener('click', function(event) {
        if (event.target === roomDetailsModal) {
            closeModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && roomDetailsModal.classList.contains('show')) {
            closeModal();
        }
    });
});

function showRoomDetails(roomId) {
    // Show loading state
    const modalContent = document.getElementById('roomDetailsContent');
    modalContent.innerHTML = `
        <div class="loading-spinner">
            <div class="spinner"></div>
            <p>Loading room details...</p>
        </div>
    `;
    
    // Show modal
    roomDetailsModal.classList.add('show');
    document.body.style.overflow = 'hidden';
    
    // Fetch room details via AJAX
    fetch(`get_room_details.php?id=${roomId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(html => {
            modalContent.innerHTML = html;
            // Initialize any carousels or other interactive elements here
            initRoomModalCarousel();
        })
        .catch(error => {
            console.error('Error loading room details:', error);
            modalContent.innerHTML = `
                <div class="error-message">
                    <p>Error loading room details. Please try again later.</p>
                    <button onclick="showRoomDetails(${roomId})" class="btn-retry">Retry</button>
                </div>
            `;
        });
}

function closeModal() {
    roomDetailsModal.classList.remove('show');
    document.body.style.overflow = 'auto';
    
    // Clear the content after animation completes
    setTimeout(() => {
        document.getElementById('roomDetailsContent').innerHTML = `
            <div class="loading-spinner">
                <div class="spinner"></div>
                <p>Loading room details...</p>
            </div>
        `;
    }, 300);
}

function initRoomModalCarousel() {
    // Initialize any carousel or other interactive elements here
    // This is a placeholder for any carousel initialization code
    const mainImage = document.getElementById('main-room-image');
    const thumbnails = document.querySelectorAll('.thumbnail');
    
    if (thumbnails.length > 0) {
        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', function() {
                const newSrc = this.getAttribute('data-image');
                if (newSrc && mainImage) {
                    mainImage.src = newSrc;
                    thumbnails.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });
    }
}
</script>
