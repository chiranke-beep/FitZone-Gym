<?php
session_start();
include 'config.php'; // Assuming a config.php file exists for database connection
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>FitZone Fitness Center</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="css/style.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTo.min.js"></script>
    </head>
<body>
    <!-- Header/Navbar -->
    <header class="header">
        <div class="container">
            <div class="logo">
                <a href="#home"><span>FitZone</span>Fitness</a>
            </div>
            <nav class="navbar">
                <ul>
                    <li><a href="#home" class="active">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#classes">Classes</a></li>
                    <li><a href="#trainers">Trainers</a></li>
                    <li><a href="#membership">Membership</a></li>
                    <li><a href="#testimonials">Testimonials</a></li>
                    <li><a href="#blog">Blog</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="user-info">
                        <div class="user-profile">
                            <i class="fas fa-user-circle"></i>
                            <span class="user-email"><?php echo htmlspecialchars($_SESSION['email']); ?></span>
                            <i class="fas fa-caret-down"></i>
                        </div>
                        <div class="user-dropdown">
                            <div class="user-details">
                                <p><strong>Membership:</strong> <span><?php echo htmlspecialchars($_SESSION['membership_plan'] ?? 'Premium'); ?></span></p>
                                <a href="<?php echo $_SESSION['role']; ?>-dashboard.php" class="btn btn-outline btn-small">Dashboard</a>
                                <a href="logout.php" class="btn btn-secondary btn-small">Logout</a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="auth.php#login" class="btn btn-outline">Login</a>
                    <a href="signup.php" class="btn btn-primary">Sign Up</a>
                <?php endif; ?>
            </div>
            <div class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Build Your <span>Perfect</span> Body</h1>
                <p>Join our gym and transform your body with professional trainers and top-notch equipment</p>
                <div class="hero-buttons">
                    <a href="<?php echo isset($_SESSION['user_id']) ? 'customer-dashboard.php' : 'signup.php'; ?>" class="btn btn-primary">Get Started</a>
                    <a href="#about" class="btn btn-outline">Learn More</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="images/hero-trainer.png" alt="Professional Trainer" class="main-image">
                <img src="images/dumbbell-icon.png" alt="Dumbbell" class="decorative-icon db-1">
                <img src="images/dumbbell-icon.png" alt="Dumbbell" class="decorative-icon db-2">
                <div class="achievement-badge badge-1">
                    <img src="images/certified-icon.png" alt="Certified">
                    <span>Certified Trainers</span>
                </div>
                <div class="achievement-badge badge-2">
                    <img src="images/equipment-icon.png" alt="Modern Equipment">
                    <span>Modern Equipment</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="stat-card">
                <img src="images/members-icon.png" alt="Members">
                <h3>100+</h3>
                <p>Active Members</p>
            </div>
            <div class="stat-card">
                <img src="images/trainers-icon.png" alt="Trainers">
                <h3>15+</h3>
                <p>Expert Trainers</p>
            </div>
            <div class="stat-card">
                <img src="images/classes-icon.png" alt="Classes">
                <h3>10+</h3>
                <p>Weekly Classes</p>
            </div>
            <div class="stat-card">
                <img src="images/awards-icon.png" alt="Awards">
                <h3>3+</h3>
                <p>Awards Won</p>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about">
        <div class="container">
            <div class="about-images">
                <img src="images/gym-interior.jpg" alt="Gym Interior" class="main-about-img">
                <img src="images/trainer-client.jpg" alt="Personal Training" class="inset-about-img">
                <div class="experience-badge">
                    <h3>10+</h3>
                    <p>Years Experience</p>
                </div>
            </div>
            <div class="about-content">
                <h2 class="section-title">About <span>FitZone</span></h2>
                <p class="section-subtitle">We're more than just a gym - we're a community</p>
                <p class="about-text">Founded in 2025, FitZone has grown to become one of the premier fitness destinations in the Kurunegala. Our state-of-the-art facility spans over 10,000 square feet with the latest equipment and dedicated spaces for all your fitness needs.</p>
                <div class="about-features">
                    <div class="feature">
                        <img src="images/checkmark-icon.png" alt="Checkmark">
                        <span>Certified Professional Trainers</span>
                    </div>
                    <div class="feature">
                        <img src="images/checkmark-icon.png" alt="Checkmark">
                        <span>Modern Fitness Equipment</span>
                    </div>
                    <div class="feature">
                        <img src="images/checkmark-icon.png" alt="Checkmark">
                        <span>Flexible Training Programs</span>
                    </div>
                    <div class="feature">
                        <img src="images/checkmark-icon.png" alt="Checkmark">
                        <span>Nutritional Guidance</span>
                    </div>
                </div>              
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services">
        <div class="container">
            <h2 class="section-title">Our <span>Services</span></h2>
            <p class="section-subtitle">Everything you need to reach your fitness goals</p>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <img src="images/weightlifting-icon.png" alt="Weightlifting">
                    </div>
                    <h3>Weight Training</h3>
                    <p>Build strength and muscle with our comprehensive weight training programs</p>
                    <a href="auth.php#login" class="service-link">Read More <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <img src="images/cardio-icon.png" alt="Cardio">
                    </div>
                    <h3>Cardio Programs</h3>
                    <p>Improve your endurance and cardiovascular health with our cardio equipment</p>
                    <a href="auth.php#login" class="service-link">Read More <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <img src="images/yoga-icon.png" alt="Yoga">
                    </div>
                    <h3>Yoga Classes</h3>
                    <p>Find balance and flexibility with our expert-led yoga sessions</p>
                    <a href="auth.php#login" class="service-link">Read More <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <img src="images/nutrition-icon.png" alt="Nutrition">
                    </div>
                    <h3>Nutrition Plans</h3>
                    <p>Customized meal plans to complement your fitness routine</p>
                    <a href="auth.php#login" class="service-link">Read More <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <img src="images/pt-icon.png" alt="Personal Training">
                    </div>
                    <h3>Personal Training</h3>
                    <p>One-on-one sessions with our certified personal trainers</p>
                    <a href="auth.php#login" class="service-link">Read More <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <img src="images/recovery-icon.png" alt="Recovery">
                    </div>
                    <h3>Recovery Therapy</h3>
                    <p>Specialized treatments to help your muscles recover faster</p>
                    <a href="auth.php#login" class="service-link">Read More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Classes Section -->
    <section id="classes" class="classes">
        <div class="container">
            <h2 class="section-title">Our <span>Classes</span></h2>
            <p class="section-subtitle">Join our group fitness sessions</p>
            <div class="class-tabs">
                <button class="tab-btn active" data-category="all">All Classes</button>
                <button class="tab-btn" data-category="strength">Strength</button>
                <button class="tab-btn" data-category="cardio">Cardio</button>
                <button class="tab-btn" data-category="flexibility">Flexibility</button>
            </div>
            <div class="classes-grid">
                <div class="class-card" data-category="strength">
                    <div class="class-image">
                        <img src="images/crossfit-class.jpg" alt="CrossFit Class">
                        <div class="class-time">7:00 - 8:00 AM</div>
                    </div>
                    <div class="class-info">
                        <h3>CrossFit</h3>
                        <div class="class-meta">
                            <span><i class="fas fa-user"></i> John Doe</span>
                            <span><i class="fas fa-calendar"></i> Mon, Wed, Fri</span>
                        </div>
                        <a href="<?php echo isset($_SESSION['user_id']) ? 'customer-dashboard.php#book-class' : 'auth.php#login'; ?>" class="btn btn-outline">Join Now</a>
                    </div>
                </div>
                <div class="class-card" data-category="cardio">
                    <div class="class-image">
                        <img src="images/spin-class.jpg" alt="Spin Class">
                        <div class="class-time">6:00 - 7:00 PM</div>
                    </div>
                    <div class="class-info">
                        <h3>Spin Cycling</h3>
                        <div class="class-meta">
                            <span><i class="fas fa-user"></i> Sarah Smith</span>
                            <span><i class="fas fa-calendar"></i> Tue, Thu</span>
                        </div>
                        <a href="<?php echo isset($_SESSION['user_id']) ? 'customer-dashboard.php#book-class' : 'auth.php#login'; ?>" class="btn btn-outline">Join Now</a>
                    </div>
                </div>
                <div class="class-card" data-category="flexibility">
                    <div class="class-image">
                        <img src="images/yoga-class.jpg" alt="Yoga Class">
                        <div class="class-time">8:00 - 9:00 AM</div>
                    </div>
                    <div class="class-info">
                        <h3>Power Yoga</h3>
                        <div class="class-meta">
                            <span><i class="fas fa-user"></i> Emily Johnson</span>
                            <span><i class="fas fa-calendar"></i> Daily</span>
                        </div>
                        <a href="<?php echo isset($_SESSION['user_id']) ? 'customer-dashboard.php#book-class' : 'auth.php#login'; ?>" class="btn btn-outline">Join Now</a>
                    </div>
                </div>
                <div class="class-card" data-category="strength">
                    <div class="class-image">
                        <img src="images/bodybuilding-class.jpg" alt="Bodybuilding Class">
                        <div class="class-time">5:00 - 6:30 PM</div>
                    </div>
                    <div class="class-info">
                        <h3>Bodybuilding</h3>
                        <div class="class-meta">
                            <span><i class="fas fa-user"></i> Mike Williams</span>
                            <span><i class="fas fa-calendar"></i> Mon, Wed, Fri</span>
                        </div>
                        <a href="<?php echo isset($_SESSION['user_id']) ? 'customer-dashboard.php#book-class' : 'auth.php#login'; ?>" class="btn btn-outline">Join Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trainers Section -->
    <section id="trainers" class="trainers">
        <div class="container">
            <h2 class="section-title">Meet Our <span>Trainers</span></h2>
            <p class="section-subtitle">Professional coaches to guide your journey</p>
            <div class="trainers-grid">
                <div class="trainer-card">
                    <div class="trainer-image">
                        <img src="images/trainer-john.jpg" alt="John Trainer">
                        <div class="trainer-social">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                    <div class="trainer-info">
                        <h3>John Anderson</h3>
                        <p>Strength Coach</p>
                        <div class="trainer-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                            <span>(120)</span>
                        </div>
                    </div>
                </div>
                <div class="trainer-card">
                    <div class="trainer-image">
                        <img src="images/trainer-sarah.jpg" alt="Sarah Trainer">
                        <div class="trainer-social">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                    <div class="trainer-info">
                        <h3>Sarah Miller</h3>
                        <p>Yoga Instructor</p>
                        <div class="trainer-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <span>(95)</span>
                        </div>
                    </div>
                </div>
                <div class="trainer-card">
                    <div class="trainer-image">
                        <img src="images/trainer-mike.jpg" alt="Mike Trainer">
                        <div class="trainer-social">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                    <div class="trainer-info">
                        <h3>Mike Roberts</h3>
                        <p>Cardio Specialist</p>
                        <div class="trainer-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <span>(110)</span>
                        </div>
                    </div>
                </div>
                <div class="trainer-card">
                    <div class="trainer-image">
                        <img src="images/trainer-lisa.jpg" alt="Lisa Trainer">
                        <div class="trainer-social">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                    <div class="trainer-info">
                        <h3>Lisa Chen</h3>
                        <p>Nutrition Coach</p>
                        <div class="trainer-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                            <span>(85)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Membership Plans -->
    <section id="membership" class="membership">
        <div class="container">
            <h2 class="section-title">Membership <span>Plans</span></h2>
            <p class="section-subtitle">Choose the perfect plan for your fitness journey</p>
            <div class="plans-grid">
                <div class="plan-card">
                    <div class="plan-header">
                        <h3>Basic</h3>
                        <div class="plan-price">
                            <span>LKR</span>3000<span>/mo</span>
                        </div>
                    </div>
                    <ul class="plan-features">
                        <li><i class="fas fa-check"></i> Gym Access</li>
                        <li><i class="fas fa-check"></i> Basic Equipment</li>
                        <li><i class="fas fa-times"></i> No Classes</li>
                        <li><i class="fas fa-times"></i> No Trainer</li>
                        <li><i class="fas fa-times"></i> No Spa Access</li>
                    </ul>
                    <a href="auth.php#login" class="btn btn-outline">Choose Plan</a>
                </div>
                <div class="plan-card popular">
                    <div class="popular-tag">Most Popular</div>
                    <div class="plan-header">
                        <h3>Premium</h3>
                        <div class="plan-price">
                            <span>LKR</span>7500<span>/mo</span>
                        </div>
                    </div>
                    <ul class="plan-features">
                        <li><i class="fas fa-check"></i> Full Gym Access</li>
                        <li><i class="fas fa-check"></i> All Equipment</li>
                        <li><i class="fas fa-check"></i> Group Classes</li>
                        <li><i class="fas fa-check"></i> 2 Trainer Sessions</li>
                        <li><i class="fas fa-times"></i> No Spa Access</li>
                    </ul>
                    <a href="auth.php#login" class="btn btn-primary">Choose Plan</a>
                </div>
                <div class="plan-card">
                    <div class="plan-header">
                        <h3>VIP</h3>
                        <div class="plan-price">
                            <span>LKR</span>15000<span>/mo</span>
                        </div>
                    </div>
                    <ul class="plan-features">
                        <li><i class="fas fa-check"></i> Full Gym Access</li>
                        <li><i class="fas fa-check"></i> All Equipment</li>
                        <li><i class="fas fa-check"></i> Unlimited Classes</li>
                        <li><i class="fas fa-check"></i> 5 Trainer Sessions</li>
                        <li><i class="fas fa-check"></i> Spa Access</li>
                    </ul>
                    <a href="auth.php#login" class="btn btn-outline">Choose Plan</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section id="testimonials" class="testimonials">
        <div class="container">
            <h2 class="section-title">What Our <span>Members Say</span></h2>
            <div class="testimonial-slider">
                <div class="testimonial-slide active">
                    <div class="testimonial-content">
                        <div class="testimonial-text">
                            <p>"Joining FitZone was the best decision I've made for my health. The trainers are knowledgeable and the community is supportive. I've lost 10 pounds in 1 months!"</p>
                        </div>
                        <div class="testimonial-author">
                            <img src="images/client-david.jpg" alt="David">
                            <div class="author-info">
                                <h4>Prabath pny</h4>
                                <span>Member since 2025</span>
                                <div class="rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-slide">
                    <div class="testimonial-content">
                        <div class="testimonial-text">
                            <p>"The variety of classes keeps me motivated. I've tried everything from spin to yoga and love them all. The facilities are always clean and well-maintained."</p>
                        </div>
                        <div class="testimonial-author">
                            <img src="images/client-jessica.jpg" alt="Jessica">
                            <div class="author-info">
                                <h4>Rushini madam</h4>
                                <span>Member since 2025</span>
                                <div class="rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-slide">
                    <div class="testimonial-content">
                        <div class="testimonial-text">
                            <p>"Best Gym and trainers i met so far, they are friendly and so much helpful"</p>
                        </div>
                        <div class="testimonial-author">
                            <img src="images/client-monica.jpg" alt="Monica">
                            <div class="author-info">
                                <h4>Shayanari</h4>
                                <span>Member since 2025</span>
                                <div class="rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="slider-controls">
                    <button class="slider-prev"><i class="fas fa-chevron-left"></i></button>
                    <button class="slider-next"><i class="fas fa-chevron-right"></i></button>
                </div>
                <div class="slider-dots">
                    <span class="dot active"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="cta" class="cta">
        <div class="container">
            <div class="cta-content">
                <h2>Ready To Transform <span>Your Body?</span></h2>
                <p>Join now and get 1 week free trial with no commitment</p>
                <a href="<?php echo isset($_SESSION['user_id']) ? 'customer-dashboard.php' : 'signup.php'; ?>" class="btn btn-primary">Get Started</a>
            </div>
            <div class="cta-image">
                <img src="images/cta-image.png" alt="Happy Member">
            </div>
        </div>
    </section>

    <!-- Blog Section -->
    <section id="blog" class="blog">
        <div class="container">
            <h2 class="section-title">Latest <span>Articles</span></h2>
            <p class="section-subtitle">Fitness tips and news from our experts</p>
            <div class="blog-grid">
                <article class="blog-card">
                    <div class="blog-image">
                        <img src="images/blog-nutrition.jpg" alt="Nutrition Tips">
                        <div class="blog-date">Feb 25, 2022</div>
                    </div>
                    <div class="blog-content">
                        <div class="blog-meta">
                            <span><i class="fas fa-user"></i> By Lisa Chen</span>
                            <span><i class="fas fa-comments"></i> 12 Comments</span>
                        </div>
                        <h3><a href="https://www.mayoclinichealthsystem.org/hometown-health/speaking-of-health/10-nutrition-myths-debunked">10 nutrition myths debunked</a></h3>
                        <p>Among the sea of information regarding nutrition is a tide of inaccuracies...</p>
                        <a href="https://www.mayoclinichealthsystem.org/hometown-health/speaking-of-health/10-nutrition-myths-debunked" class="read-more">Read More <i class="fas fa-arrow-right"></i></a>
                    </div>
                </article>
                <article class="blog-card">
                    <div class="blog-image">
                        <img src="images/blog-workout.jpg" alt="Workout Routine">
                        <div class="blog-date">Apr 10, 2023</div>
                    </div>
                    <div class="blog-content">
                        <div class="blog-meta">
                            <span><i class="fas fa-user"></i> By John Anderson</span>
                            <span><i class="fas fa-comments"></i> 8 Comments</span>
                        </div>
                        <h3><a href="https://www.planetfitness.com/community/articles/30-minute-morning-workout-routine-you-can-squeeze-work">A 30-Minute Morning Workout Routine</a></h3>
                        <p>Start your day off bright and early...</p>
                        <a href="https://www.planetfitness.com/community/articles/30-minute-morning-workout-routine-you-can-squeeze-work" class="read-more">Read More <i class="fas fa-arrow-right"></i></a>
                    </div>
                </article>
                <article class="blog-card">
                    <div class="blog-image">
                        <img src="images/blog-recovery.jpg" alt="Recovery Tips">
                        <div class="blog-date">Apr 5, 2023</div>
                    </div>
                    <div class="blog-content">
                        <div class="blog-meta">
                            <span><i class="fas fa-user"></i> By Sarah Miller</span>
                            <span><i class="fas fa-comments"></i> 15 Comments</span>
                        </div>
                        <h3><a href="https://www.thebodycoach.com/blog/the-importance-of-rest-days/">The Importance of Recovery Days</a></h3>
                        <p>When you're in the training zone...</p>
                        <a href="https://www.thebodycoach.com/blog/the-importance-of-rest-days/" class="read-more">Read More <i class="fas fa-arrow-right"></i></a>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col about-col">
                    <div class="footer-logo">
                        <a href="#home"><span>FitZone</span>Fitness</a>
                    </div>
                    <p class="footer-about">FitZone fitness is dedicated to helping you achieve your fitness goals with state-of-the-art facilities and expert trainers.</p>
                    <div class="footer-social">
                        <a href="https://www.facebook.com/"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://x.com/"><i class="fab fa-twitter"></i></a>
                        <a href="https://www.instagram.com/"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.youtube.com/"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-col links-col">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#classes">Classes</a></li>
                        <li><a href="#trainers">Trainers</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-col hours-col">
                    <h3>Opening Hours</h3>
                    <ul class="hours-list">
                        <li>
                            <span>Monday - Friday</span>
                            <span>6:00 AM - 10:00 PM</span>
                        </li>
                        <li>
                            <span>Saturday</span>
                            <span>7:00 AM - 8:00 PM</span>
                        </li>
                        <li>
                            <span>Sunday</span>
                            <span>8:00 AM - 6:00 PM</span>
                        </li>
                    </ul>
                </div>
                <div class="footer-col contact-col">
                    <h3>Contact Us</h3>
                    <ul class="contact-list">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>FitZone Fitness Center, Kurunegala, 10000</span>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <span>+94 815 644 32</span>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <span>info@fitzonefitnesscenter.com</span>
                        </li>
                    </ul>
                    <div class="newsletter">
                        <h4>Send Us a Message</h4>
                        <form class="newsletter-form" id="contact-form" action="contact.php" method="POST">
                            <div class="form-group">
                                <input type="email" id="contact-email" name="contact_email" placeholder="Your Email" required>
                            </div>
                            <div class="form-group">
                                <textarea id="contact-message" name="contact_message" placeholder="Enter your Message" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="payment-methods">
                    <i class="fab fa-cc-visa"></i>
                    <i class="fab fa-cc-mastercard"></i>
                    <i class="fab fa-cc-amex"></i>
                    <i class="fab fa-cc-paypal"></i>
                    <i class="fab fa-cc-apple-pay"></i>
                </div>
                <div class="copyright">
                    <p>© 2025 FitZone Fitness Center. All Rights Reserved. | <a href="privacy-policy.html">Privacy Policy</a> | <a href="terms-of-service.html">Terms of Service</a></p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <a href="#home" class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </a>
    
    <!-- JavaScript Files -->
    <script src="js/main.js"></script>
</body>
</html>