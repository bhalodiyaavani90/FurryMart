<?php
/**
 * FurryMart FREE Callback Request System
 * NO API COSTS - Customer requests callback, you call them manually!
 * 
 * How it works:
 * 1. Customer enters phone number
 * 2. System saves to database & sends email to you
 * 3. You receive notification and call them back manually
 * 4. Completely FREE - No Exotel/Twilio needed!
 */

header('Content-Type: application/json');
require_once 'db.php';

// FurryMart contact email for notifications
$notificationEmail = 'customercare@furrymart.com';
$furryMartPhone = '+91 99257 08543';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $userPhone = isset($_POST['user_phone']) ? trim($_POST['user_phone']) : '';
    $userName = isset($_POST['user_name']) ? trim($_POST['user_name']) : 'Guest';
    
    // Clean phone number - remove +91 if present
    $userPhone = str_replace(['+91', '+', ' ', '-'], '', $userPhone);
    
    // Validate
    if (empty($userPhone) || strlen($userPhone) != 10 || !is_numeric($userPhone)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please provide a valid 10-digit mobile number'
        ]);
        exit;
    }
    
    try {
        // Save callback request to database
        $requestTime = date('Y-m-d H:i:s');
        $status = 'pending';
        
        $stmt = $conn->prepare("INSERT INTO callback_requests (customer_name, customer_phone, request_time, status) VALUES (?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param("ssss", $userName, $userPhone, $requestTime, $status);
            
            if ($stmt->execute()) {
                $requestId = $conn->insert_id;
                
                // Send email notification to FurryMart team
                $subject = "🔔 New Callback Request #" . $requestId;
                $message = "
                    <html>
                    <head>
                        <style>
                            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                            .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f8f9fa; }
                            .header { background: linear-gradient(135deg, #f43f5e, #dc2626); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                            .content { background: white; padding: 30px; border-radius: 0 0 10px 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
                            .info-box { background: #fff9f5; padding: 15px; border-left: 4px solid #f43f5e; margin: 20px 0; border-radius: 5px; }
                            .phone-number { font-size: 24px; font-weight: bold; color: #f43f5e; letter-spacing: 2px; }
                            .button { display: inline-block; padding: 12px 30px; background: #f43f5e; color: white; text-decoration: none; border-radius: 25px; font-weight: bold; margin-top: 15px; }
                            .footer { text-align: center; margin-top: 20px; color: #64748b; font-size: 12px; }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <div class='header'>
                                <h2>📞 NEW CALLBACK REQUEST</h2>
                                <p>Request ID: #" . $requestId . "</p>
                            </div>
                            <div class='content'>
                                <h3 style='color: #1e293b; margin-bottom: 5px;'>Customer Details:</h3>
                                <div class='info-box'>
                                    <p><strong>👤 Name:</strong> " . htmlspecialchars($userName) . "</p>
                                    <p><strong>📱 Phone:</strong> <span class='phone-number'>+91 " . htmlspecialchars($userPhone) . "</span></p>
                                    <p><strong>🕐 Request Time:</strong> " . date('d M Y, h:i A') . "</p>
                                    <p><strong>📊 Status:</strong> <span style='color: #f59e0b; font-weight: bold;'>PENDING</span></p>
                                </div>
                                
                                <h3 style='color: #1e293b; margin-top: 25px;'>Action Required:</h3>
                                <p style='color: #64748b;'>Please call this customer as soon as possible. They are waiting for your call!</p>
                                
                                <div style='text-align: center; margin: 25px 0;'>
                                    <a href='tel:+91" . htmlspecialchars($userPhone) . "' class='button'>
                                        📞 CALL NOW: +91 " . htmlspecialchars($userPhone) . "
                                    </a>
                                </div>
                                
                                <div style='background: #f0fdf4; padding: 15px; border-radius: 8px; margin-top: 20px; border: 2px solid #86efac;'>
                                    <p style='margin: 0; color: #15803d; font-size: 14px;'>
                                        <strong>💡 Tip:</strong> Call within 2-3 minutes for best customer experience. Have their order/query details ready!
                                    </p>
                                </div>
                            </div>
                            <div class='footer'>
                                <p>This is an automated notification from FurryMart Callback System</p>
                                <p>To manage requests, visit: <a href='http://localhost/FURRYMART/admin/' style='color: #f43f5e;'>Admin Dashboard</a></p>
                            </div>
                        </div>
                    </body>
                    </html>
                ";
                
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= "From: FurryMart Callback System <noreply@furrymart.com>" . "\r\n";
                
                mail($notificationEmail, $subject, $message, $headers);
                
                // Success response
                echo json_encode([
                    'success' => true,
                    'message' => 'Callback request received! We\'ll call you within 2-3 minutes.',
                    'request_id' => $requestId,
                    'phone' => '+91 ' . $userPhone
                ]);
                
            } else {
                throw new Exception('Failed to save request');
            }
            
            $stmt->close();
        } else {
            throw new Exception('Database error');
        }
        
    } catch (Exception $e) {
        // If database fails, still send email
        $subject = "🔔 Callback Request (No DB)";
        $message = "
            <h3>New Callback Request</h3>
            <p><strong>Name:</strong> " . htmlspecialchars($userName) . "</p>
            <p><strong>Phone:</strong> +91 " . htmlspecialchars($userPhone) . "</p>
            <p><strong>Time:</strong> " . date('d M Y, h:i A') . "</p>
            <p><a href='tel:+91" . htmlspecialchars($userPhone) . "'>Click to Call</a></p>
        ";
        $headers = "Content-type:text/html;charset=UTF-8" . "\r\n";
        mail($notificationEmail, $subject, $message, $headers);
        
        echo json_encode([
            'success' => true,
            'message' => 'Request sent! We\'ll call you shortly.',
            'phone' => '+91 ' . $userPhone
        ]);
    }
    
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>
