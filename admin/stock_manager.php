<?php
include "../db.php";
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// --- 1. CORE LOGIC: INSTANT REFILL PROTOCOL ---
if(isset($_POST['sync_stock'])){
    $v_id = $_POST['variant_id'];
    $new_qty = (int)$_POST['quantity'];
    
    // Direct database synchronization for specific variant
    $update_q = "UPDATE product_variants SET stock_qty = '$new_qty' WHERE id = '$v_id'";
    if(mysqli_query($conn, $update_q)){
        $_SESSION['alert'] = ['type' => 'success', 'msg' => 'Stock Intelligence Synchronized!'];
        header("Location: stock_manager.php"); exit();
    }
}

// --- 2. DATA CAPTURE: ISOLATE INVENTORY HEALTH ---
// Joins products and variants to show specific identity for each size
$inventory_res = mysqli_query($conn, "SELECT v.*, p.name, p.brand_name, p.base_image, p.life_stage 
                                      FROM product_variants v 
                                      JOIN products p ON v.product_id = p.id 
                                      ORDER BY v.stock_qty ASC");
?>

<?php include "includes/header.php"; ?> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root { --teal: #518992; --navy: #0f1c3f; --pink: #f87171; --bg: #f4f7f6; --amber: #f59e0b; }
    body { background: var(--bg); font-family: 'Plus Jakarta Sans', sans-serif; }
    
    /* Centered Dashboard Architecture */
    .admin-main { margin-left: 280px; padding: 60px 40px; display: flex; flex-direction: column; align-items: center; }
    .stock-hub { background: #fff; border-radius: 45px; padding: 55px; width: 100%; max-width: 1150px; box-shadow: 0 40px 100px rgba(15, 28, 63, 0.05); }

    /* Health Badges */
    .status-badge { padding: 6px 14px; border-radius: 10px; font-size: 11px; font-weight: 800; text-transform: uppercase; }
    .status-critical { background: #fee2e2; color: var(--pink); border: 1px solid #fecaca; }
    .status-warning { background: #fff7ed; color: var(--amber); border: 1px solid #fed7aa; }
    .status-healthy { background: #ecfdf5; color: #059669; border: 1px solid #d1fae5; }

    /* Table Intelligence */
    .stock-table { width: 100%; border-collapse: separate; border-spacing: 0 15px; margin-top: 30px; }
    .stock-row { background: #fff; transition: 0.4s; }
    .stock-row:hover { transform: scale(1.015); box-shadow: 0 20px 40px rgba(0,0,0,0.04); }
    .stock-row td { padding: 20px; vertical-align: middle; border-top: 1px solid #f8fafc; border-bottom: 1px solid #f8fafc; }
    
    .prod-img { width: 60px; height: 60px; border-radius: 18px; object-fit: cover; }
    .qty-control { width: 90px; padding: 12px; border-radius: 12px; border: 2px solid #f1f5f9; font-weight: 900; text-align: center; color: var(--navy); }
    .btn-sync-stock { background: var(--navy); color: #fff; border: none; padding: 12px 20px; border-radius: 12px; font-weight: 800; cursor: pointer; transition: 0.3s; }
    .btn-sync-stock:hover { background: var(--teal); transform: translateY(-2px); }
</style>

<div class="admin-main">
    <div class="stock-hub animate__animated animate__fadeInUp">
        <div style="text-align: center; margin-bottom: 50px;">
            <i class="fas fa-warehouse fa-3x" style="color: var(--teal); margin-bottom: 15px;"></i>
            <h2 style="font-weight: 900; color: var(--navy); font-size: 32px; letter-spacing: -1px;">Inventory Health Intelligence</h2>
            <p style="color: #94a3b8; font-weight: 600;">Real-time synchronization of variant protocols</p>
        </div>

        <table class="stock-table">
            <thead>
                <tr style="text-align:left; font-size:11px; color:#94a3b8; text-transform:uppercase; letter-spacing: 2px;">
                    <th>Media</th><th>Identity & Size</th><th>Operating Status</th><th>Stock Level</th><th>Sync Protocol</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($inventory_res)): 
                    // Logic to determine health status
                    $status_class = "status-healthy"; $status_text = "Safe";
                    if($row['stock_qty'] <= 0) { $status_class = "status-critical"; $status_text = "Depleted"; }
                    elseif($row['stock_qty'] < 5) { $status_class = "status-warning"; $status_text = "Low Stock"; }
                ?>
                <tr class="stock-row">
                    <td style="border-radius: 20px 0 0 20px;"><img src="../uploads/products/<?php echo $row['base_image']; ?>" class="prod-img"></td>
                    <td>
                        <div style="font-weight: 900; color: var(--navy);"><?php echo $row['name']; ?></div>
                        <div style="font-size: 11px; color: var(--teal); font-weight: 800; text-transform: uppercase;">
                            Variant: <?php echo $row['weight_size']; ?> | <?php echo $row['life_stage']; ?>
                        </div>
                    </td>
                    <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                    <td><div style="font-weight: 900; font-size: 18px;"><?php echo $row['stock_qty']; ?> <small style="font-size: 10px; color:#94a3b8;">Units</small></div></td>
                    <td style="border-radius: 0 20px 20px 0;">
                        <form method="POST" style="display: flex; gap: 12px; align-items: center;">
                            <input type="hidden" name="variant_id" value="<?php echo $row['id']; ?>">
                            <input type="number" name="quantity" class="qty-control" value="<?php echo $row['stock_qty']; ?>" min="0">
                            <button type="submit" name="sync_stock" class="btn-sync-stock"><i class="fas fa-sync"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
<?php if(isset($_SESSION['alert'])): ?>
    Swal.fire({
        icon: '<?php echo $_SESSION['alert']['type']; ?>',
        title: '<?php echo $_SESSION['alert']['msg']; ?>',
        showConfirmButton: false, timer: 2000, background: '#fff', color: '#0f1c3f'
    });
<?php unset($_SESSION['alert']); endif; ?>
</script>

<?php include "includes/footer.php"; ?>