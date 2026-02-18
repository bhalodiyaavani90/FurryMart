<?php
// search_dashboard.php - AJAX endpoint for dashboard search
header('Content-Type: application/json');
session_start();

// Security check
if (!isset($_SESSION['admin_email'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

// Define all dashboard modules with their metadata
$modules = [
    [
        'name' => 'Users',
        'keywords' => ['users', 'manage users', 'user management', 'customers', 'members'],
        'url' => 'admin_users.php',
        'icon' => 'fas fa-users-cog',
        'description' => 'Manage Users'
    ],
    [
        'name' => 'Veterinarians',
        'keywords' => ['vet', 'veterinarians', 'doctors', 'vets', 'veterinary'],
        'url' => 'manage_vet.php',
        'icon' => 'fas fa-user-doctor',
        'description' => 'Manage Veterinarians'
    ],
    [
        'name' => 'Appointments',
        'keywords' => ['appointments', 'vet appointments', 'bookings', 'schedule'],
        'url' => 'manage_appointments.php',
        'icon' => 'fas fa-calendar-alt',
        'description' => 'Vet Appointments'
    ],
    [
        'name' => 'Grooming',
        'keywords' => ['grooming', 'groom', 'makeovers', 'pet grooming', 'grooming appointments'],
        'url' => 'admin_grooming.php',
        'icon' => 'fas fa-shower',
        'description' => 'Groom Appointments'
    ],
    [
        'name' => 'Services',
        'keywords' => ['services', 'vet services', 'veterinary services'],
        'url' => 'manage_vetService.php',
        'icon' => 'fas fa-briefcase-medical',
        'description' => 'Manage Services'
    ],
    [
        'name' => 'Tips',
        'keywords' => ['tips', 'insights', 'articles', 'blog', 'news'],
        'url' => 'admin_manage_tips.php',
        'icon' => 'far fa-newspaper',
        'description' => 'Insights & Tips'
    ],
    [
        'name' => 'Categories',
        'keywords' => ['categories', 'category', 'product categories'],
        'url' => 'admin_categories.php',
        'icon' => 'fas fa-tags',
        'description' => 'Manage Categories'
    ],
    [
        'name' => 'Subscribers',
        'keywords' => ['subscribers', 'newsletter', 'subscriptions', 'mailing list'],
        'url' => 'admin_manage_subscribers.php',
        'icon' => 'fas fa-mail-bulk',
        'description' => 'Manage Subscribers'
    ],
    [
        'name' => 'MakeOvers',
        'keywords' => ['makeovers', 'makeover images', 'grooming images', 'gallery'],
        'url' => 'admin_makeovers.php',
        'icon' => 'fas fa-shower',
        'description' => 'MakeOvers Image'
    ],
    [
        'name' => 'Brands',
        'keywords' => ['brands', 'brand', 'manufacturers'],
        'url' => 'admin_brands.php',
        'icon' => 'fas fa-rug',
        'description' => 'Manage Brands'
    ],
    [
        'name' => 'Pharmacy',
        'keywords' => ['pharmacy', 'medicines', 'drugs', 'medication'],
        'url' => 'admin_pharmacy.php',
        'icon' => 'fas fa-prescription-bottle-alt',
        'description' => 'Pharmacy Management'
    ],
    [
        'name' => 'Videos',
        'keywords' => ['videos', 'reels', 'media', 'video content'],
        'url' => 'admin_videos.php',
        'icon' => 'fas fa-video',
        'description' => 'Manage Reels'
    ],
    [
        'name' => 'Orders',
        'keywords' => ['orders', 'purchases', 'sales', 'transactions'],
        'url' => 'admin_orders.php',
        'icon' => 'fas fa-shopping-cart',
        'description' => 'Manage Orders'
    ],
    [
        'name' => 'FAQs',
        'keywords' => ['faqs', 'questions', 'help', 'faq'],
        'url' => 'admin_faqs.php',
        'icon' => 'fas fa-question-circle',
        'description' => 'Manage FAQs'
    ],
    [
        'name' => 'Donations',
        'keywords' => ['donations', 'donate', 'charity', 'funding'],
        'url' => 'admin_manage_donations.php',
        'icon' => 'fas fa-donate',
        'description' => 'Manage Donations'
    ],
    [
        'name' => 'Products',
        'keywords' => ['products', 'items', 'merchandise', 'shop'],
        'url' => 'admin_products.php',
        'icon' => 'fas fa-box-open',
        'description' => 'Manage Products'
    ],
    [
        'name' => 'Feedbacks',
        'keywords' => ['feedbacks', 'feedback', 'reviews', 'comments'],
        'url' => 'admin_manage_feedback.php',
        'icon' => 'fas fa-id-card-clip',
        'description' => 'Manage FeedBacks'
    ],
    [
        'name' => 'Breeds',
        'keywords' => ['breeds', 'pet breeds', 'dog breeds', 'cat breeds'],
        'url' => 'admin_manage_breeds.php',
        'icon' => 'fas fa-dog',
        'description' => 'Manage Breeds'
    ],
    [
        'name' => 'Queries',
        'keywords' => ['queries', 'contact', 'messages', 'inquiries', 'contact queries'],
        'url' => 'admin_queries.php',
        'icon' => 'fas fa-images',
        'description' => 'Contact Queries'
    ],
    [
        'name' => 'Settings',
        'keywords' => ['settings', 'configuration', 'preferences', 'setup'],
        'url' => 'settings.php',
        'icon' => 'fas fa-sliders-h',
        'description' => 'Settings'
    ],
    [
        'name' => 'Dashboard',
        'keywords' => ['dashboard', 'home', 'overview', 'statistics'],
        'url' => 'dashboard.php',
        'icon' => 'fas fa-th-large',
        'description' => 'Dashboard'
    ]
];

// If search term is empty, return all modules
if (empty($searchTerm)) {
    echo json_encode(['results' => $modules, 'count' => count($modules)]);
    exit;
}

// Search and score modules
$results = [];
$searchLower = strtolower($searchTerm);

foreach ($modules as $module) {
    $score = 0;
    
    // Check name match (highest priority)
    if (stripos($module['name'], $searchTerm) !== false) {
        $score += 100;
    }
    
    // Check description match
    if (stripos($module['description'], $searchTerm) !== false) {
        $score += 80;
    }
    
    // Check keywords
    foreach ($module['keywords'] as $keyword) {
        if (stripos($keyword, $searchTerm) !== false) {
            $score += 60;
            break;
        }
    }
    
    // Fuzzy matching for partial words
    $searchWords = explode(' ', $searchLower);
    foreach ($searchWords as $word) {
        if (strlen($word) >= 2) {
            foreach ($module['keywords'] as $keyword) {
                if (strpos($keyword, $word) !== false || strpos($word, $keyword) !== false) {
                    $score += 20;
                }
            }
        }
    }
    
    // Threshold of 10 (10% of 100)
    if ($score >= 10) {
        $module['score'] = $score;
        $results[] = $module;
    }
}

// Sort by score (highest first)
usort($results, function($a, $b) {
    return $b['score'] - $a['score'];
});

echo json_encode([
    'results' => $results,
    'count' => count($results),
    'searchTerm' => $searchTerm
]);
