<?php
// backend/config/security.php

class Security {
    public static function sanitize($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
    
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    public static function csrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public static function verifyCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    public static function rateLimit($key, $limit = 10, $timeout = 3600) {
        $filename = sys_get_temp_dir() . '/' . md5($key);
        $requests = file_exists($filename) ? (int)file_get_contents($filename) : 0;
        
        if ($requests >= $limit) {
            return false;
        }
        
        file_put_contents($filename, $requests + 1);
        return true;
    }
}
?>