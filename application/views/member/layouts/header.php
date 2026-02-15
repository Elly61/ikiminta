<!-- Member Header -->
<header class="header">
    <div class="header-left" style="display: flex; align-items: center; gap: 15px;">
        <button class="hamburger-btn" onclick="openSidebar()">☰</button>
        <h1>IKIMINA Financial System</h1>
    </div>
    <div class="header-right">
        <div class="user-info">
            <span>Welcome, <?php echo $_SESSION['username']; ?></span>
            <a href="<?php echo BASE_URL; ?>member/profile">Profile</a>
        </div>
    </div>
</header>

<script>
// Ensure pages accessed via back button are reloaded so server-side session checks run
window.addEventListener('pageshow', function(event) {
    if (event.persisted || (window.performance && window.performance.getEntriesByType && window.performance.getEntriesByType('navigation').length && window.performance.getEntriesByType('navigation')[0].type === 'back_forward')) {
        window.location.reload();
    }
});
</script>
