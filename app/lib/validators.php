<?php
// app/lib/validators.php - basic validation helpers

declare(strict_types=1);

function v_required(string $value): bool { return trim($value) !== ''; }
function v_email(?string $value): bool { return $value === null || $value === '' || filter_var($value, FILTER_VALIDATE_EMAIL) !== false; }
function v_date(?string $value): bool { if ($value === null || $value === '') return true; return (bool)strtotime($value); }
function v_enum(?string $value, array $allowed): bool { return $value === null || in_array($value, $allowed, true); }

function v_phone(?string $value): bool {
    if ($value === null || $value === '') return true;
    // Allow digits, spaces, plus sign, dashes, parentheses; at least 7 digits
    $digits = preg_replace('/\D/', '', $value);
    return strlen($digits) >= 7 && preg_match('/^[\d\s\+\-\(\)\.]+$/', $value) === 1;
}

function v_passport(?string $value): bool {
    if ($value === null || $value === '') return true;
    // Most passports: letters and digits, typically 6-9 characters
    return preg_match('/^[A-Z0-9]{6,15}$/i', $value) === 1;
}

/**
 * Validate that an employment/education period has a start date before end date.
 */
function v_date_range(?string $start, ?string $end): bool {
    if (empty($start) || empty($end)) return true;
    $startTs = strtotime($start);
    $endTs = strtotime($end);
    if ($startTs === false || $endTs === false) return true;
    return $startTs <= $endTs;
}
