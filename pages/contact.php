<?php

require_once('../inc/db.inc.php');

$select_que = "SELECT * FROM popular_products";
$select_res = $conn->query($select_que);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Wasantha Products | Passionate About Taste</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <!-- Favicons -->
    <link href="../assets/img/Wasantha Products Gray Logo.png" rel="icon">
    <link href="../assets/img/Wasantha Products Gray Logo.png" rel="wasantha-products-logo">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Amatic+SC:wght@400;700&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="../assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="../assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" />

    <!-- Main CSS File -->
    <link href="../assets/css/main.css?v=1.1" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/main-section.css?v=1.1">


    <!-- =======================================================
  * Template Name: Yummy
  * Template URL: https://bootstrapmade.com/yummy-bootstrap-restaurant-website-template/
  * Updated: Aug 07 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->




</head>

<body class="index-page">

    <!-- Main Section -->

    <!-- Top Bar -->
    <?php
    include_once('../components/topbar.php');
    ?>

    <!-- Nav Bar -->
    <header>
        <nav class="container" style="z-index: 50;">
            <a href="#" class="logo">
                <div class="logo-icon">
                    <img src="../assets/img/Wasantha Products Gray Logo.png" width="100%" alt="">
                </div>
                <div class="logo-text">
                    <span class="brand-name">WASANTHA</span>
                    <span class="tagline">PRODUCTS</span>
                </div>
            </a>

            <ul class="nav-menu">
                <li><a href="../">HOME</a></li>
                <li><a href="./" >ABOUT</a></li>
                <li><a href="./products.php">PRODUCTS</a></li>
                <li><a href="./gallery.php">GALLERY</a></li>
                <li><a href="./contact.php" class="active">CONTACT</a></li>
            </ul>

            <div class="mobile-menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <div class="d-flex align-items-center justify-content-center text-center bg-light w-100 shadow" style="height: 400px;">
        <div class="hero-text d-flex flex-column align-items-center">
            <h1 class="hero-title" style="font-weight: 800;">Contact Us</h1>
            <p class="hero-description" style="width: 65%; font-size: 16px;">
                We are Sri Lanka's premium snacks & sweets manufacturing and distributing company,
                bringing you authentic flavors and traditional recipes crafted with love and quality ingredients.
            </p>
            <small>HOME | CONTACT US</small>
        </div>
    </div>



    <header id="header" class="header d-flex align-items-center sticky-top" style="display: none !important;">
        <div class="container position-relative d-flex align-items-center justify-content-between">

            <a href="index.html" class="logo d-flex align-items-center me-auto me-xl-0">
                <img src="../assets/img/Wasantha Products Gray Logo.png" style="width: 75px; height: 75px !important;" alt="">

            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="#hero" class="active">Home<br></a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#menu">Menu</a></li>
                    <li><a href="#events">Events</a></li>
                    <li><a href="#chefs">Chefs</a></li>
                    <li><a href="#gallery">Gallery</a></li>
                    <li class="dropdown"><a href="#"><span>Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                        <ul>
                            <li><a href="#">Dropdown 1</a></li>
                            <li class="dropdown"><a href="#"><span>Deep Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                                <ul>
                                    <li><a href="#">Deep Dropdown 1</a></li>
                                    <li><a href="#">Deep Dropdown 2</a></li>
                                    <li><a href="#">Deep Dropdown 3</a></li>
                                    <li><a href="#">Deep Dropdown 4</a></li>
                                    <li><a href="#">Deep Dropdown 5</a></li>
                                </ul>
                            </li>
                            <li><a href="#">Dropdown 2</a></li>
                            <li><a href="#">Dropdown 3</a></li>
                            <li><a href="#">Dropdown 4</a></li>
                        </ul>
                    </li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

            <a class="btn-get-started" href="index.html#book-a-table">Get In Touch</a>

        </div>
    </header>

    <main class="main">
        
        <!-- Contact Section -->
        <?php
        include_once('../components/contact-us.php');
        ?>
        <!-- /Contact Section -->

    </main>

    <!-- Footer Start -->
    <?php
    include_once('../components/footer.php')
    ?>
    <!-- Footer End -->

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader"></div>

    <!-- Vendor JS Files -->
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/vendor/php-email-form/validate.js"></script>
    <script src="../assets/vendor/aos/aos.js"></script>
    <script src="../assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="../assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="../assets/vendor/swiper/swiper-bundle.min.js"></script>

    <!-- Main JS File -->
    <script src="../assets/js/main.js"></script>

</body>

</html>