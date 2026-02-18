<?php
/**
 * FURRYMART ADMIN COMMAND CENTER - FULL SOVEREIGN EDITION
 * Features: 10-Metric High-Density Grid + Real-time Analytics
 */
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once('../db.php');

// Redirect to login if not authenticated
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

/** 1. DYNAMIC DATA AGGREGATION **/
function getTableCount($conn, $table) {
    $res = mysqli_query($conn, "SELECT COUNT(*) as total FROM $table");
    return ($res) ? mysqli_fetch_assoc($res)['total'] : 0;
}

// Full Enterprise Stats aggregation
// Full Enterprise Stats aggregation
$stats = [
    'users'         => getTableCount($conn, "users"),
    'vets'          => getTableCount($conn, "veterinarians"),
    'appointments'  => getTableCount($conn, "vet_appointments"),
    'grooming'      => getTableCount($conn, "appoint_grooming"), // New Table
    'brands'        => getTableCount($conn, "brands"),           // New Table
    'vet_services'  => getTableCount($conn, "vet_services"),
    'tips'          => getTableCount($conn, "expert_tips"),
    'subs'          => getTableCount($conn, "newsletter_subscribers"),
    'faqs'          => getTableCount($conn, "faqs"),
    'products'          => getTableCount($conn, "products"),
    'orders'            => getTableCount($conn, "orders"),
    'categories'        => getTableCount($conn, "main_categories"),
    'subcategories'     => getTableCount($conn, "sub_categories"),
    'reels'             => getTableCount($conn, "pet_moods"),
    'breeds'        => getTableCount($conn, "breed_picks"),
    'queries'       => getTableCount($conn, "contact_queries"),
    'feedbacks'     => getTableCount($conn, "customer_feedbacks"),
    'makeovers'     => getTableCount($conn, "pawsome_makeovers"),
    'banners'       => getTableCount($conn, "hero_banners"),
    'donations'     => getTableCount($conn, "foundation_donations"),
    'pharmacy_products' => getTableCount($conn, "pharmacy_products") // New Table
];

