<!-- Admin Header -->
<header class="admin-header">
    <div class="admin-header-left" style="display: flex; align-items: center; gap: 15px;">
        <button class="admin-hamburger-btn" onclick="openAdminSidebar()">☰</button>
        <h1>Admin Panel</h1>
    </div>
    <div class="admin-header-right">
        <div class="admin-user-info">
            <span>Admin: <?php echo $_SESSION['username']; ?></span>
            <a href="<?php echo BASE_URL; ?>admin/settings">Settings</a>
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
