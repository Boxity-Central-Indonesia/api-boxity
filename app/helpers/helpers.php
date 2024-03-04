<?php

if (!function_exists('formatRupiah')) {
    function formatRupiah($amount)
    {
        // Convert the amount to a numeric value if it's a string
        $amount = is_numeric($amount) ? $amount : floatval(preg_replace('/[^0-9.]/', '', $amount));

        // Format the numeric value
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
