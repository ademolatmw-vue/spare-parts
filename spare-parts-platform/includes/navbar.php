<?php
/**
 * Shared navigation bar for PHP pages in /pages/.
 * Include this file AFTER session_start() and db_connect.php.
 *
 * Usage:
 *   session_start();
 *   include '../includes/db_connect.php';
 *   include '../includes/navbar.php';   // outputs the full <nav>...</nav> block
 *
 * $navbar_home (optional) — override the Home link href (default: '../index.php')
 */
$_nav_home = isset($navbar_home) ? $navbar_home : '../index.php';
?>
<style>
/* User menu dropdown — scoped inside nav */
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
    white-space: nowrap;
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
    min-width: 165px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.4);
    z-index: 10000;
}
.user-menu-wrap.open .user-dropdown { display: block; }
.user-dropdown a {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    color: #c9d1d9;
    text-decoration: none;
    font-size: 0.9rem;
    transition: background 0.2s;
}
.user-dropdown a:hover { background: rgba(255,255,255,0.1); color: #fff; }
.user-dropdown a:first-child { border-radius: 8px 8px 0 0; }
.user-dropdown a:last-child { border-radius: 0 0 8px 8px; color: #f85149; }
</style>

<nav class="navbar">
    <div class="container">
        <div class="logo">
            <h1><i class="fas fa-car"></i> SpareParts<span class="highlight">NG</span></h1>
        </div>
        <ul class="nav-links">
            <li><a href="<?php echo htmlspecialchars($_nav_home); ?>">Home</a></li>
            <li><a href="<?php echo htmlspecialchars($_nav_home); ?>#parts">Parts</a></li>
            <li><a href="<?php echo htmlspecialchars($_nav_home); ?>#about">About</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
            <li>
                <div class="user-menu-wrap" id="spngUserMenu">
                    <button class="user-menu-btn" onclick="
                        var m=document.getElementById('spngUserMenu');
                        m.classList.toggle('open');
                    ">
                        <i class="fas fa-user-circle"></i>
                        &#x1F464; Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        <i class="fas fa-chevron-down" style="font-size:0.7rem;"></i>
                    </button>
                    <div class="user-dropdown">
                        <a href="profile.php"><i class="fas fa-id-card"></i> My Profile</a>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </li>
            <?php else: ?>
            <li><a href="login.php" class="btn-nav">Login</a></li>
            <li><a href="register.php" class="btn-nav btn-primary-small">Register</a></li>
            <?php endif; ?>
        </ul>
        <div class="hamburger">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
    </div>
</nav>
<script>
// Close user menu when clicking outside
(function() {
    document.addEventListener('click', function(e) {
        var m = document.getElementById('spngUserMenu');
        if (m && !m.contains(e.target)) { m.classList.remove('open'); }
    });
})();
</script>
