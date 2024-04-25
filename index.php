<?php
include 'includes/functions.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/font-awesome.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .logo {
            display: flex;
            align-items: center;
            flex-direction: column;
        }
        .logo img {
            margin-bottom: 5px;
        }
        .logo h3 {
            color: black;
            margin: 0;
            font-size: 1rem;
        }
    </style>
    <title>AIC Kijabe Hospital Telemedicine Platform</title>
</head>
<body>
    <section class="form-02-main">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="_lk_de">
                        <div class="form-03-main">
                            <div class="logo">
                                <img src="assets/images/user.png">
                                <h3>Telemedicine Platform</h3>
                            </div>
                            <!-- Updated form action to submit directly to the processing script -->
                            <form id="loginForm" method="POST" action="pages/processlogin.php">
                                <div class="form-group">
                                    <input type="username" name="username" class="form-control _ge_de_ol" placeholder="Enter Email" required aria-required="true">
                                </div>
                                <div class="form-group">
                                    <input type="password" name="password" class="form-control _ge_de_ol" placeholder="Enter Password" required aria-required="true">
                                </div>
                             
                                <div class="form-group">
                                    <button type="submit" class="_btn_04" style="width: 100%; color: white; background-color: #2b3990; border: none; border-radius: 20px; padding: 10px;">
                                        Login
                                    </button>
                                </div>
                                <!-- New Register Button -->
                                <div class="form-group">
                                    <a href="register.php" class="_btn_04" style="width: 100%; color: white; background-color: #2b3990; border: none; border-radius: 20px; padding: 10px;">
                                        Register
                                    </a>
                                </div>
                                   <div class="checkbox form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="rememberMe">
                                        <label class="form-check-label" for="rememberMe">
                                            Remember me
                                        </label>
                                    </div>
                                    <a href="#">Forgot Password</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
