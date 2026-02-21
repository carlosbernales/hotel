<!-- Footer -->
<footer class="footer bg-dark text-light py-5">
    <div class="container">
        <div class="row">
            <!-- Hotel Info -->
            <div class="col-lg-4 mb-4">
                <h5 class="text-gold mb-3">Casa Estela</h5>
                <p>Your luxurious home away from home in the heart of the city. Experience comfort, elegance, and exceptional service.</p>
                <div class="social-links mt-3">
                    <a href="https://www.facebook.com/casaestelahotelcafe?_rdc=1&_rdr#" class="me-3"><i class="fab fa-facebook"></i></a>
                    <a href="https://www.instagram.com/accounts/login/?next=%2Fcasaestelahotelcafe%2F&source=omni_redirect" class="me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-4 mb-4">
                <h5 class="text-gold mb-3">Quick Links</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="roomss.php">Our Rooms</a></li>
                    <li><a href="events.php">Events & Celebrations</a></li>
                    <li><a href="cafes.php">Cafe</a></li>
                    <li><a href="table.php">Table Reservation</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-4 mb-4">
                <h5 class="text-gold mb-3">Contact Us</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Gov. B Marasigan St., Libis Calapan City 
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone me-2"></i>
                        0908-7474-892 / 043-441-6924
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-envelope me-2"></i>
                        casaestelahotelcafe@gmail.com
                    </li>
                </ul>
            </div>
        </div>

        <!-- Copyright -->
        <div class="row mt-4">
            <div class="col-12">
                <hr class="bg-light">
                <p class="text-center mb-0">
                    &copy; <?php echo date('Y'); ?> Casa Estela. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</footer>

<style>
/* Footer styles */
.footer {
    background-color: #1a1a1a !important;
}

.text-gold {
    color: #d4af37 !important;
}

.footer-links a {
    color: #fff;
    text-decoration: none;
    transition: color 0.3s ease;
    display: block;
    margin-bottom: 10px;
}

.footer-links a:hover {
    color: #d4af37;
}

.footer .social-links a {
    color: #fff;
    font-size: 1.5rem;
    transition: color 0.3s ease;
}

.footer .social-links a:hover {
    color: #d4af37;
}

.footer hr {
    opacity: 0.2;
}

.footer i {
    color: #d4af37;
}

@media (max-width: 768px) {
    .footer {
        text-align: center;
    }

    .footer .social-links {
        justify-content: center;
        margin-bottom: 20px;
    }
}
</style> 