<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="global.css">
    <link rel="stylesheet" href="login.css">
    
   </head>
  <body>
        <nav class="navbar navbar-expand-lg custom-navbar">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <img src="your-logo.svg" width="30" height="30" class="d-inline-block align-top" alt="SSC Logo">
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
                        <a class="nav-item nav-link" href="vote.php">Vote</a>
                        <div class="vr mx-2 d-none d-lg-block"></div>
                        <a class="nav-item nav-link active" href="#" aria-current="page">Account</a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="container-fluid login-banner ">
            <div class="row h-100">
                <div class="col-5 d-flex justify-content-center flex-column align-items-center">
                    <div>
                        <h1 class="text-warning">University of Santo Tomas</h1>
                    </div>
                    <div class="text-white">
                        <h3>Supreme Student Council:</h3>
                    </div>
                    <div class="text-white">
                        <h3>BOTOmasino Elections</h3>
                    </div>
                </div>
                <div class="col d-flex align-items-center">
                    <div class="bg-white w-75 mx-auto shadow">
                        <form action="" method="post">
                            <div class="row">
                                <div class="col bg-warning block">
                                    
                                </div> 
                            </div>
                            <div class="row mx-5 mt-3">
                                <div class="col">
                                    <h1>Login</h1>
                                </div>
                            </div>
                            <div class="row mx-5 mt-3">
                                <div class="col">
                                    <div class="form-floating">
                                        <input type="text" name="username" id="username" class="form-control border-secondary" placeholder=" ">
                                        <label for="username" class="form-label">Username</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mx-5 mt-3">
                                <div class="col">
                                    <div class="form-floating">
                                        <input type="password" name="pass" id="pass" class="form-control border-secondary" placeholder=" ">
                                        <label for="pass" class="form-label">Password</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mx-5 mt-5 mb-1">
                                <div class="col">   
                                    <input type="submit" name="sub" class="btn btn-primary btn-block w-100 fw-bold" value="Login" id=sub >
                                </div>
                            </div>
                            <div class="row">
                                <div class="col d-flex justify-content-center">
                                    <p>Don't have an account? <a href="register.php">Sign Up Here!</a></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col bg-dark block">
                                    
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
  </body>
</html>