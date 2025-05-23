<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UST Supreme Student Council: BOTOmasino Elections</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/landing.css">
    <style>
        body { background: #f8f9fa; }
        .ust-header { background: #060841; color: #efb409; }
        .ust-header .navbar-brand, .ust-header .nav-link { color: #efb409 !important; font-weight: 600; }
        .ust-hero {
            background: linear-gradient(rgba(6,8,65,0.7),rgba(6,8,65,0.7)), url('../images/ust-bg.png') center/cover no-repeat;
            color: #fff;
            padding: 60px 0 0 0;
            min-height: 480px;
            position: relative;
        }
        .ust-hero .hero-title { font-size: 2.5rem; font-weight: 700; color: #ffd700; }
        .ust-hero .hero-sub { font-size: 2rem; font-weight: 600; color: #efb409; }
        .ust-hero .hero-announcement {
            background: rgba(0,0,0,0.7);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
            color: #fff;
        }
        .ust-hero .hero-logo {
            width: 80px; height: 80px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;
        }
        .ust-section-title {
            background: #060841;
            color: #ffd700;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-align: center;
            padding: 0.75rem 0;
            margin-bottom: 0;
        }
        .what-now-section { background: #efb409; }
        .what-now-card {
            background: #060841;
            color: #fff;
            border-radius: 20px;
            padding: 2rem 1.5rem;
            min-height: 220px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            position: relative;
        }
        .what-now-card.yellow {
            background: #efb409;
            color: #060841;
        }
        .what-now-btn {
            background: #ffd700;
            color: #060841;
            font-weight: 700;
            border: none;
            border-radius: 30px;
            padding: 0.5rem 2rem;
            margin-top: 1rem;
        }
        .what-now-btn.blue {
            background: #060841;
            color: #ffd700;
        }
        .mission-vision-section {
            background: #fff;
            padding: 2rem 0 1rem 0;
        }
        .mission-vision-title {
            color: #060841;
            font-weight: 700;
            font-size: 1.2rem;
        }
        .mission-vision-text {
            color: #222;
            font-size: 1rem;
        }
        .ust-footer {
            background: #060841;
            color: #ffd700;
            font-size: 0.95rem;
        }
        .ust-footer a { color: #ffd700; }
        .ust-footer .social-icons a { color: #ffd700; font-size: 1.5rem; margin-right: 1rem; }
        .back-to-top { color: #ffd700; text-decoration: underline; }
    </style>
</head>
<body>
    <!-- Header & Navbar -->
    <nav class="navbar navbar-expand-lg ust-header">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="../images/ust-logo.png" alt="UST Logo" width="40" class="me-2">
                UST Supreme Student Council: BOTOmasino Elections
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Candidates</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Vote</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Account</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="ust-hero d-flex align-items-center">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <div class="hero-title mb-2">University of Santo Tomas</div>
                    <div class="hero-sub mb-4">BOTOmasino Elections</div>
                    <div class="hero-announcement d-flex align-items-start">
                        <div class="hero-logo me-3">
                            <img src="../images/ust-logo.png" alt="UST Logo" width="60">
                        </div>
                        <div>
                            <div class="fw-bold mb-2">Hello, Thomasian. It's Election Day!</div>
                            <div style="font-size: 0.98rem;">
                                Election season has arrived at the University of Santo Tomas—a time for Thomasians to shape the future of our student body. As we choose new Supreme Student Council leaders, let us be guided by our core values: competence, compassion, and commitment. Voting is not just a right, but a duty to uphold Veritas and servant leadership. Choose the candidates who will lead with integrity and serve with heart. Vote wisely. Vote as a true Thomasian.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block">
                    <!-- Optionally, you can add a large UST image or keep empty for background focus -->
                </div>
            </div>
        </div>
    </section>

    <!-- What Now Section -->
    <div class="ust-section-title">WHAT NOW?</div>
    <section class="what-now-section py-4">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="what-now-card h-100">
                        <div class="mb-3"><i class="bi bi-megaphone-fill" style="font-size:2rem;"></i></div>
                        <div class="fw-bold mb-2">Make Your Voice Count.</div>
                        <div>Your voice matters. Take part in shaping the future of UST by joining the Central Student Council elections. Be the Thomasian who chooses to lead change—with one hand, one vote, and one heart.</div>
                        <a href="#" class="btn what-now-btn mt-3">VOTE NOW</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="what-now-card yellow h-100">
                        <div class="mb-3"><i class="bi bi-people-fill" style="font-size:2rem;"></i></div>
                        <div class="fw-bold mb-2">Get to Know the Candidates</div>
                        <div>Great leadership starts with informed choices. Learn about the candidates, their platforms, and their vision for UST. Choose those who truly represent your values as a Thomasian.</div>
                        <a href="#" class="btn what-now-btn blue mt-3">VIEW CANDIDATES</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission and Vision Section -->
    <section class="mission-vision-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mission-vision-title mb-2">Mission</div>
                    <div class="mission-vision-text">
                        The Central Student Council, in upholding its vision, commits to the holistic development of Thomasians by promoting competence, compassion, and commitment. It aims to be the voice of the student body, fostering unity and leadership, and ensuring that every Thomasian is heard and empowered.
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mission-vision-title mb-2">Vision</div>
                    <div class="mission-vision-text">
                        The Central Student Council envisions itself as the premier student government, recognized for its integrity, service, and dedication to the Thomasian community. It strives to create a positive impact and uphold the values of Veritas in all its endeavors.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="ust-footer pt-4 pb-2 mt-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <img src="../images/ust-logo.png" alt="UST Logo" width="32" class="me-2">
                    University of Santo Tomas<br>
                    Supreme Student Council: BOTOmasino Elections<br>
                    Academic Year 2024-2025
                </div>
                <div class="col-md-6 text-md-end">
                    <div>SECTIONS</div>
                    <a href="#" class="me-2">Home</a>
                    <a href="#" class="me-2">Candidates</a>
                    <a href="#" class="me-2">Vote</a>
                    <a href="#" class="me-2">Account</a>
                    <div class="social-icons mt-2">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-tiktok"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-3" style="border-color:#ffd700;">
            <div class="d-flex justify-content-between align-items-center">
                <div>&copy; 2024 Student Council. All rights reserved.</div>
                <a href="#" class="back-to-top">Back to Top</a>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

