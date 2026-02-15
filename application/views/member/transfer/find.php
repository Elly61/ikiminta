<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer - Find Receiver - IKIMINA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/dashboard.css">
    <style>
        .member-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        .member-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid #2563eb;
        }
        .member-card h4 {
            margin: 0 0 8px 0;
            color: #111827;
        }
        .member-card p {
            margin: 4px 0;
            font-size: 13px;
            color: #6b7280;
        }
        .member-card .phone {
            font-weight: 600;
            color: #2563eb;
            font-family: monospace;
        }
        .search-box {
            margin: 20px 0;
        }
        .search-box input {
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <?php include VIEW_PATH . 'member/layouts/sidebar.php'; ?>

    <div class="main-content">
        <?php include VIEW_PATH . 'member/layouts/header.php'; ?>

        <div class="dashboard-content">
            <h1>Find Receiver</h1>
            <p>Select or copy a MOMO number to use in transfer:</p>

            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search by name or MOMO number...">
            </div>

            <div class="member-list" id="memberList">
                <?php if (!empty($members)): ?>
                    <?php foreach ($members as $member): ?>
                    <div class="member-card" data-search="<?php echo strtolower($member['username'] . ' ' . $member['first_name'] . ' ' . $member['last_name'] . ' ' . $member['phone_number']); ?>">
                        <h4>@<?php echo htmlspecialchars($member['username']); ?></h4>
                        <p><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></p>
                        <p class="phone" onclick="copyToClipboard('<?php echo $member['phone_number']; ?>')">
                            📱 <?php echo htmlspecialchars($member['phone_number']); ?>
                        </p>
                        <p style="font-size: 11px; margin-top: 8px;">
                            <a href="<?php echo BASE_URL; ?>member/transfer/create?momo=<?php echo urlencode($member['phone_number']); ?>" class="btn btn-primary" style="padding: 4px 8px; font-size: 12px;">Transfer</a>
                        </p>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No active members found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('MOMO number copied: ' + text);
    });
}

document.getElementById('searchInput').addEventListener('keyup', function(e) {
    const searchText = e.target.value.toLowerCase();
    const cards = document.querySelectorAll('.member-card');
    
    cards.forEach(card => {
        const searchData = card.getAttribute('data-search');
        if (searchData.includes(searchText)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>
<?php include VIEW_PATH . 'member/layouts/footer.php'; ?>
</body>
</html>