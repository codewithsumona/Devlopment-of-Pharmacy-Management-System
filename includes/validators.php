<?php
// Basic input validation and sanitization helpers used across endpoints
function sanitize_string($s) {
    if ($s === null) return '';
    return trim(filter_var($s, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
}

function sanitize_int($v, $default = 0) {
    if (!isset($v) || $v === '') return $default;
    return filter_var($v, FILTER_VALIDATE_INT) !== false ? intval($v) : $default;
}

function validate_period($p) {
    $p = strtolower(sanitize_string($p));
    return in_array($p, ['today','week','month']) ? $p : 'week';
}

function validate_tab($t) {
    $t = strtolower(sanitize_string($t));
    $allowed = ['sales','purchases','inventory','expiry','low_stock'];
    return in_array($t, $allowed) ? $t : 'sales';
}

function clamp_threshold($v, $min = 1, $max = 1000, $default = 10) {
    $n = sanitize_int($v, $default);
    if ($n < $min) return $min;
    if ($n > $max) return $max;
    return $n;
}

?>
