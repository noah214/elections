<footer class="footer mt-auto py-4 bg-dark text-light">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3 mb-md-0">
                <h5 class="text-warning mb-3">BOTOmasino Elections</h5>
                <p class="mb-0">A secure and efficient way to conduct student elections at the University of Santo Tomas.</p>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <h5 class="text-warning mb-3">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="index.php" class="text-light text-decoration-none">Home</a></li>
                    <li><a href="candidates.php" class="text-light text-decoration-none">Candidates</a></li>
                    <li><a href="vote.php" class="text-light text-decoration-none">Vote</a></li>
                    <li><a href="login.php" class="text-light text-decoration-none">Login</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5 class="text-warning mb-3">Connect With Us</h5>
                <div class="d-flex gap-3">
                    <a href="#" class="text-light fs-4"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-light fs-4"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="text-light fs-4"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-light fs-4"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </div>
        <hr class="my-4 border-secondary">
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0">&copy; <?= date('Y') ?> BOTOmasino Elections. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0">University of Santo Tomas</p>
            </div>
        </div>
    </div>
</footer>

<style>
.footer {
    background-color: #000 !important;
    color: #ffc107 !important;
}

.footer a {
    transition: all 0.3s ease;
}

.footer a:hover {
    color: #e0a800 !important;
    text-decoration: none;
}

.footer .text-warning {
    color: #ffc107 !important;
}

.footer hr {
    border-color: #ffc107 !important;
    opacity: 0.2;
}

.footer .bi {
    transition: transform 0.3s ease;
}

.footer .bi:hover {
    transform: translateY(-3px);
}

@media (max-width: 767.98px) {
    .footer {
        text-align: center;
    }
    
    .footer .text-md-start,
    .footer .text-md-end {
        text-align: center !important;
    }
}
</style> 