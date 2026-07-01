<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SpareParts NG - Find Original Automobile Spare Parts in Nigeria</title>
    <meta name="description" content="Find original automobile spare parts near you. Connect with verified vendors across Nigeria.">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        /* User menu dropdown */
        .user-menu-wrap {
            position: relative;
            display: inline-block;
        }
        .user-menu-btn {
            background: none;
            border: none;
            color: var(--white, #f0f6fc);
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 6px;
            transition: background 0.2s;
        }
        .user-menu-btn:hover { background: rgba(255,255,255,0.08); }
        .user-dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            background: #1d3557;
            border: 1px solid #30363d;
            border-radius: 8px;
            min-width: 160px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.4);
            z-index: 1000;
        }
        .user-menu-wrap:hover .user-dropdown,
        .user-menu-wrap.open .user-dropdown { display: block; }
        .user-dropdown a {
            display: block;
            padding: 10px 16px;
            color: #c9d1d9;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.2s;
        }
        .user-dropdown a:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .user-dropdown a:first-child { border-radius: 8px 8px 0 0; }
        .user-dropdown a:last-child { border-radius: 0 0 8px 8px; color: #f85149; }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <h1><i class="fas fa-car"></i> SpareParts<span class="highlight">NG</span></h1>
            </div>
            <ul class="nav-links">
                <li><a href="index.php" class="active">Home</a></li>
                <li><a href="#parts">Parts</a></li>
                <li><a href="#about">About</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                <li>
                    <div class="user-menu-wrap" id="userMenuWrap">
                        <button class="user-menu-btn" id="userMenuBtn" onclick="document.getElementById('userMenuWrap').classList.toggle('open')">
                            <i class="fas fa-user-circle"></i>
                            👤 Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                            <i class="fas fa-chevron-down" style="font-size:0.7rem;"></i>
                        </button>
                        <div class="user-dropdown">
                            <a href="pages/profile.php"><i class="fas fa-id-card"></i> My Profile</a>
                            <a href="pages/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                </li>
                <?php else: ?>
                <li><a href="pages/login.php" class="btn-nav">Login</a></li>
                <li><a href="pages/register.php" class="btn-nav btn-primary-small">Register</a></li>
                <?php endif; ?>
            </ul>
            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h2>Find Original Automobile Spare Parts <span class="highlight">Near You</span></h2>
            <p>Connect with verified vendors across Nigeria. Get genuine parts for your vehicle with just a few clicks.
            </p>

            <!-- Search Box -->
            <div class="search-container">
                <div class="search-tabs">
                    <button class="search-tab active" data-tab="parts">Search Parts</button>
                    <button class="search-tab" data-tab="shops">Find Shops</button>
                </div>
                <form class="search-form-main" id="searchForm" action="pages/search.php" method="GET">
                    <div class="search-input-group">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput"
                            placeholder="Search for parts (e.g., brake pads, alternator)...">
                    </div>

                    <button type="submit" class="btn-search">
                        <i class="fas fa-search"></i> Search
                    </button>
                </form>
                <div class="quick-filters">
                    <span class="filter-label">Popular:</span>
                    <button class="filter-chip" data-search="brake pads">Brake Pads</button>
                    <button class="filter-chip" data-search="battery">Battery</button>
                    <button class="filter-chip" data-search="alternator">Alternator</button>
                    <button class="filter-chip" data-search="oil filter">Oil Filter</button>
                    <button class="filter-chip" data-search="spark plugs">Spark Plugs</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Parts Categories Section -->
    <section id="parts" class="categories-section">
        <div class="container">
            <div class="section-header">
                <h2>Browse by <span class="highlight">Category</span></h2>
                <p>Find the exact part you need from our comprehensive categories</p>
            </div>

            <div class="categories-grid">
                <div class="category-card" data-category="engine">
                    <a href="pages/category.php?cat=engine">
                        <div class="category-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h3>Engine Parts</h3>
                        <p>Pistons, filters, gaskets, pumps</p>
                        <span class="parts-count"></span>
                    </a>
                </div>

                <div class="category-card" data-category="brakes">
                    <a href="pages/category.php?cat=brakes">
                        <div class="category-icon">
                            <i class="fas fa-stop-circle"></i>
                        </div>
                        <h3>Brakes</h3>
                        <p>Pads, discs, calipers, rotors</p>
                        <span class="parts-count"></span>
                    </a>
                </div>

                <div class="category-card" data-category="suspension">
                    <a href="pages/category.php?cat=suspension">
                        <div class="category-icon">
                            <i class="fas fa-random"></i>
                        </div>
                        <h3>Suspension</h3>
                        <p>Shocks, struts, springs, links</p>
                        <span class="parts-count"></span>
                    </a>
                </div>

                <div class="category-card" data-category="electrical">
                    <a href="pages/category.php?cat=electrical">
                        <div class="category-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3>Electrical</h3>
                        <p>Battery, alternator, starter, wiring</p>
                        <span class="parts-count"></span>
                    </a>
                </div>

                <div class="category-card" data-category="body">
                    <a href="pages/category.php?cat=body">
                        <div class="category-icon">
                            <i class="fas fa-car-side"></i>
                        </div>
                        <h3>Body Parts</h3>
                        <p>Bumpers, mirrors, lights, panels</p>
                        <span class="parts-count"></span>
                    </a>
                </div>

                <div class="category-card" data-category="transmission">
                    <a href="pages/category.php?cat=transmission">
                        <div class="category-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <h3>Transmission</h3>
                        <p>Gears, clutches, shafts, bearings</p>
                        <span class="parts-count"></span>
                    </a>
                </div>

                <div class="category-card" data-category="cooling">
                    <a href="pages/category.php?cat=cooling">
                        <div class="category-icon">
                            <i class="fas fa-temperature-low"></i>
                        </div>
                        <h3>Cooling</h3>
                        <p>Radiator, thermostat, water pump</p>
                        <span class="parts-count"></span>
                    </a>
                </div>

                <div class="category-card" data-category="exhaust">
                    <a href="pages/category.php?cat=exhaust">
                        <div class="category-icon">
                            <i class="fas fa-smog"></i>
                        </div>
                        <h3>Exhaust</h3>
                        <p>Mufflers, catalytic converters, pipes</p>
                        <span class="parts-count"></span>
                    </a>
                </div>

                <div class="category-card" data-category="interior">
                    <a href="pages/category.php?cat=interior">
                        <div class="category-icon">
                            <i class="fas fa-chair"></i>
                        </div>
                        <h3>Interior</h3>
                        <p>Seats, steering, dashboard, trim</p>
                        <span class="parts-count"></span>
                    </a>
                </div>

                <div class="category-card" data-category="wheels">
                    <a href="pages/category.php?cat=wheels">
                        <div class="category-icon">
                            <i class="fas fa-circle"></i>
                        </div>
                        <h3>Wheels &amp; Tires</h3>
                        <p>Tires, rims, valves, sensors</p>
                        <span class="parts-count"></span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- No home map - categories only -->

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="features-grid">
                <div class="feature-item">
                    <i class="fas fa-shield-alt"></i>
                    <h3>100% Verified</h3>
                    <p>All vendors are verified for authenticity</p>
                </div>

                <div class="feature-item">
                    <i class="fas fa-money-bill-wave"></i>
                    <h3>Best Prices</h3>
                    <p>Compare prices across multiple vendors</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-headset"></i>
                    <h3>24/7 Support</h3>
                    <p>We're here to help anytime</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>SpareParts<span class="highlight">NG</span></h3>
                    <p>Your trusted platform for finding original automobile spare parts in Nigeria.</p>
                    <div class="social-links">
                        <a href=""><i class="fab fa-facebook"></i></a>
                        <a href="https://x.com/spareparts_NG"><i class="fab fa-twitter"></i></a>
                        <a href=""><i class="fab fa-instagram"></i></a>

                    </div>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="#parts">Parts</a></li>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="pages/login.php">Login</a></li>
                        <li><a href="pages/register.php">Register</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>For Vendors</h4>
                    <ul>
                        <li><a href="#parts">find your automobile spareparts </a></li>
                        <li><a href="pages/register.php">Vendor Registration</a></li>
                        <li><a href="#">Partner Program</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact Us</h4>
                    <ul>
                        <li><i class="fas fa-envelope"></i> info@sparepartsng.com</li>
                        <li><i class="fas fa-phone"></i> +234 915 234 6276</li>
                        <li><i class="fas fa-map-marker-alt"></i> Lagos, Nigeria</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 SparePartsNG. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
    <script>
        // Close user menu when clicking outside
        document.addEventListener('click', function(e) {
            var wrap = document.getElementById('userMenuWrap');
            if (wrap && !wrap.contains(e.target)) {
                wrap.classList.remove('open');
            }
        });
    </script>
</body>

</html>
