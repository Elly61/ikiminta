<!-- Admin Sidebar Overlay (for mobile) -->
<div class="admin-sidebar-overlay" id="adminSidebarOverlay" onclick="closeAdminSidebar()"></div>

<!-- Admin Sidebar Navigation -->
<aside class="admin-sidebar" id="adminSidebar">
    <button class="admin-sidebar-close" onclick="closeAdminSidebar()">&times;</button>
    <div class="admin-logo">
        <h2>IKIMINA ADMIN</h2>
    </div>
    <nav class="admin-nav-menu">
        <ul>
            <li><a href="<?php echo BASE_URL; ?>admin/dashboard">📊 Dashboard</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/deposits">💳 Pending Deposits</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/loans">📋 Loan Requests</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/withdrawals">💸 Withdrawals</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/users">👥 Manage Users</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/settings">⚙️ Settings</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/auth/logout" class="logout">🚪 Logout</a></li>
        </ul>
    </nav>
</aside>

<script>
function openAdminSidebar() {
    document.getElementById('adminSidebar').classList.add('open');
    document.getElementById('adminSidebarOverlay').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeAdminSidebar() {
    document.getElementById('adminSidebar').classList.remove('open');
    document.getElementById('adminSidebarOverlay').classList.remove('active');
    document.body.style.overflow = '';
}
</script>
