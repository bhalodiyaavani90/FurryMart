<?php
include "../db.php";
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Handle status update
if(isset($_POST['update_status'])) {
    $booking_id = (int)$_POST['booking_id'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $admin_notes = mysqli_real_escape_string($conn, $_POST['admin_notes']);
    
    $update_query = "UPDATE birthday_bookings 
                     SET status = '$status', admin_notes = '$admin_notes', updated_at = NOW() 
                     WHERE id = $booking_id";
    
    if(mysqli_query($conn, $update_query)) {
        $_SESSION['alert'] = ['type' => 'success', 'msg' => 'Booking status updated successfully!'];
    } else {
        $_SESSION['alert'] = ['type' => 'error', 'msg' => 'Failed to update status'];
    }
    
    header("Location: admin_birthdays.php");
    exit();
}

// Handle delete
if(isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM birthday_bookings WHERE id = $id");
    $_SESSION['alert'] = ['type' => 'success', 'msg' => 'Booking deleted successfully'];
    header("Location: admin_birthdays.php");
    exit();
}

// Fetch all bookings
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$sql = "SELECT b.*, u.first_name, u.last_name 
        FROM birthday_bookings b 
        LEFT JOIN users u ON b.user_email = u.email";

if($filter != 'all') {
    $sql .= " WHERE b.status = '$filter'";
}

$sql .= " ORDER BY b.party_date ASC, b.booking_date DESC";
$all_bookings = mysqli_query($conn, $sql);

// Get counts for each status
$pending_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM birthday_bookings WHERE status = 'pending'");
$pending_count = $pending_result ? mysqli_fetch_assoc($pending_result)['count'] : 0;

$approved_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM birthday_bookings WHERE status = 'approved'");
$approved_count = $approved_result ? mysqli_fetch_assoc($approved_result)['count'] : 0;

$rejected_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM birthday_bookings WHERE status = 'rejected'");
$rejected_count = $rejected_result ? mysqli_fetch_assoc($rejected_result)['count'] : 0;
$total_count = mysqli_num_rows($all_bookings);
?>

<?php include "includes/header.php"; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root { --pink: #ec4899; --purple: #8b5cf6; --navy: #0f1c3f; --teal: #518992; --bg: #f4f7f6; }
    body { background: var(--bg); font-family: 'Plus Jakarta Sans', sans-serif; }
    
    .admin-viewport { 
        margin-left: 20px; 
        padding: 60px 40px; 
        display: flex; 
        flex-direction: column; 
        gap: 40px; 
    }
    
    .page-header {
        background: linear-gradient(135deg, var(--pink), var(--purple));
        padding: 50px;
        border-radius: 35px;
        color: white;
        text-align: center;
        box-shadow: 0 20px 60px rgba(139, 92, 246, 0.3);
    }
    
    .page-header h1 {
        font-size: 42px;
        font-weight: 900;
        margin-bottom: 10px;
    }
    
    .page-header p {
        font-size: 18px;
        opacity: 0.9;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }
    
    .stat-card {
        background: white;
        padding: 30px;
        border-radius: 25px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        gap: 20px;
        transition: 0.3s;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
    }
    
    .stat-icon {
        width: 70px;
        height: 70px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
    }
    
    .stat-icon.pending { background: linear-gradient(135deg, #fbbf24, #f59e0b); }
    .stat-icon.approved { background: linear-gradient(135deg, #10b981, #059669); }
    .stat-icon.rejected { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .stat-icon.total { background: linear-gradient(135deg, var(--pink), var(--purple)); }
    
    .stat-content h3 {
        font-size: 36px;
        font-weight: 900;
        color: var(--navy);
        margin: 0 0 5px 0;
    }
    
    .stat-content p {
        font-size: 14px;
        color: #64748b;
        font-weight: 600;
        margin: 0;
    }
    
    .filter-tabs {
        display: flex;
        gap: 15px;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }
    
    .filter-tab {
        padding: 12px 25px;
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 15px;
        text-decoration: none;
        color: #64748b;
        font-weight: 700;
        font-size: 14px;
        transition: 0.3s;
    }
    
    .filter-tab:hover {
        border-color: var(--purple);
        color: var(--purple);
    }
    
    .filter-tab.active {
        background: linear-gradient(135deg, var(--pink), var(--purple));
        color: white;
        border-color: transparent;
    }
    
    .bookings-container {
        background: white;
        border-radius: 35px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.05);
    }
    
    .table-wrapper {
        overflow-x: auto;
    }
    
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 12px;
    }
    
    thead tr {
        text-align: left;
        font-size: 12px;
        color: #94a3b8;
        text-transform: uppercase;
        font-weight: 800;
    }
    
    tbody tr {
        background: #f8fafc;
        transition: 0.3s;
    }
    
    tbody tr:hover {
        background: #e0e7ff;
        transform: scale(1.01);
    }
    
    td {
        padding: 20px 15px;
    }
    
    td:first-child {
        border-radius: 15px 0 0 15px;
    }
    
    td:last-child {
        border-radius: 0 15px 15px 0;
    }
    
    .status-badge {
        display: inline-block;
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
    }
    
    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }
    
    .status-approved {
        background: #d1fae5;
        color: #065f46;
    }
    
    .status-rejected {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .action-btn {
        padding: 8px 15px;
        border-radius: 10px;
        border: none;
        font-weight: 700;
        font-size: 12px;
        cursor: pointer;
        transition: 0.3s;
        margin-right: 8px;
    }
    
    .btn-view {
        background: #dbeafe;
        color: #1e40af;
    }
    
    .btn-view:hover {
        background: #3b82f6;
        color: white;
    }
    
    .btn-delete {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .btn-delete:hover {
        background: #ef4444;
        color: white;
    }
    
    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 28, 63, 0.5);
        backdrop-filter: blur(5px);
        display: none;
        z-index: 1000;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }
    
    .modal-content {
        background: white;
        border-radius: 35px;
        padding: 50px;
        width: 100%;
        max-width: 800px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 30px 80px rgba(0,0,0,0.3);
        position: relative;
    }
    
    .modal-close {
        position: absolute;
        top: 25px;
        right: 30px;
        font-size: 30px;
        cursor: pointer;
        color: #94a3b8;
        transition: 0.3s;
    }
    
    .modal-close:hover {
        color: var(--pink);
        transform: rotate(90deg);
    }
    
    .modal-header {
        text-align: center;
        margin-bottom: 35px;
    }
    
    .modal-title {
        font-size: 32px;
        font-weight: 900;
        background: linear-gradient(135deg, var(--pink), var(--purple));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 10px;
    }
    
    .booking-details {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
        margin-bottom: 30px;
    }
    
    .detail-item {
        background: #f8fafc;
        padding: 20px;
        border-radius: 15px;
    }
    
    .detail-label {
        font-size: 12px;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        margin-bottom: 8px;
    }
    
    .detail-value {
        font-size: 16px;
        font-weight: 700;
        color: var(--navy);
    }
    
    .form-group {
        margin-bottom: 25px;
    }
    
    .form-label {
        font-size: 14px;
        font-weight: 800;
        color: var(--navy);
        margin-bottom: 10px;
        display: block;
    }
    
    .form-select, .form-textarea {
        width: 100%;
        padding: 15px;
        border: 2px solid #e2e8f0;
        border-radius: 15px;
        font-size: 15px;
        font-weight: 600;
        font-family: inherit;
    }
    
    .form-textarea {
        min-height: 100px;
        resize: vertical;
    }
    
    .submit-btn {
        width: 100%;
        padding: 18px;
        background: linear-gradient(135deg, var(--pink), var(--purple));
        color: white;
        border: none;
        border-radius: 15px;
        font-size: 16px;
        font-weight: 800;
        cursor: pointer;
        transition: 0.3s;
    }
    
    .submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(139, 92, 246, 0.4);
    }
</style>

<div class="admin-viewport">
    <!-- Page Header -->
    <div class="page-header animate__animated animate__fadeInDown">
        <h1>🎂 Birthday Party Bookings</h1>
        <p>Manage and approve pet birthday party reservations</p>
    </div>
    
    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card animate__animated animate__fadeInUp">
            <div class="stat-icon pending">⏳</div>
            <div class="stat-content">
                <h3><?php echo $pending_count; ?></h3>
                <p>Pending Approval</p>
            </div>
        </div>
        
        <div class="stat-card animate__animated animate__fadeInUp animate__delay-1s">
            <div class="stat-icon approved">✅</div>
            <div class="stat-content">
                <h3><?php echo $approved_count; ?></h3>
                <p>Approved Parties</p>
            </div>
        </div>
        
        <div class="stat-card animate__animated animate__fadeInUp animate__delay-1s">
            <div class="stat-icon rejected">❌</div>
            <div class="stat-content">
                <h3><?php echo $rejected_count; ?></h3>
                <p>Rejected</p>
            </div>
        </div>
        
        <div class="stat-card animate__animated animate__fadeInUp animate__delay-2s">
            <div class="stat-icon total">🎉</div>
            <div class="stat-content">
                <h3><?php echo $total_count; ?></h3>
                <p>Total Bookings</p>
            </div>
        </div>
    </div>
    
    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <a href="?filter=all" class="filter-tab <?php echo $filter == 'all' ? 'active' : ''; ?>">
            <i class="fas fa-list"></i> All Bookings
        </a>
        <a href="?filter=pending" class="filter-tab <?php echo $filter == 'pending' ? 'active' : ''; ?>">
            <i class="fas fa-clock"></i> Pending
        </a>
        <a href="?filter=approved" class="filter-tab <?php echo $filter == 'approved' ? 'active' : ''; ?>">
            <i class="fas fa-check-circle"></i> Approved
        </a>
        <a href="?filter=rejected" class="filter-tab <?php echo $filter == 'rejected' ? 'active' : ''; ?>">
            <i class="fas fa-times-circle"></i> Rejected
        </a>
    </div>
    
    <!-- Bookings Table -->
    <div class="bookings-container animate__animated animate__fadeInUp">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Pet Details</th>
                        <th>Party Date</th>
                        <th>Time</th>
                        <th>Guests</th>
                        <th>Package</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php mysqli_data_seek($all_bookings, 0); while($booking = mysqli_fetch_assoc($all_bookings)): ?>
                    <tr>
                        <td><strong>#<?php echo $booking['id']; ?></strong></td>
                        <td>
                            <div style="font-weight: 700; color: var(--navy);">
                                <?php echo $booking['first_name'] . ' ' . $booking['last_name']; ?>
                            </div>
                            <div style="font-size: 11px; color: #64748b;">
                                <?php echo $booking['user_email']; ?>
                            </div>
                        </td>
                        <td>
                            <strong><?php echo $booking['pet_name']; ?></strong>
                            <div style="font-size: 11px; color: #64748b;">Type: <?php echo $booking['pet_type']; ?></div>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($booking['party_date'])); ?></td>
                        <td><?php echo $booking['party_time']; ?></td>
                        <td><?php echo $booking['guest_count']; ?> pets</td>
                        <td>
                            <?php if($booking['party_package']): ?>
                                <span style="color: var(--pink); font-weight: 600;"><?php echo $booking['party_package']; ?></span>
                            <?php else: ?>
                                <span style="color: #94a3b8;">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo $booking['status']; ?>">
                                <?php echo ucfirst($booking['status']); ?>
                            </span>
                        </td>
                        <td>
                            <button class="action-btn btn-view" onclick='openModal(<?php echo json_encode($booking); ?>)'>
                                <i class="fas fa-eye"></i> View
                            </button>
                            <a href="?delete=<?php echo $booking['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Delete this booking?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="bookingModal" class="modal-overlay">
    <div class="modal-content animate__animated animate__zoomIn">
        <span class="modal-close" onclick="closeModal()">&times;</span>
        
        <div class="modal-header">
            <h2 class="modal-title">🎉 Booking Details</h2>
        </div>
        
        <form method="POST">
            <input type="hidden" name="booking_id" id="modal_booking_id">
            
            <div class="booking-details">
                <div class="detail-item">
                    <div class="detail-label">Pet Name</div>
                    <div class="detail-value" id="modal_pet_name"></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Pet Type</div>
                    <div class="detail-value" id="modal_pet_type"></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Customer</div>
                    <div class="detail-value" id="modal_customer"></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Email</div>
                    <div class="detail-value" id="modal_email"></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Party Date</div>
                    <div class="detail-value" id="modal_date"></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Party Time</div>
                    <div class="detail-value" id="modal_time"></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Number of Pets</div>
                    <div class="detail-value" id="modal_guests"></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label"><i class="fas fa-gift"></i> Party Package</div>
                    <div class="detail-value" id="modal_package" style="color: var(--pink); font-weight: 600;"></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Contact Phone</div>
                    <div class="detail-value" id="modal_phone"></div>
                </div>
            </div>
            
            <div class="detail-item" style="margin-bottom: 30px;">
                <div class="detail-label">Special Requests</div>
                <div class="detail-value" id="modal_requests"></div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Booking Status</label>
                <select name="status" class="form-select" required>
                    <option value="pending">⏳ Pending</option>
                    <option value="approved">✅ Approved</option>
                    <option value="rejected">❌ Rejected</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Admin Notes</label>
                <textarea name="admin_notes" class="form-textarea" placeholder="Add any notes for this booking..."></textarea>
            </div>
            
            <button type="submit" name="update_status" class="submit-btn">
                <i class="fas fa-save"></i> Update Booking Status
            </button>
        </form>
    </div>
</div>

<script>
function openModal(booking) {
    document.getElementById('modal_booking_id').value = booking.id;
    document.getElementById('modal_pet_name').textContent = booking.pet_name;
    document.getElementById('modal_pet_type').textContent = booking.pet_type;
    document.getElementById('modal_customer').textContent = booking.user_name || (booking.first_name + ' ' + booking.last_name);
    document.getElementById('modal_email').textContent = booking.user_email;
    document.getElementById('modal_date').textContent = new Date(booking.party_date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
    document.getElementById('modal_time').textContent = booking.party_time;
    document.getElementById('modal_guests').textContent = booking.guest_count + ' pets';
    document.getElementById('modal_package').textContent = booking.party_package || 'Not specified';
    document.getElementById('modal_phone').textContent = booking.contact_phone;
    document.getElementById('modal_requests').textContent = booking.special_requests || 'No special requests';
    
    document.querySelector('select[name="status"]').value = booking.status;
    document.querySelector('textarea[name="admin_notes"]').value = booking.admin_notes || '';
    
    document.getElementById('bookingModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('bookingModal').style.display = 'none';
}

// Close modal on outside click
window.addEventListener('click', function(e) {
    if(e.target === document.getElementById('bookingModal')) {
        closeModal();
    }
});

<?php if(isset($_SESSION['alert'])): ?>
    Swal.fire({
        icon: '<?php echo $_SESSION['alert']['type']; ?>',
        title: '<?php echo $_SESSION['alert']['msg']; ?>',
        showConfirmButton: false,
        timer: 2000
    });
<?php unset($_SESSION['alert']); endif; ?>
</script>

<?php include "includes/footer.php"; ?>
