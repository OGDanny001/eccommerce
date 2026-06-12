<?php
/**
 * Telegram Configuration File
 * Store your Telegram Bot credentials here
 */

// Load environment variables from .env file
require_once __DIR__ . '/../includes/env.php';

// Telegram Bot Token - from .env file
if (!defined('TELEGRAM_BOT_TOKEN')) {
    define('TELEGRAM_BOT_TOKEN', 'YOUR_TELEGRAM_BOT_TOKEN');
}

// Telegram Chat ID - from .env file
if (!defined('TELEGRAM_CHAT_ID')) {
    define('TELEGRAM_CHAT_ID', '8333125028');
}
