<?php
/**
 * FurryMart Email Handler
 * Sends emails using PHP mail() or SMTP
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get form data
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    // Validate
    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please fill in all fields'
        ]);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email address'
        ]);
        exit;
    }
    
    // Email settings
    $to = "customercare@furrymart.com";
    $subject = "Customer Inquiry from FurryMart Website";
    
    // Email body
    $emailBody = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px; }
            .header { background: #518992; color: white; padding: 20px; border-radius: 10px 10px 0 0; text-align: center; }
            .content { padding: 20px; background: #f9f9f9; }
            .field { margin-bottom: 15px; padding: 10px; background: white; border-left: 4px solid #518992; }
            .label { font-weight: bold; color: #518992; }
            .footer { text-align: center; padding: 15px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>🐾 New Customer Inquiry - FurryMart</h2>
            </div>
            <div class='content'>
                <div class='field'>
                    <span class='label'>Name:</span><br>
                    $name
                </div>
                <div class='field'>
                    <span class='label'>Email:</span><br>
                    $email
                </div>
                <div class='field'>
                    <span class='label'>Message:</span><br>
                    $message
                </div>
            </div>
            <div class='footer'>
                <p>This email was sent from FurryMart Contact Form</p>
                <p>Time: " . date('Y-m-d H:i:s') . "</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: FurryMart Website <noreply@furrymart.com>" . "\r\n";
    $headers .= "Reply-To: $email" . "\r\n";
    
    // Send email
    $mailSent = mail($to, $subject, $emailBody, $headers);
    
    if ($mailSent) {
        // Send auto-reply to customer
        $autoReplySubject = "Thank you for contacting FurryMart! 🐾";
        $autoReplyBody = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #518992, #6ba9b5); color: white; padding: 30px; border-radius: 15px; text-align: center; }
                .content { padding: 25px; background: #fff9f5; border-radius: 10px; margin-top: 20px; }
                .btn { display: inline-block; padding: 12px 30px; background: #518992; color: white; text-decoration: none; border-radius: 25px; margin-top: 15px; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 13px; border-top: 1px solid #ddd; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🐾 Thank You, $name!</h1>
                </div>
                <div class='content'>
                    <p>Dear $name,</p>
                    <p>Thank you for reaching out to <strong>FurryMart</strong>! We've received your message and our customer care team will respond within <strong>24 hours</strong>.</p>
                    
                    <p><strong>Your query:</strong></p>
                    <p style='padding: 15px; background: white; border-left: 4px solid #518992; font-style: italic;'>$message</p>
                    
                    <p style='margin-top: 20px;'><strong>Need immediate assistance?</strong></p>
                    <p>📱 WhatsApp: +91 99257 08543<br>
                    📞 Call: +91 99257 08543 (Mon-Sat, 9:30 AM - 6:30 PM)<br>
                    📧 Email: customercare@furrymart.com</p>
                    
                    <a href='https://furrymart.com' class='btn'>Visit FurryMart</a>
                </div>
                <div class='footer'>
                    <p><strong>FurryMart</strong> - Your Pet's Paradise</p>
                    <p>Pastel Plaza, Nehru Place, New Delhi - 110019</p>
                    <p style='font-size: 11px; color: #999;'>This is an automated message. Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $autoReplyHeaders = "MIME-Version: 1.0" . "\r\n";
        $autoReplyHeaders .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $autoReplyHeaders .= "From: FurryMart Customer Care <customercare@furrymart.com>" . "\r\n";
        
        mail($email, $autoReplySubject, $autoReplyBody, $autoReplyHeaders);
        
        echo json_encode([
            'success' => true,
            'message' => 'Email sent successfully! Check your inbox for confirmation.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to send email. Please try again or use WhatsApp.'
        ]);
    }
    
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>
