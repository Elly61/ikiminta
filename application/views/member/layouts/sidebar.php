<!-- Sidebar Overlay (for mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- Member Sidebar Navigation -->
<aside class="sidebar" id="sidebar">
    <button class="sidebar-close" onclick="closeSidebar()">&times;</button>
    <div class="logo">
        <h2>IKIMINA</h2>
    </div>
    <nav class="nav-menu">
        <ul>
            <li><a href="<?php echo BASE_URL; ?>member/dashboard">📊 Dashboard</a></li>
            <li><a href="<?php echo BASE_URL; ?>member/deposits">💳 Deposits</a></li>
            <li><a href="<?php echo BASE_URL; ?>member/transfer">🔄 Transfer Funds</a></li>
            <li><a href="<?php echo BASE_URL; ?>member/loans">📋 Loans</a></li>
            <li><a href="<?php echo BASE_URL; ?>member/withdraw">💸 Withdrawals</a></li>
            <li><a href="<?php echo BASE_URL; ?>member/savings">💰 Savings</a></li>
            <li><a href="<?php echo BASE_URL; ?>member/transactions">📜 Transactions</a></li>
            <li><a href="<?php echo BASE_URL; ?>member/profile">👤 Profile</a></li>
            <li><a href="<?php echo BASE_URL; ?>member/auth/logout" class="logout">🚪 Logout</a></li>
        </ul>
    </nav>
</aside>

<script>
function openSidebar() {
    document.getElementById('sidebar').classList.add('open');
    document.getElementById('sidebarOverlay').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('active');
    document.body.style.overflow = '';
}
</script>
