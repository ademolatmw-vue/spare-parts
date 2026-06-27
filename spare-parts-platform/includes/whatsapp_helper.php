<?php
// SparePartsNG WhatsApp helper (shared)
// - Normalizes Nigerian phone numbers to international format (234...)
// - Builds WhatsApp wa.me links

function whatsapp_normalize_phone(string $raw): string {
    $raw = trim($raw);
    if ($raw === '') return '';

    // Remove spaces, dashes, parentheses, plus signs
    $raw = preg_replace('/[\s\-()]/', '', $raw);

    // If already starts with +234
    if (strpos($raw, '+234') === 0) {
        return '234' . substr($raw, 4);
    }

    // If starts with 234
    if (strpos($raw, '234') === 0) {
        return $raw;
    }

    // If starts with 0 (local format)
    if (isset($raw[0]) && $raw[0] === '0') {
        return '234' . substr($raw, 1);
    }

    // If starts with 08...
    if (strpos($raw, '08') === 0) {
        return '234' . substr($raw, 2);
    }

    // Fallback: if it contains digits, just strip leading '+'
    return ltrim($raw, '+');
}

function whatsapp_message_default(): string {
    return "Hello,\n\nI found your shop on SparePartsNG.\nI am interested in one of your spare parts.\nPlease provide availability and additional details.\n\nThank you.";
}

function whatsapp_link(string $rawPhone, string $message = ''): string {
    $phone = whatsapp_normalize_phone($rawPhone);
    if ($phone === '') return '';

    $base = 'https://wa.me/' . $phone;

    $message = trim($message);
    if ($message === '') return $base;

    return $base . '?text=' . rawurlencode($message);
}

