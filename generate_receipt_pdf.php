<?php
session_start();
include 'db.php';

// Set timezone to India Standard Time
date_default_timezone_set('Asia/Kolkata');

// 1. AUTHENTICATION PROTOCOL
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$order_id = (int)$_GET['id'];
$user_email = $_SESSION['email'];

// 2. MASTER LOGISTICS FETCH with Payment Status
$order_sql = "SELECT *, COALESCE(payment_status, 'Pending') as payment_status FROM orders WHERE id = $order_id AND user_email = '$user_email'";
$order_result = mysqli_query($conn, $order_sql);
$order = mysqli_fetch_assoc($order_result);

if (!$order) {
    die("Logistics Error: Protocol not found in infrastructure.");
}

// 3. SYNCHRONIZED ITEM FETCH
$items_sql = "SELECT * FROM order_items WHERE order_id = $order_id";
$items_result = mysqli_query($conn, $items_sql);

include "includes/header.php"; 
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">

<style>
    :root { 
        --primary: #518992; --navy: #0f1c3f; --bg: #f8fafc; 
        --border: #eef2f6; --accent: #f87171; --gold: #92400e;
        --success: #16a34a;
    }
    
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f4f7f6; color: var(--navy); }

    /* --- THE MASTER RECEIPT VAULT --- */
    .receipt-vault {
        max-width: 1000px; margin: 60px auto; background: #fff;
        padding: 70px; border-radius: 50px; border: 1px solid var(--border);
        box-shadow: 0 50px 120px rgba(15, 28, 63, 0.08); position: relative;
    }

    /* PRINT PROTOCOL: browser PDF logic */
    @media print {
        body { background: #fff; padding: 0; -webkit-print-color-adjust: exact; }
        .no-print { display: none !important; }
        .receipt-vault { 
            box-shadow: none !important; 
            border: 3px solid #518992 !important; /* Add elegant border for print */
            border-radius: 0 !important;
            width: 100% !important; margin: 0 !important; padding: 20px !important;
        }
        footer, .nav-container, .whatsapp-btn { display: none !important; }
        @page { margin: 1cm; }
    }

    /* --- METADATA DASHBOARD GRID --- */
    .meta-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 60px; }
    .meta-item {
        background: #fcfdfe; padding: 25px 20px; border-radius: 25px;
        border: 2px solid var(--border); text-align: center; transition: 0.3s;
    }
    .meta-item i { color: var(--primary); font-size: 22px; margin-bottom: 12px; display: block; opacity: 0.8; }
    .meta-label { font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1.5px; }
    .meta-value { font-size: 14px; font-weight: 800; color: var(--navy); margin-top: 6px; }

    /* --- SOVEREIGN ITEM GRID (TABLE) --- */
    .table-shell { border-radius: 30px; overflow: hidden; border: 2px solid var(--border); margin-bottom: 50px; }
    .items-table { width: 100%; border-collapse: collapse; }
    .items-table thead { background: var(--navy); color: white; }
    .items-table th { 
        padding: 25px; text-align: left; font-size: 12px; font-weight: 900; 
        text-transform: uppercase; letter-spacing: 2px; 
    }
    .items-table td { padding: 22px 25px; border-bottom: 1.5px solid #f1f5f9; font-size: 15px; vertical-align: middle; }
    .items-table tr:nth-child(even) { background: #fbfcfd; }
    .items-table tr:last-child td { border: none; }

    /* --- SUMMARY DASHBOARD VAULT --- */
    .summary-vault {
        float: right; width: 380px; background: #fffbeb; 
        padding: 35px; border-radius: 35px; border: 2.5px dashed #fde68a;
    }
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 15px; }
    .total-protocol { 
        border-top: 3px solid #fbbf24; margin-top: 25px; padding-top: 25px;
        display: flex; justify-content: space-between; align-items: center;
    }

    .btn-execute {
        background: linear-gradient(135deg, var(--primary) 0%, var(--navy) 100%);
        color: #fff; border: none; padding: 20px 50px; border-radius: 60px;
        font-weight: 900; font-size: 15px; letter-spacing: 1.5px; cursor: pointer;
        display: block; margin: 0 auto 40px; transition: 0.4s;
        box-shadow: 0 20px 40px rgba(81, 137, 146, 0.3);
    }
    .btn-execute:hover { transform: translateY(-4px) scale(1.02); box-shadow: 0 25px 50px rgba(15, 28, 63, 0.4); }

    .status-active { color: var(--success); font-weight: 900; }
</style>

<div class="container">
    <div class="no-print" style="text-align: center; padding-top: 40px;">
        <button class="btn-execute" onclick="window.print()">
            <i class="fas fa-file-invoice-dollar" style="margin-right:12px;"></i> GENERATE PERFECT RECEIPT
        </button>
    </div>

    <div class="receipt-vault">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 4px solid var(--primary); padding-bottom: 40px; margin-bottom: 50px;">
            <div>
                <h1 style="font-size: 56px; font-weight: 900; color: var(--navy); margin: 0; letter-spacing: -2px;">FurryMart</h1>
                <p style="font-size: 11px; color: var(--primary); font-weight: 900; text-transform: uppercase; letter-spacing: 6px; margin: 8px 0 0;">Pet Care Intelligence Protocol</p>
            </div>
            <div style="text-align: right;">
                <div style="font-weight: 900; color: var(--primary); font-size: 24px;">TAX RECEIPT</div>
                <div style="font-size: 13px; font-weight: 700; color: #94a3b8; margin-top: 5px;">Registry ID: #<?php echo $order['order_reference']; ?></div>
            </div>
        </div>

        <div class="meta-grid">
            <div class="meta-item">
                <i class="fas fa-barcode"></i>
                <div class="meta-label">Reference</div>
                <div class="meta-value"><?php echo $order['order_reference']; ?></div>
            </div>
            <div class="meta-item">
                <i class="fas fa-clock-rotate-left"></i>
                <div class="meta-label">Logistics Date</div>
                <div class="meta-value"><?php echo date('d M Y', strtotime($order['order_date'])); ?></div>
            </div>
            <div class="meta-item">
                <i class="fas fa-wallet"></i>
                <div class="meta-label">Transaction</div>
                <div class="meta-value"><?php echo $order['payment_method']; ?></div>
            </div>
            <div class="meta-item" style="<?php echo $order['payment_status'] == 'Paid' ? 'background: #d1fae5; border-color: #34d399;' : 'background: #fef3c7; border-color: #fbbf24;'; ?>">
                <i class="fas <?php echo $order['payment_status'] == 'Paid' ? 'fa-check-circle' : 'fa-clock'; ?>" style="<?php echo $order['payment_status'] == 'Paid' ? 'color: #16a34a;' : 'color: #d97706;'; ?>"></i>
                <div class="meta-label">Payment Status</div>
                <div class="meta-value" style="<?php echo $order['payment_status'] == 'Paid' ? 'color: #065f46;' : 'color: #92400e;'; ?>"><?php echo strtoupper($order['payment_status']); ?></div>
            </div>
            <div class="meta-item">
                <i class="fas fa-satellite-dish"></i>
                <div class="meta-label">Grid Status</div>
                <div class="meta-value status-active"><?php echo strtoupper($order['order_status']); ?></div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 80px; margin-bottom: 60px;">
            <div>
                <h4 style="font-weight: 900; color: var(--navy); text-transform: uppercase; font-size: 13px; border-bottom: 2px solid var(--border); padding-bottom: 12px; margin-bottom: 20px;">
                    <i class="fas fa-user-circle" style="margin-right:10px; color:var(--primary);"></i> Customer Intelligence
                </h4>
                <div style="font-size: 15px; line-height: 1.8; font-weight: 700; color: #475569;">
                    <?php echo $order['customer_name']; ?><br>
                    <span style="font-weight:500; color:#94a3b8;"><?php echo $order['user_email']; ?></span><br>
                    <span style="font-weight:500; color:#94a3b8;"><?php echo $order['customer_phone']; ?></span>
                </div>
            </div>
            <div>
                <h4 style="font-weight: 900; color: var(--navy); text-transform: uppercase; font-size: 13px; border-bottom: 2px solid var(--border); padding-bottom: 12px; margin-bottom: 20px;">
                    <i class="fas fa-truck-fast" style="margin-right:10px; color:var(--primary);"></i> Delivery Logistics
                </h4>
                <div style="font-size: 15px; line-height: 1.8; font-weight: 700; color: #475569;">
                    <?php echo $order['shipping_address']; ?><br>
                    <span style="font-weight:500; color:#94a3b8;"><?php echo $order['city']; ?>, <?php echo $order['state']; ?></span><br>
                    <span style="font-weight:500; color:#94a3b8;"><?php echo $order['zip_code']; ?>, INDIA</span>
                </div>
            </div>
        </div>

        <div class="table-shell">
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Product Specification</th>
                        <th style="text-align: center;">Intelligence Rate</th>
                        <th style="text-align: center;">Capacity</th>
                        <th style="text-align: right;">Logistics Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($item = mysqli_fetch_assoc($items_result)): ?>
                    <tr>
                        <td>
                            <div style="font-weight: 900; color: var(--navy);"><?php echo $item['product_name']; ?></div>
                            <div style="font-size: 11px; color: var(--primary); font-weight: 800; text-transform: uppercase; margin-top: 4px;">SYNC WEIGHT: <?php echo $item['weight_size']; ?></div>
                        </td>
                        <td style="text-align: center; font-weight: 700; color: var(--navy);">₹<?php echo number_format($item['price'], 2); ?></td>
                        <td style="text-align: center; font-weight: 900; color: var(--primary);">x <?php echo $item['quantity']; ?></td>
                        <td style="text-align: right; font-weight: 900; font-size: 17px; color: var(--navy);">₹<?php echo number_format($item['subtotal'], 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <?php
        // Calculate subtotal from order items
        mysqli_data_seek($items_result, 0); // Reset pointer to beginning
        $subtotal = 0;
        while($item = mysqli_fetch_assoc($items_result)) {
            $subtotal += $item['subtotal'];
        }
        
        // Calculate shipping (same logic as checkout)
        $free_shipping_threshold = 499;
        $shipping_charges = 0;
        if ($subtotal > 0 && $subtotal < $free_shipping_threshold) {
            $shipping_charges = 40; // ₹40 shipping fee
        }
        
        // Calculate GST (18%)
        $gst_rate = 0.18;
        $gst_amount = ($subtotal + $shipping_charges) * $gst_rate;
        
        // Grand total should match what's in database
        $calculated_total = $subtotal + $shipping_charges + $gst_amount;
        ?>

        <div class="summary-vault">
            <div class="summary-row">
                <span style="font-weight:900; color:var(--gold); font-size:11px; text-transform:uppercase; letter-spacing:1px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-shopping-bag" style="color: var(--primary);"></i> Subtotal
                </span>
                <strong style="font-size:17px;">₹<?php echo number_format($subtotal, 2); ?></strong>
            </div>
            <div class="summary-row">
                <span style="font-weight:900; color:var(--gold); font-size:11px; text-transform:uppercase; letter-spacing:1px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-truck-fast" style="color: var(--primary);"></i> Shipping
                </span>
                <?php if ($shipping_charges == 0): ?>
                    <strong style="color: var(--success);">
                        FREE <span style="background: rgba(22, 163, 74, 0.15); padding: 2px 6px; border-radius: 6px; font-size: 9px; margin-left: 4px;">SAVED ₹40</span>
                    </strong>
                <?php else: ?>
                    <strong style="color: #dc2626;">₹<?php echo number_format($shipping_charges, 2); ?></strong>
                <?php endif; ?>
            </div>
            <div class="summary-row">
                <span style="font-weight:900; color:var(--gold); font-size:11px; text-transform:uppercase; letter-spacing:1px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-receipt" style="color: var(--primary);"></i> GST (18%)
                </span>
                <strong style="font-size:17px;">₹<?php echo number_format($gst_amount, 2); ?></strong>
            </div>
            <div class="total-protocol">
                <div style="line-height:1.1;">
                    <span style="font-weight:900; color:var(--navy); font-size:26px; letter-spacing:-1px;">GRAND TOTAL</span><br>
                    <small style="font-size:10px; font-weight:900; color:var(--gold);">ALL INCLUSIVE</small>
                </div>
                <span style="font-weight: 900; color: var(--navy); font-size: 32px;">₹<?php echo number_format($order['total_amount'], 2); ?></span>
            </div>
        </div>

        <div style="clear: both;"></div>

        <?php if($order['order_status'] == 'Delivered'): ?>
            <div style="background: #f0fdf4; border: 2.5px solid #bbf7d0; padding: 30px; border-radius: 30px; text-align: center; margin-top: 60px;">
                <i class="fas fa-shield-check" style="color:var(--success); font-size:36px; margin-bottom:12px; display:block;"></i>
                <strong style="color:#166534; font-size:20px; letter-spacing:-0.5px;">PROTOCOL VERIFIED: LOGISTICS CLOSED SUCCESSFULLY</strong>
                <p style="margin:8px 0 0; color:#166534; font-size:13px; font-weight:600;">Infrastructure handover completed on: <?php echo date('d M Y', strtotime($order['delivery_date'])); ?></p>
            </div>
        <?php endif; ?>

        <!-- FurryMart Official Stamp with Real Image -->
        <div style="display: grid; grid-template-columns: 1fr auto; gap: 30px; align-items: center; margin-top: 50px; padding: 25px; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-radius: 15px; border: 2px dashed var(--primary);">
            <!-- Left: Signature Section -->
            <div>
                <div style="border-bottom: 2px solid var(--navy); width: 200px; margin-bottom: 8px; padding-bottom: 5px;">
                    <p style="margin: 0; font-family: 'Brush Script MT', cursive; font-size: 24px; color: var(--primary); font-weight: bold;">FurryMart</p>
                </div>
                <p style="margin: 0; font-size: 11px; font-weight: 700; color: #94a3b8;">AUTHORIZED SIGNATURE</p>
                <p style="margin: 5px 0 0 0; font-size: 10px; color: #94a3b8;">Date: <?php echo date('d M Y'); ?></p>
            </div>
            
            <!-- Right: Official Stamp Image (Diagonal) -->
            <div style="position: relative; width: 200px; height: 200px;">
                <!-- Real Stamp Image (rotated diagonally) -->
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(15deg); width: 200px; height: 200px;">
                    <!-- Stamp Image with Realistic Ink Effect -->
                    <img src="uploads/STAMP.png" 
                         alt="FurryMart Official Stamp" 
                         style="width: 100%; 
                                height: 100%; 
                                object-fit: contain; 
                                mix-blend-mode: darken; 
                                filter: contrast(1.4) brightness(0.85) saturate(1.3) hue-rotate(-5deg);
                                opacity: 0.88;">
                    
                    <!-- Realistic Ink Texture Overlay -->
                    <div style="position: absolute; inset: 0; background: 
                        radial-gradient(circle at 25% 25%, rgba(30, 58, 138, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 75% 30%, rgba(30, 58, 138, 0.12) 0%, transparent 45%),
                        radial-gradient(circle at 70% 75%, rgba(30, 58, 138, 0.18) 0%, transparent 55%),
                        radial-gradient(circle at 30% 70%, rgba(30, 58, 138, 0.14) 0%, transparent 48%);
                        mix-blend-mode: multiply; 
                        pointer-events: none;"></div>
                    
                    <!-- Ink Imperfections & Splatters -->
                    <div style="position: absolute; top: 12%; left: 8%; width: 5px; height: 5px; background: rgba(30, 58, 138, 0.35); border-radius: 50%; filter: blur(1px);"></div>
                    <div style="position: absolute; top: 22%; right: 12%; width: 4px; height: 4px; background: rgba(30, 58, 138, 0.3); border-radius: 50%; filter: blur(0.8px);"></div>
                    <div style="position: absolute; bottom: 18%; left: 15%; width: 6px; height: 6px; background: rgba(30, 58, 138, 0.28); border-radius: 50%; filter: blur(1.2px);"></div>
                    <div style="position: absolute; bottom: 28%; right: 10%; width: 5px; height: 5px; background: rgba(30, 58, 138, 0.32); border-radius: 50%; filter: blur(1px);"></div>
                    <div style="position: absolute; top: 45%; left: 5%; width: 3px; height: 3px; background: rgba(30, 58, 138, 0.25); border-radius: 50%; filter: blur(0.7px);"></div>
                    <div style="position: absolute; top: 60%; right: 8%; width: 4px; height: 4px; background: rgba(30, 58, 138, 0.27); border-radius: 50%; filter: blur(0.9px);"></div>
                </div>
            </div>
        </div>

        <div style="margin-top: 50px; text-align: center; border-top: 2px solid var(--border); padding-top: 40px;">
            <p style="font-size: 12px; color: #94a3b8; font-weight: 800; text-transform: uppercase; letter-spacing: 3px; margin-bottom: 8px;">Synchronized shopping experience by FurryMart</p>
            <p style="font-size: 11px; color: #94a3b8; font-weight: 600;">Contact Intelligence: care@furrymart.com | Infrastructure Support: +123 456 789</p>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>