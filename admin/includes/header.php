<?php
// PHP logic to identify the current page for sidebar highlighting
$current_page = basename($_SERVER['PHP_SELF']);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fallback for admin session
if (!isset($_SESSION['admin_email'])) {
    $_SESSION['admin_email'] = "Admin"; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURRYMART-ADMIN</title>
     <link rel="icon" type="image/png" href="uploads/logo.jpg">

    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><path fill='%23518992' d='M256 160c12.5 0 24.7 1.4 36.3 4.1C303.4 134.4 332.2 112 365.4 112c41.3 0 74.8 33.5 74.8 74.8c0 20.3-8.1 38.8-21.3 52.3c35.4 14.8 59.1 49.3 59.1 88.9c0 62.4-56.7 112-126.6 112c-4.4 0-8.8-.2-13.1-.6c-17.5 12.1-38.6 19.3-61.4 19.3c-23.7 0-45.5-7.7-63.1-20.7c-4 0-8.1 .1-12.1 .1c-71.1 0-128.7-49.6-128.7-112c0-41.2 24.9-76.8 61.9-91.2c-12.5-13.3-20.2-31.1-20.2-50.7c0-41.3 33.5-74.8 74.8-74.8c32.1 0 60 21.3 70.3 50.4c12-3.1 24.9-4.7 38.3-4.7z'/></svg>">
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* ===== PRELOADER STYLES ===== */
        #furrymart-preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }
        
        #furrymart-preloader.fade-out {
            opacity: 0;
            visibility: hidden;
        }
        
        .preloader-content {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }
        
        .preloader-animation {
            width: 200px;
            height: 200px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(81, 137, 146, 0.2);
        }
        
        .preloader-animation video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .preloader-text {
            font-size: 24px;
            font-weight: 800;
            color: #518992;
            letter-spacing: 2px;
            animation: pulse 1.5s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(1.05); }
        }
        
        body.loading {
            overflow: hidden;
        }
        
        :root {
            --sidebar-width: 280px;
            --primary: #518992ff; 
            --bg-dark: #0f172a;   
            --bg-light: #f8fafc;  
            --text-main: #1e293b;
            --text-muted: #64748b;
            --white: #ffffff;
            --radius: 12px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-main);
            min-height: 100vh;
        }

        .admin-container { 
            display: flex; 
            min-height: 100vh;
            position: relative;
            width: 100%;
        }

        /* --- THE PERFECT FIXED SIDEBAR --- */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--bg-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar.collapsed {
            width: 0;
            min-width: 0;
            overflow: hidden;
            opacity: 0;
            margin-left: 0;
        }

        .sidebar-brand {
            padding: 3rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 25px;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--white);
            letter-spacing: 0.5px;
            flex-shrink: 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background: linear-gradient(135deg, rgba(81, 137, 146, 0.1) 0%, rgba(81, 137, 146, 0.05) 100%);
        }

        .sidebar-brand i { 
            color: var(--primary);
            font-size: 1.8rem;
            filter: drop-shadow(0 2px 4px rgba(81, 137, 146, 0.3));
        }

        /* --- INDEPENDENTLY SCROLLABLE NAV --- */
        .nav-container {
            flex-grow: 1;
            padding: 0 1.5rem 2rem 1.5rem;
        }

        /* Modern Invisible Scrollbar for Sidebar */
        .nav-container::-webkit-scrollbar { width: 6px; }
        .nav-container::-webkit-scrollbar-track { 
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }
        .nav-container::-webkit-scrollbar-thumb { 
            background: rgba(255, 255, 255, 0.1); 
            border-radius: 10px;
            transition: background 0.3s ease;
        }
        .nav-container:hover::-webkit-scrollbar-thumb { 
            background: var(--primary);
        }

        .nav-list { list-style: none; }
        .nav-item { margin-bottom: 0.5rem; }
        
        .nav-link {
            text-decoration: none;
            color: #94a3b8;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: var(--radius);
            transition: all 0.2s ease;
            font-weight: 600;
            font-size: 0.92rem;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: var(--primary);
            transform: scaleY(0);
            transition: transform 0.2s ease;
        }

        .nav-link i { 
            font-size: 1.1rem; 
            width: 20px; 
            text-align: center;
            transition: transform 0.2s ease;
        }

        .nav-link:hover { 
            background: rgba(255, 255, 255, 0.05); 
            color: var(--white);
            padding-left: 20px;
        }

        .nav-link:hover i {
            transform: scale(1.1);
        }

        .nav-link.active {
            background: var(--primary);
            color: var(--white);
            box-shadow: 0 8px 15px -3px rgba(81, 137, 146, 0.3);
        }

        .nav-link.active::before {
            transform: scaleY(1);
        }

        /* --- TOP HEADER & MAIN WRAPPER --- */
        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: 0;
            min-width: 0;
        }

        .top-bar {
            height: 85px;
            background: var(--white);
            padding: 0 2.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
            border-bottom: 1px solid #f1f5f9;
            position: sticky;
            top: 0;
            z-index: 999;
            width: 100%;
        }

        .sidebar-toggle {
            width: 42px;
            height: 42px;
            background: var(--primary);
            border: none;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(81, 137, 146, 0.15);
            flex-shrink: 0;
        }

        .sidebar-toggle:hover {
            background: #3d6b73;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(81, 137, 146, 0.25);
        }

        .sidebar-toggle i {
            color: white;
            font-size: 1.1rem;
            transition: transform 0.3s ease;
        }

        .sidebar-toggle:hover i {
            transform: scale(1.1);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-shrink: 0;
        }

        .header-search {
            background: #f1f5f9;
            padding: 10px 20px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 12px;
            width: 350px;
            max-width: 350px;
            flex-shrink: 0;
        }

        .header-search input { background: transparent; border: none; outline: none; width: 100%; font-size: 0.9rem; }

        .header-actions { 
            display: flex; 
            align-items: center; 
            gap: 20px;
            flex-shrink: 0;
        }

        .profile-pill {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 6px 16px 6px 8px;
            background: #f8fafc;
            border-radius: 50px;
            border: 1px solid #e2e8f0;
            transition: 0.3s;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .admin-avatar {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, var(--primary), #3d6b73);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1rem;
            box-shadow: 0 4px 10px rgba(81, 137, 146, 0.2);
        }

        .admin-meta { 
            line-height: 1.2;
            min-width: max-content;
        }
        .admin-meta b { display: block; font-size: 0.85rem; color: var(--text-main); }
        .admin-meta small { font-size: 0.72rem; color: var(--text-muted); font-weight: 600; }

        .btn-logout {
            background: #fa5d67ff;
            color: var(--danger);
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s ease;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .btn-logout:hover { 
            background: var(--danger); 
            color: var(--red); 
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }

        /* --- CONTENT AREA (SCROLLABLE) --- */
        .dashboard-content {
            padding: 2.5rem;
            flex-grow: 1;
            width: 100%;
            box-sizing: border-box;
        }

        @media (max-width: 1024px) {
            .sidebar { 
                width: 85px;
            }
            .sidebar-brand span, .nav-link span { display: none; }
            .sidebar.collapsed {
                width: 0;
            }
            .header-search {
                width: 250px;
                max-width: 250px;
            }
        }
        
        @media (max-width: 768px) {
            .top-bar {
                padding: 0 1.5rem;
            }
            .header-search {
                width: 200px;
                max-width: 200px;
            }
            .admin-meta small {
                display: none;
            }
            .btn-logout span {
                display: none;
            }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideIn {
            from { transform: translateX(-20px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        .nav-item {
            animation: slideIn 0.3s ease forwards;
        }
        
        .nav-item:nth-child(1) { animation-delay: 0.05s; }
        .nav-item:nth-child(2) { animation-delay: 0.1s; }
        .nav-item:nth-child(3) { animation-delay: 0.15s; }
        .nav-item:nth-child(4) { animation-delay: 0.2s; }
        .nav-item:nth-child(5) { animation-delay: 0.25s; }
    </style>
</head>
<body class="loading">
<!-- ===== FURRYMART PRELOADER ===== -->
<div id="furrymart-preloader">
    <div class="preloader-content">
        <div class="preloader-animation">
            <video autoplay loop muted playsinline>
                <source src="../uploads/Animation/preloader.webm" type="video/webm">
            </video>
        </div>
        <div class="preloader-text">FURRYMART ADMIN</div>
    </div>
</div>
<!-- ===== END PRELOADER ===== -->

<div class="admin-container">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-paw"></i>
            <span>FURRYMART</span>
        </div>
        
        <div class="nav-container">
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                        <i class="fas fa-th-large"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin_users.php" class="nav-link <?php echo ($current_page == 'admin_users.php') ? 'active' : ''; ?>">
                        <i class="fas fa-users-cog"></i> <span>Manage Users</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="manage_vet.php" class="nav-link <?php echo ($current_page == 'manage_vet.php') ? 'active' : ''; ?>">
                        <i class="fas fa-user-doctor"></i> <span>Veterinarians</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="manage_appointments.php" class="nav-link <?php echo ($current_page == 'manage_appointments.php') ? 'active' : ''; ?>">
                        <i class="fas fa-calendar-alt"></i> <span>Vet Appointments</span>
                    </a>
                </li>
                  <li class="nav-item">
                    <a href="admin_grooming.php" class="nav-link <?php echo ($current_page == 'admin_grooming.php') ? 'active' : ''; ?>">
                        <i class="fas fa-shower"></i> <span>Groom Appointments</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="manage_vetService.php" class="nav-link <?php echo ($current_page == 'manage_vetService.php') ? 'active' : ''; ?>">
                        <i class="fas fa-briefcase-medical"></i> <span>Services</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin_manage_tips.php" class="nav-link <?php echo ($current_page == 'admin_manage_tips.php') ? 'active' : ''; ?>">
                        <i class="far fa-newspaper"></i> <span>Insights & Tips</span>
                    </a>
                </li>
                 <li class="nav-item">
                    <a href="admin_categories.php" class="nav-link <?php echo ($current_page == 'admin_categories.php') ? 'active' : ''; ?>">
                        <i class="fas fa-tags"></i> <span>Categories</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin_manage_subscribers.php" class="nav-link <?php echo ($current_page == 'admin_manage_subscribers.php') ? 'active' : ''; ?>">
                        <i class="fas fa-mail-bulk"></i> <span>Subscribers</span>
                    </a>
                </li>
    
                
                <li class="nav-item">
                    <a href="admin_makeovers.php" class="nav-link <?php echo ($current_page == 'admin_makeovers.php') ? 'active' : ''; ?>">
                        <i class="fas fa-shower"></i> <span>MakeOvers Image</span>
                    </a>
                </li>
                 <li class="nav-item">
                    <a href="admin_brands.php" class="nav-link <?php echo ($current_page == 'admin_brands.php') ? 'active' : ''; ?>">
                        <i class="fas fa-rug"></i> <span>Brands</span>
                    </a>
                </li>
                 <li class="nav-item">
                    <a href="admin_pharmacy.php" class="nav-link <?php echo ($current_page == 'admin_pharmacy.php') ? 'active' : ''; ?>">
                        <i class="fas fa-prescription-bottle-alt"></i> <span>Pharmacy</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin_videos.php" class="nav-link <?php echo ($current_page == 'admin_videos.php') ? 'active' : ''; ?>">
                        <i class="fas fa-video"></i> <span>Reels</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin_orders.php" class="nav-link <?php echo ($current_page == 'admin_orders.php') ? 'active' : ''; ?>">
                        <i class="fas fa-shopping-cart"></i> <span>Orders</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="admin_faqs.php" class="nav-link <?php echo ($current_page == 'admin_faqs.php') ? 'active' : ''; ?>">
                        <i class="fas fa-question-circle"></i> <span>FAQs</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin_manage_donations.php" class="nav-link <?php echo ($current_page == 'admin_manage_donations.php') ? 'active' : ''; ?>">
                        <i class="fas fa-donate"></i> <span>Donations</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin_products.php" class="nav-link <?php echo ($current_page == 'admin_products.php') ? 'active' : ''; ?>">
                        <i class="fas fa-box-open"></i> <span>Products</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin_manage_feedback.php" class="nav-link <?php echo ($current_page == 'admin_manage_feedback.php') ? 'active' : ''; ?>">
                        <i class="fas fa-id-card-clip"></i> <span>FeedBacks</span>
                    </a>
                </li>
                  <li class="nav-item">
                    <a href="admin_manage_breeds.php" class="nav-link <?php echo ($current_page == 'admin_manage_breeds.php') ? 'active' : ''; ?>">
                        <i class="fas fa-dog"></i> <span>Breeds</span>
                    </a>
                </li>
                 <li class="nav-item">
                    <a href="admin_queries.php?" class="nav-link <?php echo ($current_page == 'admin_queries.php?') ? 'active' : ''; ?>">
                        <i class="fas fa-images"></i> <span>Contact Queries</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="settings.php" class="nav-link <?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>">
                        <i class="fas fa-sliders-h"></i> <span>Settings</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <div class="main-wrapper">
        <header class="top-bar">
            <div class="header-left">
                <button class="sidebar-toggle" id="sidebarToggle" title="Toggle Sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="header-search" style="position: relative;">
                    <i class="fas fa-search" style="color: var(--text-muted)"></i>
                    <input type="text" id="dashboardSearch" placeholder="Search modules..." autocomplete="off">
                    <div id="searchResults" style="display: none;"></div>
                </div>
            </div>

            <div class="header-actions">
                <div class="profile-pill">
                    <div class="admin-avatar">A</div>
                    <div class="admin-meta">
                        <b>Administrator</b>
                        <small><?php echo $_SESSION['admin_email']; ?></small>
                    </div>
                </div>
                <a href="logout.php" class="btn-logout"><i class="fas fa-power-off"></i> <span>Logout</span></a>
            </div>
        </header>

        <main class="dashboard-content">

        <style>
        #searchResults {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            margin-top: 8px;
            max-height: 400px;
            overflow-y: auto;
            z-index: 1000;
        }
        
        .search-result-item {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .search-result-item:hover {
            background: #f8fafc;
        }
        
        .search-result-item:last-child {
            border-bottom: none;
        }
        
        .search-result-icon {
            width: 36px;
            height: 36px;
            background: var(--primary);
            color: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .search-result-info h4 {
            margin: 0;
            font-size: 0.9rem;
            color: var(--text-main);
        }
        
        .search-result-info p {
            margin: 0;
            font-size: 0.75rem;
            color: var(--text-muted);
        }
        
        .no-results {
            padding: 20px;
            text-align: center;
            color: var(--text-muted);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        </style>

        <script>
        // AJAX Dashboard Search with 10% Threshold
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('dashboardSearch');
            const searchResults = document.getElementById('searchResults');
            let searchTimeout;
            
            if (!searchInput) return;
            
            // Debounced AJAX search
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = this.value.trim();
                
                if (searchTerm === '') {
                    searchResults.style.display = 'none';
                    // Show all cards when search is empty
                    const cards = document.querySelectorAll('.stat-card, .card, [class*="card"]');
                    cards.forEach(card => card.style.display = '');
                    return;
                }
                
                // Debounce for 300ms
                searchTimeout = setTimeout(() => {
                    performSearch(searchTerm);
                }, 300);
            });
            
            function performSearch(query) {
                // Show loading
                searchResults.innerHTML = '<div class="no-results"><i class="fas fa-spinner fa-spin"></i> Searching...</div>';
                searchResults.style.display = 'block';
                
                // AJAX request
                fetch('search_dashboard.php?q=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        displayResults(data);
                        filterDashboardCards(data.results);
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        searchResults.innerHTML = '<div class="no-results">Error performing search</div>';
                    });
            }
            
            function displayResults(data) {
                if (data.results.length === 0) {
                    searchResults.innerHTML = '<div class="no-results"><i class="fas fa-search"></i><br>No modules found</div>';
                    return;
                }
                
                let html = '';
                data.results.forEach(result => {
                    html += `
                        <div class="search-result-item" onclick="window.location.href='${result.url}'">
                            <div class="search-result-icon">
                                <i class="${result.icon}"></i>
                            </div>
                            <div class="search-result-info">
                                <h4>${result.name}</h4>
                                <p>${result.description}</p>
                            </div>
                        </div>
                    `;
                });
                
                searchResults.innerHTML = html;
            }
            
            function filterDashboardCards(results) {
                const cards = document.querySelectorAll('.stat-card, .card, [class*="card"]');
                const matchingNames = results.map(r => r.name.toLowerCase());
                
                cards.forEach(card => {
                    const cardText = card.textContent.toLowerCase();
                    const cardName = card.getAttribute('data-card-name') || '';
                    
                    let shouldShow = false;
                    
                    // Check if card matches any result
                    matchingNames.forEach(name => {
                        if (cardName.toLowerCase().includes(name) || 
                            cardText.includes(name) ||
                            name.includes(cardName.toLowerCase())) {
                            shouldShow = true;
                        }
                    });
                    
                    if (shouldShow) {
                        card.style.display = '';
                        card.style.animation = 'fadeIn 0.3s ease';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }
            
            // Close search results when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.style.display = 'none';
                }
            });
            
            // Show results when focusing on search
            searchInput.addEventListener('focus', function() {
                if (this.value.trim() !== '') {
                    searchResults.style.display = 'block';
                }
            });
        });
        </script>

        <script>
        // Sidebar Toggle Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const toggleIcon = sidebarToggle.querySelector('i');
            
            // Don't auto-collapse on load - always start visible
            // Only check saved preference if explicitly set
            const sidebarState = localStorage.getItem('sidebarCollapsed');
            if (sidebarState === 'true') {
                // Uncomment next line if you want sidebar to stay visible on refresh
                // localStorage.removeItem('sidebarCollapsed');
                sidebar.classList.add('collapsed');
                toggleIcon.classList.remove('fa-bars');
                toggleIcon.classList.add('fa-arrow-right');
            }
            
            // Helper function to reset sidebar (can be called from console)
            window.resetSidebar = function() {
                localStorage.removeItem('sidebarCollapsed');
                sidebar.classList.remove('collapsed');
                toggleIcon.classList.remove('fa-arrow-right');
                toggleIcon.classList.add('fa-bars');
                console.log('Sidebar reset to visible state');
            };
            
            sidebarToggle.addEventListener('click', function(e) {
                e.preventDefault();
                sidebar.classList.toggle('collapsed');
                
                // Change icon based on state
                if (sidebar.classList.contains('collapsed')) {
                    toggleIcon.classList.remove('fa-bars');
                    toggleIcon.classList.add('fa-arrow-right');
                    localStorage.setItem('sidebarCollapsed', 'true');
                } else {
                    toggleIcon.classList.remove('fa-arrow-right');
                    toggleIcon.classList.add('fa-bars');
                    localStorage.setItem('sidebarCollapsed', 'false');
                }
            });
            
            // Extend sidebar height to match content for proper full-page screenshots
            function extendSidebarHeight() {
                if (!sidebar || sidebar.classList.contains('collapsed')) return;
                
                const adminContainer = document.querySelector('.admin-container');
                const mainWrapper = document.querySelector('.main-wrapper');
                
                if (adminContainer && mainWrapper) {
                    const contentHeight = mainWrapper.scrollHeight;
                    const containerHeight = adminContainer.scrollHeight;
                    const maxHeight = Math.max(contentHeight, containerHeight, window.innerHeight);
                    
                    sidebar.style.minHeight = maxHeight + 'px';
                }
            }
            
            // Run on load and when content changes
            setTimeout(extendSidebarHeight, 100);
            window.addEventListener('load', extendSidebarHeight);
            window.addEventListener('resize', extendSidebarHeight);
            
            // Monitor for content changes
            const observer = new MutationObserver(extendSidebarHeight);
            const main = document.querySelector('.dashboard-content');
            if (main) {
                observer.observe(main, { childList: true, subtree: true, attributes: true });
            }
        });
        </script>

        <!-- ===== PRELOADER SCRIPT ===== -->
        <script>
        // Hide preloader when page is fully loaded
        window.addEventListener('load', function() {
            const preloader = document.getElementById('furrymart-preloader');
            const body = document.body;
            
            // Small delay for smooth transition
            setTimeout(function() {
                preloader.classList.add('fade-out');
                body.classList.remove('loading');
                
                // Remove preloader from DOM after animation
                setTimeout(function() {
                    preloader.style.display = 'none';
                }, 500);
            }, 500); // Show preloader for at least 500ms
        });

        // Also hide preloader if it takes too long (fallback)
        setTimeout(function() {
            const preloader = document.getElementById('furrymart-preloader');
            const body = document.body;
            if (preloader && !preloader.classList.contains('fade-out')) {
                preloader.classList.add('fade-out');
                body.classList.remove('loading');
                setTimeout(function() {
                    preloader.style.display = 'none';
                }, 500);
            }
        }, 5000); // Maximum 5 seconds
        </script>

        