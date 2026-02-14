<?php
// backend/config/security.php

class Security {

    /**
     * Recursively sanitize input data (string or array)
     */
    public static function sanitize($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }
        // Remove whitespace, strip HTML tags, escape special characters
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate email format
     */
    public static function validateEmail($email) {
        return filter_var(trim($email), FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Generate a CSRF token and store it in session
     */
    public static function csrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify provided CSRF token against session
     */
    public static function verifyCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Simple file-based rate limiting
     * $key: unique key for the user/action (e.g., IP + endpoint)
     * $limit: maximum requests allowed
     * $timeout: window in seconds
     */
    public static function rateLimit($key, $limit = 10, $timeout = 3600) {
        $filename = sys_get_temp_dir() . '/' . md5($key);
        $data = file_exists($filename) ? json_decode(file_get_contents($filename), true) : ['count' => 0, 'time' => time()];

        // Reset counter if timeout expired
        if (time() - $data['time'] > $timeout) {
            $data['count'] = 0;
            $data['time'] = time();
        }

        if ($data['count'] >= $limit) {
            return false;
        }

        $data['count']++;
        file_put_contents($filename, json_encode($data));
        return true;
    }

    /**
     * Optional logging for security events
     */
    public static function logEvent($message) {
        $logfile = __DIR__ . '/security.log';
        $time = date('Y-m-d H:i:s');
        file_put_contents($logfile, "[$time] $message" . PHP_EOL, FILE_APPEND);
    }
}
?>
