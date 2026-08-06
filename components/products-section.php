<!-- Products Section -->
<section id="events" class="events products section">

    <div class="container-fluid px-4" data-aos="fade-up" data-aos-delay="100">

        <div class="swiper init-swiper">
            <script type="application/json" class="swiper-config">
                {
                    "loop": true,
                    "speed": 600,
                    "autoplay": {
                        "delay": 5000
                    },
                    "slidesPerView": "auto",
                    "pagination": {
                        "el": ".swiper-pagination",
                        "type": "bullets",
                        "clickable": true
                    },
                    "breakpoints": {
                        "320": {
                            "slidesPerView": 1,
                            "spaceBetween": 40
                        },
                        "1200": {
                            "slidesPerView": 3,
                            "spaceBetween": 1
                        }
                    }
                }
            </script>

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Related Products<br></h2>
                <p><span>Our </span> <span class="description-title">Popular Products</span></p>
            </div>
            <!-- End Section Title -->


            <div class="swiper-wrapper" >
                <?php
                if (mysqli_num_rows($select_res) > 0) {
                    while ($row = mysqli_fetch_assoc($select_res)) {
                        $imagePath = './assets/img/Products/Snacks/' . $row['image_url'];
                        $imagePath = str_replace(' ', '%20', $imagePath); // Encode spaces

                        echo "
                <div class='swiper-slide event-item d-flex flex-column justify-content-end' 
                    style=\"background-image: url('$imagePath'); background-size: cover; background-position: center;\">
                    <h3>{$row['title_english']}</h3>
                    <div class='price align-self-start'>{$row['title_sinhala']}</div>
                    <p class='description'>
                        {$row['description']}
                    </p>
                </div>
            ";
                    }
                }
                ?>
            </div>

            <div class="swiper-pagination"></div>

        </div>

    </div>

    </div>

    <div class="viewmorebtn d-flex w-100 align-items-center justify-content-center">
        <a href="./pages/products.php" class="btn btn-primary">View More <i class="fa-regular fa-circle-right"></i></a>
    </div>

</section>

<!-- /Products Section -->