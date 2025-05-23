<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vote - BOTOmasino</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/vote.css">
</head>
<body>
    <!--Navbar -->
    <nav class="navbar navbar-expand-lg custom-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="bi bi-mortarboard-fill me-2"></i>
                UST Supreme Student Council
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav ms-auto">
                    <a class="nav-item nav-link" href="home.php">Home</a>
                    <div class="vr mx-2 d-none d-lg-block"></div>
                    <a class="nav-item nav-link" href="candidate.php">Candidates</a>
                    <div class="vr mx-2 d-none d-lg-block"></div>
                    <a class="nav-item nav-link active" href="vote.php">Vote</a>
                    <div class="vr mx-2 d-none d-lg-block"></div>
                    <a class="nav-item nav-link" href="account.php">Account</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Voting Section -->
    <section class="vote-section">
        <div class="container">
            <div class="vote-container">
                <form id="votingForm" action="process_vote.php" method="post">
                    <!-- Progress Bar -->
                    <div class="progress-container">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 33.33%" aria-valuenow="33.33" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="step-indicator">
                            <div class="step active">President</div>
                            <div class="step">Vice President</div>
                            <div class="step">Secretary</div>
                        </div>
                    </div>

                    <!-- President Position -->
                    <div class="vote-card" id="presidentStep">
                        <h2 class="position-header">President</h2>
                        <p class="text-muted mb-4">Select your candidate for President</p>
                        
                        <div class="candidate-options">
                            <label class="candidate-option d-flex align-items-center">
                                <input type="radio" name="president" value="1" required>
                                <img src="../assets/candidates/president1.jpg" alt="John Doe" class="candidate-image">
                                <div class="candidate-info">
                                    <h3 class="candidate-name">John Doe</h3>
                                    <p class="candidate-position">4th Year BS Computer Science</p>
                                    <p class="candidate-platform">Platform: Student welfare, Campus facilities, Student representation</p>
                                </div>
                            </label>

                            <label class="candidate-option d-flex align-items-center">
                                <input type="radio" name="president" value="2">
                                <img src="../assets/candidates/president2.jpg" alt="Jane Smith" class="candidate-image">
                                <div class="candidate-info">
                                    <h3 class="candidate-name">Jane Smith</h3>
                                    <p class="candidate-position">4th Year BS Psychology</p>
                                    <p class="candidate-platform">Platform: Mental health, Academic support, Community engagement</p>
                                </div>
                            </label>
                        </div>

                        <div class="navigation-buttons">
                            <button type="button" class="btn btn-secondary" disabled>Previous</button>
                            <button type="button" class="btn btn-primary next-step">Next</button>
                        </div>
                    </div>

                    <!-- Vice President Position -->
                    <div class="vote-card" id="vicePresidentStep" style="display: none;">
                        <h2 class="position-header">Vice President</h2>
                        <p class="text-muted mb-4">Select your candidate for Vice President</p>
                        
                        <div class="candidate-options">
                            <label class="candidate-option d-flex align-items-center">
                                <input type="radio" name="vice_president" value="1" required>
                                <img src="../assets/candidates/vp1.jpg" alt="Mike Johnson" class="candidate-image">
                                <div class="candidate-info">
                                    <h3 class="candidate-name">Mike Johnson</h3>
                                    <p class="candidate-position">3rd Year BS Accountancy</p>
                                    <p class="candidate-platform">Platform: Financial transparency, Organization support, Leadership development</p>
                                </div>
                            </label>
                        </div>

                        <div class="navigation-buttons">
                            <button type="button" class="btn btn-secondary prev-step">Previous</button>
                            <button type="button" class="btn btn-primary next-step">Next</button>
                        </div>
                    </div>

                    <!-- Secretary Position -->
                    <div class="vote-card" id="secretaryStep" style="display: none;">
                        <h2 class="position-header">Secretary</h2>
                        <p class="text-muted mb-4">Select your candidate for Secretary</p>
                        
                        <div class="candidate-options">
                            <label class="candidate-option d-flex align-items-center">
                                <input type="radio" name="secretary" value="1" required>
                                <img src="../assets/candidates/secretary1.jpg" alt="Sarah Wilson" class="candidate-image">
                                <div class="candidate-info">
                                    <h3 class="candidate-name">Sarah Wilson</h3>
                                    <p class="candidate-position">3rd Year BS Journalism</p>
                                    <p class="candidate-platform">Platform: Communication channels, Documentation system, Feedback mechanism</p>
                                </div>
                            </label>
                        </div>

                        <div class="navigation-buttons">
                            <button type="button" class="btn btn-secondary prev-step">Previous</button>
                            <button type="submit" class="btn btn-success">Submit Vote</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const steps = ['presidentStep', 'vicePresidentStep', 'secretaryStep'];
            const progressBar = document.querySelector('.progress-bar');
            const stepIndicators = document.querySelectorAll('.step');
            let currentStep = 0;

            // Handle next button clicks
            document.querySelectorAll('.next-step').forEach(button => {
                button.addEventListener('click', function() {
                    if (validateCurrentStep()) {
                        document.getElementById(steps[currentStep]).style.display = 'none';
                        currentStep++;
                        document.getElementById(steps[currentStep]).style.display = 'block';
                        updateProgress();
                    }
                });
            });

            // Handle previous button clicks
            document.querySelectorAll('.prev-step').forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById(steps[currentStep]).style.display = 'none';
                    currentStep--;
                    document.getElementById(steps[currentStep]).style.display = 'block';
                    updateProgress();
                });
            });

            // Handle candidate selection
            document.querySelectorAll('.candidate-option').forEach(option => {
                option.addEventListener('click', function() {
                    // Remove selected class from all options in the current step
                    const currentStepOptions = document.querySelectorAll(`#${steps[currentStep]} .candidate-option`);
                    currentStepOptions.forEach(opt => opt.classList.remove('selected'));
                    
                    // Add selected class to clicked option
                    this.classList.add('selected');
                    
                    // Check the radio button
                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;
                });
            });

            function validateCurrentStep() {
                const currentStepElement = document.getElementById(steps[currentStep]);
                const selectedOption = currentStepElement.querySelector('input[type="radio"]:checked');
                
                if (!selectedOption) {
                    alert('Please select a candidate before proceeding.');
                    return false;
                }
                return true;
            }

            function updateProgress() {
                const progress = ((currentStep + 1) / steps.length) * 100;
                progressBar.style.width = `${progress}%`;
                progressBar.setAttribute('aria-valuenow', progress);

                // Update step indicators
                stepIndicators.forEach((indicator, index) => {
                    indicator.classList.remove('active', 'completed');
                    if (index === currentStep) {
                        indicator.classList.add('active');
                    } else if (index < currentStep) {
                        indicator.classList.add('completed');
                    }
                });
            }
        });
    </script>
</body>
</html>

