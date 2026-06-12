<?php
/**
 * Centralized Notification Service
 * Handles Email, Telegram, WhatsApp, and SMS notifications
 */

// Include database connection
require_once __DIR__ . '/../config/db.php';

// Include Telegram configuration
require_once __DIR__ . '/../config/telegram.php';

// Configuration - Replace with your real API keys
define('TWILIO_SID', 'YOUR_TWILIO_SID');
define('TWILIO_TOKEN', 'YOUR_TWILIO_TOKEN');
define('TWILIO_PHONE', 'YOUR_TELEGRAM_PHONE_NUMBER');
define('TWILIO_WHATSAPP', 'YOUR_TWILIO_WHATSAPP_NUMBER'); // Format: whatsapp:+123456789

/**
 * Main function to send notifications across all enabled channels
 */
function sendNotification($user, $subject, $message, $channels = ['email', 'telegram', 'whatsapp', 'sms']) {
    $results = [];

    if (in_array('email', $channels)) {
        $results['email'] = sendEmailNotification($user['email'], $subject, $message);
    }

    if (in_array('telegram', $channels)) {
        $results['telegram'] = sendTelegramNotification($message);
    }

    if (in_array('whatsapp', $channels)) {
        $phone = isset($user['phone']) ? $user['phone'] : '';
        if ($phone) {
            $results['whatsapp'] = sendWhatsAppNotification($phone, $message);
        }
    }

    if (in_array('sms', $channels)) {
        $phone = isset($user['phone']) ? $user['phone'] : '';
        if ($phone) {
            $results['sms'] = sendSMSNotification($phone, $message);
        }
    }

    return $results;
}

/**
 * Send Email Notification
 * Note: For production, use PHPMailer or a service like SendGrid
 */
function sendEmailNotification($to, $subject, $message) {
    $headers = "From: LuxuryStore <noreply@luxurystore.com>\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    $htmlMessage = "
    <html>
    <body style='font-family: Arial, sans-serif; color: #333;'>
        <div style='background: #4f46e5; padding: 20px; color: white; text-align: center;'>
            <h2>LuxuryStore</h2>
        </div>
        <div style='padding: 20px;'>
            <h3>$subject</h3>
            <p>$message</p>
        </div>
        <div style='background: #f3f4f6; padding: 10px; font-size: 12px; text-align: center;'>
            &copy; " . date('Y') . " LuxuryStore. All rights reserved.
        </div>
    </body>
    </html>";

    return @mail($to, $subject, $htmlMessage, $headers);
}

/**
 * Send Telegram Message using cURL
 * 
 * @param string $message The message to send
 * @return bool True if message sent successfully, false otherwise
 */
function sendTelegramMessage($message) {
    // Check if bot token is configured
    if (TELEGRAM_BOT_TOKEN === 'YOUR_TELEGRAM_BOT_TOKEN') {
        return false;
    }

    // Telegram API URL
    $apiUrl = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage";

    // Prepare POST data
    $postData = http_build_query([
        'chat_id' => TELEGRAM_CHAT_ID,
        'text' => $message,
        'parse_mode' => 'HTML'
    ]);

    // Initialize cURL session
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification (for testing)
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Set timeout to 10 seconds

    // Execute cURL request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);

    // Close cURL session
    curl_close($ch);

    // Check for errors and return status
    if ($error) {
        return false;
    }

    // Check if HTTP code is 200 (OK)
    return $httpCode === 200;
}

/**
 * Send Telegram Notification
 */
function sendTelegramNotification($message) {
    // Call the new sendTelegramMessage function
    return sendTelegramMessage($message);
}

/**
 * Send WhatsApp Notification (Using Twilio)
 */
function sendWhatsAppNotification($to, $message) {
    if (TWILIO_SID === 'YOUR_TWILIO_SID') return false;

    $url = "https://api.twilio.com/2010-04-01/Accounts/" . TWILIO_SID . "/Messages.json";
    $to_whatsapp = "whatsapp:" . $to;
    
    $data = [
        'From' => TWILIO_WHATSAPP,
        'To' => $to_whatsapp,
        'Body' => $message
    ];

    $auth = base64_encode(TWILIO_SID . ":" . TWILIO_TOKEN);
    $options = [
        'http' => [
            'header'  => "Authorization: Basic $auth\r\nContent-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    $context  = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    return $result !== false;
}

/**
 * Send SMS Notification (Using Twilio)
 */
function sendSMSNotification($to, $message) {
    if (TWILIO_SID === 'YOUR_TWILIO_SID') return false;

    $url = "https://api.twilio.com/2010-04-01/Accounts/" . TWILIO_SID . "/Messages.json";
    
    $data = [
        'From' => TWILIO_PHONE,
        'To' => $to,
        'Body' => $message
    ];

    $auth = base64_encode(TWILIO_SID . ":" . TWILIO_TOKEN);
    $options = [
        'http' => [
            'header'  => "Authorization: Basic $auth\r\nContent-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    $context  = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    return $result !== false;
}

// =============================================
// DATABASE NOTIFICATION FUNCTIONS
// =============================================

/**
 * Create a database notification for a user
 */
function createNotification($user_id, $title, $message) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $title, $message);
    $success = $stmt->execute();
    $stmt->close();
    
    return $success;
}

/**
 * Get all notifications for a user
 */
function getUserNotifications($user_id, $limit = null) {
    global $conn;
    
    if ($limit) {
        $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
        $stmt->bind_param("ii", $user_id, $limit);
    } else {
        $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $user_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $notifications = [];
    
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    
    $stmt->close();
    return $notifications;
}

/**
 * Get count of unread notifications for a user
 */
function getUnreadNotificationCount($user_id) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = $row['count'];
    $stmt->close();
    
    return $count;
}

/**
 * Mark a single notification as read
 */
function markNotificationAsRead($notification_id) {
    global $conn;
    
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
    $stmt->bind_param("i", $notification_id);
    $success = $stmt->execute();
    $stmt->close();
    
    return $success;
}

/**
 * Mark all notifications as read for a user
 */
function markAllNotificationsAsRead($user_id) {
    global $conn;
    
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $success = $stmt->execute();
    $stmt->close();
    
    return $success;
}