// Fetch Recent Activity for UI depth
$recentApts = mysqli_query($conn, "SELECT pet_parent_name, pet_name, appointment_date, status FROM vet_appointments ORDER BY id DESC LIMIT 5");
$recentTips = mysqli_query($conn, "SELECT title, category, publish_date FROM expert_tips ORDER BY id DESC LIMIT 4");
$recentQueries = mysqli_query($conn, "SELECT first_name, subject, status, created_at FROM contact_queries ORDER BY id DESC LIMIT 5");
include('includes/header.php'); 
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    :root {
        --primary: #518992;
        --navy: #0f1c3f;
        --bg: #f4f7fa;
        --white: #ffffff;
    }

    body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); margin: 0; }
    .dashboard-container { padding: 40px 4%; }

    /* Welcome Hub */
    .welcome-section {
        background: linear-gradient(135deg, var(--navy) 0%, #1a3a5f 100%);
        border-radius: 30px;
        padding: 40px;
        color: white;
        margin-bottom: 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 20px 40px rgba(15, 28, 63, 0.15);
    }
    .welcome-text h1 { font-size: 2.2rem; font-weight: 800; margin: 0; }
    .welcome-text p { opacity: 0.8; margin-top: 10px; font-size: 1.1rem; }

    /* High-Density Stats Architecture */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 50px;
    }

    .stat-card {
        background: var(--white);
        border-radius: 25px;
        padding: 25px;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 1px solid #eef2f6;
        text-decoration: none;
        color: inherit;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-10px);
        border-color: var(--primary);
        box-shadow: 0 20px 40px rgba(81, 137, 146, 0.1);
    }

    .icon-circle {
        width: 55px;
        height: 55px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }

    /* Clinical Color palette */
    .c-blue { background: #eff6ff; color: #3b82f6; }
    .c-teal { background: #f0fdfa; color: #518992; }
    .c-pink { background: #fdf2f8; color: #db2777; }
    .c-gold { background: #fffbeb; color: #d97706; }
    .c-purple { background: #f5f3ff; color: #7c3aed; }
    .c-indigo { background: #eef2ff; color: #6366f1; }
    .c-emerald { background: #ecfdf5; color: #10b981; }
    .c-rose { background: #fff1f2; color: #f43f5e; }
    .c-orange { background: #fff7ed; color: #f59e0b; }
    .c-cyan { background: #ecfeff; color: #0891b2; }
    .c-violet { background: #f5f3ff; color: #8b5cf6; }
    .c-emerald-dark { background: #d1fae5; color: #065f46; }

    .stat-data h3 { font-size: 1.8rem; font-weight: 800; margin: 0; color: var(--navy); }
    .stat-data p { font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase; margin: 2px 0 0; letter-spacing: 1px; }

    .bottom-grid { display: grid; grid-template-columns: 1.6fr 1fr; gap: 30px; }
    .glass-panel { background: var(--white); border-radius: 30px; padding: 35px; border: 1px solid #eef2f6; box-shadow: 0 15px 35px rgba(0,0,0,0.03); }
    .panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
    .panel-header h2 { font-size: 1.3rem; font-weight: 800; color: var(--navy); margin: 0; }

    .custom-table { width: 100%; border-collapse: collapse; }
    .custom-table th { text-align: left; padding: 15px; color: #64748b; font-size: 0.8rem; text-transform: uppercase; border-bottom: 2px solid #f8fafc; }
    .custom-table td { padding: 18px 15px; border-bottom: 1px solid #f8fafc; font-size: 0.95rem; }

    .status-badge { padding: 6px 14px; border-radius: 50px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; }
    .pending { background: #fff7ed; color: #c2410c; }
    .confirmed { background: #f0fdf4; color: #166534; }

    .tip-item { display: flex; align-items: center; gap: 15px; padding: 15px 0; border-bottom: 1px solid #f8fafc; }
    .tip-icon { width: 45px; height: 45px; background: #f1f5f9; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--primary); }
    .tip-info b { display: block; font-size: 0.9rem; color: var(--navy); }
</style>

<main class="dashboard-container">
    
    <div class="welcome-section">
        <div class="welcome-text">
            <h1>FurryMart Command Center</h1>
            <p>Managing clinical data across <?php echo array_sum($stats); ?> integrated records.</p>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 1.5rem; font-weight: 800;" id="liveClock"></div>
            <div style="opacity: 0.7;"><?php echo date('l, d F Y'); ?></div>
        </div>
    </div>

    <div class="stats-grid">
        <a href="admin_users.php" class="stat-card">
            <div class="icon-circle c-blue"><i class="fas fa-users"></i></div>
            <div class="stat-data"><h3><?php echo number_format($stats['users']); ?></h3><p>Users</p></div>
        </a>

        <a href="manage_vet.php" class="stat-card">
            <div class="icon-circle c-teal"><i class="fas fa-user-md"></i></div>
            <div class="stat-data"><h3><?php echo number_format($stats['vets']); ?></h3><p>Veterinarians</p></div>
        </a>

        <a href="manage_appointments.php" class="stat-card">
            <div class="icon-circle c-pink"><i class="fas fa-calendar-check"></i></div>
            <div class="stat-data"><h3><?php echo number_format($stats['appointments']); ?></h3><p>Vet Appoint</p></div>
        </a>
        <a href="admin_manage_donations.php" class="stat-card">
            <div class="icon-circle c-yellow"><i class="fas fa-donate"></i></div>
            <div class="stat-data"><h3><?php echo number_format($stats['donations']); ?></h3><p>Donations</p></div>
        </a>
        <a href="admin_grooming.php" class="stat-card">
        <div class="icon-circle c-cyan"><i class="fas fa-cut"></i></div>
        <div class="stat-data"> <h3><?php echo number_format($stats['grooming']); ?></h3> <p>Groom Appoint</p></div>
         </a>

         <a href="manage_vetService.php" class="stat-card">
        <div class="icon-circle c-emerald-dark"><i class="fas fa-hand-holding-medical"></i></div>
        <div class="stat-data"><h3><?php echo number_format($stats['vet_services']); ?></h3><p>Medical Services</p></div>
        </a>

        <a href="admin_manage_breeds.php" class="stat-card">
            <div class="icon-circle c-gold"><i class="fas fa-dog"></i></div>
            <div class="stat-data"><h3><?php echo number_format($stats['breeds']); ?></h3><p>Breed</p></div>
        </a>
        <a href="admin_queries.php" class="stat-card">
    <div class="icon-circle" style="background: #fdf2f8; color: #be185d;"><i class="fas fa-envelope-open-text"></i></div>
    <div class="stat-data"><h3><?php echo number_format($stats['queries']); ?></h3> <p>Contact Queries</p></div>
    </a>

        <a href="admin_manage_feedback.php" class="stat-card">
            <div class="icon-circle c-rose"><i class="fas fa-star"></i></div>
            <div class="stat-data"><h3><?php echo number_format($stats['feedbacks']); ?></h3><p>FeedBacks</p></div>
        </a>
        <a href="admin_orders.php" class="stat-card">
            <div class="icon-circle c-orders"><i class="fas fa-shopping-bag"></i></div>
            <div class="stat-data"><h3><?php echo number_format($stats['orders']); ?></h3><p>Orders</p></div>
        </a>
        <a href="admin_products.php" class="stat-card">
            <div class="icon-circle c-commerce"><i class="fas fa-box-open"></i></div>
            <div class="stat-data"><h3><?php echo number_format($stats['products']); ?></h3><p>Products</p></div>
        </a>
        <a href="admin_categories.php" class="stat-card">
            <div class="icon-circle c-inventory"><i class="fas fa-layer-group"></i></div>
            <div class="stat-data"><h3><?php echo number_format($stats['categories']); ?></h3><p>Categories</p></div>
        </a>
        <a href="admin_categories.php" class="stat-card">
            <div class="icon-circle c-navy-light"><i class="fas fa-indent"></i></div>
            <div class="stat-data"><h3><?php echo number_format($stats['subcategories']); ?></h3><p>Sub-Cats</p></div>
        </a>
        <a href="admin_videos.php" class="stat-card">
            <div class="icon-circle c-reels"><i class="fas fa-film"></i></div>
            <div class="stat-data"><h3><?php echo number_format($stats['reels']); ?></h3><p>Mood Reels</p></div>
        </a>
        <a href="admin_makeovers.php" class="stat-card">
            <div class="icon-circle c-emerald"><i class="fas fa-magic"></i></div>
            <div class="stat-data"><h3><?php echo number_format($stats['makeovers']); ?></h3><p>Makeovers</p></div>
        </a>

        <a href="admin_manage_banners.php" class="stat-card">
            <div class="icon-circle c-indigo"><i class="fas fa-image"></i></div>
            <div class="stat-data"><h3><?php echo number_format($stats['banners']); ?></h3><p>Banners</p></div>
        </a>
        <a href="admin_pharmacy.php" class="stat-card">
            <div class="icon-circle c-purple"><i class="fas fa-prescription-bottle-alt"></i></div>
            <div class="stat-data"><h3><?php echo number_format($stats['pharmacy_products']); ?></h3><p>Pharmacy Products</p></div>
        </a>

        <a href="admin_manage_tips.php" class="stat-card">
            <div class="icon-circle c-gold"><i class="far fa-lightbulb"></i></div>
            <div class="stat-data"><h3><?php echo number_format($stats['tips']); ?></h3><p>Health Insights</p></div>
        </a>
        <a href="admin_brands.php" class="stat-card">
        <div class="icon-circle c-violet"><i class="fas fa-tags"></i></div>
        <div class="stat-data"><h3><?php echo number_format($stats['brands']); ?></h3><p>Brands</p> </div>
        </a>

        <a href="admin_faqs.php" class="stat-card">
            <div class="icon-circle c-orange"><i class="fas fa-question-circle"></i></div>
            <div class="stat-data"><h3><?php echo number_format($stats['faqs']); ?></h3><p>FAQs</p></div>
        </a>

        <a href="admin_manage_subscribers.php" class="stat-card">
            <div class="icon-circle c-blue"><i class="fas fa-mail-bulk"></i></div>
            <div class="stat-data"><h3><?php echo number_format($stats['subs']); ?></h3><p>Subscribers</p></div>
        </a>
    </div>

    <div class="bottom-grid">
        <div class="glass-panel">
            <div class="panel-header">
                <h2>Pending Appointments</h2>
                <a href="manage_appointments.php" style="color: var(--primary); font-weight: 700; text-decoration: none; font-size: 0.85rem;">VIEW ALL</a>
            </div>
            <table class="custom-table">
                <thead><tr><th>Parent & Pet</th><th>Schedule</th><th>Status</th></tr></thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($recentApts)): ?>
                    <tr>
                        <td><div style="font-weight: 700;"><?php echo $row['pet_parent_name']; ?></div><small>Pet: <?php echo $row['pet_name']; ?></small></td>
                        <td><?php echo date('M d, Y', strtotime($row['appointment_date'])); ?></td>
                        <td><span class="status-badge <?php echo ($row['status'] == 'Confirmed') ? 'confirmed' : 'pending'; ?>"><?php echo $row['status']; ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="glass-panel">
            <div class="panel-header"><h2>Recent Tips</h2><i class="fas fa-pen-nib"></i></div>
            <div class="tips-list">
                <?php while($tip = mysqli_fetch_assoc($recentTips)): ?>
                <div class="tip-item">
                    <div class="tip-icon"><i class="far fa-file-alt"></i></div>
                    <div class="tip-info"><b><?php echo $tip['title']; ?></b><small><?php echo $tip['category']; ?></small></div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    <div class="glass-panel">
    <div class="panel-header">
        <h2>Recent Inquiries</h2>
        <a href="admin_queries.php" style="color: var(--primary); font-weight: 700; text-decoration: none; font-size: 0.85rem;">VIEW ALL</a>
    </div>
    <table class="custom-table">
        <thead><tr><th>User & Subject</th><th>Date</th><th>Status</th></tr></thead>
        <tbody>
            <?php while($q = mysqli_fetch_assoc($recentQueries)): ?>
            <tr>
                <td><div style="font-weight: 700;"><?php echo $q['first_name']; ?></div><small><?php echo $q['subject']; ?></small></td>
                <td><?php echo date('M d', strtotime($q['created_at'])); ?></td>
                <td><span class="status-badge <?php echo $q['status']; ?>"><?php echo $q['status']; ?></span></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</main>

<script>
    function updateClock() {
        const now = new Date();
        document.getElementById('liveClock').innerText = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', second:'2-digit'});
    }
    setInterval(updateClock, 1000); updateClock();
</script>

<?php include('includes/footer.php'); ?>