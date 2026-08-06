<?php

require_once('inc/db.inc.php');

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
  <link href="assets/img/Wasantha Products Gray Logo.png" rel="icon">
  <link href="assets/img/Wasantha Products Gray Logo.png" rel="wasantha-products-logo">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Amatic+SC:wght@400;700&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" />

  <!-- Main CSS File -->
  <link href="assets/css/main.css?v=1.1" rel="stylesheet">

  <link rel="stylesheet" href="assets/css/main-section.css?v=1.1">


  <!-- =======================================================
  * Template Name: Yummy
  * Template URL: https://bootstrapmade.com/yummy-bootstrap-restaurant-website-template/
  * Updated: Aug 07 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->




</head>

<body class="index-page">

  <!-- Here the main section -->

  <?php

  include_once("./components/main-section.php");

  ?>

  <header id="header" class="header d-flex align-items-center sticky-top" style="display: none !important;">
    <div class="container position-relative d-flex align-items-center justify-content-between">

      <a href="index.html" class="logo d-flex align-items-center me-auto me-xl-0">
        <img src="assets/img/Wasantha Products Gray Logo.png" style="width: 75px; height: 75px !important;" alt="">

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
    <!-- About Section -->
    <section id="about" class="about section">
      <div class="container">

        <div class="row gy-4">
          <div class="col-lg-7" data-aos="fade-up" data-aos-delay="100">
            <img src="assets/img/Head Branch.jpeg" class="img-fluid mb-4" alt="">
            <div class="book-a-table">
              <h3><i class="fas fa-phone"></i> Call Now</h3>
              <a href="tel:+94763011685">
                <p>+94 76 301 1685</p>
              </a>
            </div>
          </div>
          <div class="col-lg-5" data-aos="fade-up" data-aos-delay="250">
            <div class="content ps-0 ps-lg-5">
              <h1 style="font-size: 48px;">Trusted Snack Food Manufacturer in Sri Lanka</h1>
              <p class="fst-italic">
                We are proud to be one of Sri Lanka’s leading snack food manufacturers with over 36 years of experience. Specializing in favorites like mixture murukku, garlic murukku, and many more, we deliver premium quality snacks loved for their authentic taste and consistent quality. Our commitment to tradition and excellence has made us a trusted name in households and stores across the island.
              </p>

              <div class="position-relative mt-4">
                <img src="assets/img/about-img.jpeg" class="img-fluid" alt="">
                <!-- Play Button Here -->
                <!-- <a href="https://www.youtube.com/watch?v=Y7f98aduVJ8" class="glightbox pulsating-play-btn"></a> -->
              </div>
            </div>
          </div>
        </div>

      </div>

    </section><!-- /About Section -->

    <!-- Here why us section -->

    <?php

    include_once("./components/why-us.php");

    ?>



    <!-- Products section -->

    <?php

    include_once("./components/products-section.php");

    ?>


    <!-- Gallery Section -->
    <section id="gallery" class="gallery section light-background">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Gallery</h2>
        <p><span>Check</span> <span class="description-title">Our Gallery</span></p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="swiper init-swiper">
          <script type="application/json" class="swiper-config">
            {
              "loop": true,
              "speed": 600,
              "autoplay": {
                "delay": 5000
              },
              "slidesPerView": "auto",
              "centeredSlides": true,
              "pagination": {
                "el": ".swiper-pagination",
                "type": "bullets",
                "clickable": true
              },
              "breakpoints": {
                "320": {
                  "slidesPerView": 1,
                  "spaceBetween": 0
                },
                "768": {
                  "slidesPerView": 3,
                  "spaceBetween": 20
                },
                "1200": {
                  "slidesPerView": 5,
                  "spaceBetween": 20
                }
              }
            }
          </script>
          <div class="swiper-wrapper align-items-center">
            <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery" href="./assets/img/gallery/gallery-1.jpeg"><img src="./assets/img/gallery/gallery-1.jpeg" class="img-fluid" alt=""></a></div>
            <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery" href="./assets/img/gallery/gallery-2.jpeg"><img src="./assets/img/gallery/gallery-2.jpeg" class="img-fluid" alt=""></a></div>
            <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery" href="./assets/img/gallery/gallery-3.jpeg"><img src="./assets/img/gallery/gallery-3.jpeg" class="img-fluid" alt=""></a></div>
            <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery" href="./assets/img/gallery/gallery-4.jpeg"><img src="./assets/img/gallery/gallery-4.jpeg" class="img-fluid" alt=""></a></div>
            <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery" href="./assets/img/gallery/gallery-5.jpeg"><img src="./assets/img/gallery/gallery-5.jpeg" class="img-fluid" alt=""></a></div>
            <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery" href="./assets/img/gallery/gallery-6.jpeg"><img src="./assets/img/gallery/gallery-6.jpeg" class="img-fluid" alt=""></a></div>
            <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery" href="./assets/img/gallery/gallery-7.jpeg"><img src="./assets/img/gallery/gallery-7.jpeg" class="img-fluid" alt=""></a></div>
            <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery" href="./assets/img/gallery/gallery-8.jpeg"><img src="./assets/img/gallery/gallery-8.jpeg" class="img-fluid" alt=""></a></div>
          </div>
          <div class="swiper-pagination"></div>
        </div>

      </div>

    </section>
    <!-- /Gallery Section -->


    <!-- Contact Section -->
    <?php
    include_once('./components/contact-us.php');
    ?>
    <!-- /Contact Section -->

  </main>

  <!-- Footer Start -->
  <?php
  include_once('./components/footer.php')
  ?>
  <!-- Footer End -->

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>