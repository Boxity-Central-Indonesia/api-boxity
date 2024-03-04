<?php
use App\Models\businesses;

if (!function_exists('formatRupiah')) {
    function formatRupiah($amount)
    {
        // Convert the amount to a numeric value if it's a string
        $amount = is_numeric($amount) ? $amount : floatval(preg_replace('/[^0-9.]/', '', $amount));

        // Format the numeric value
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
if (!function_exists('getCompanyName')) {
    function getCompanyName()
    {
        // Assuming you want to get the latest business record
        $latestBusiness = businesses::latest()->first();

        // Check if a record is found before accessing the property
        if ($latestBusiness) {
            return $latestBusiness->nama_bisnis;
        }

        // Handle the case when no business record is found
        return 'Default Company Name';
    }
}
if (!function_exists('getCompanyAddress')) {
    function getCompanyAddress()
    {
        // Assuming you want to get the latest business record
        $latestBusiness = businesses::latest()->first();

        // Check if a record is found before accessing the property
        if ($latestBusiness) {
            return $latestBusiness->full_address;
        }

        // Handle the case when no business record is found
        return 'Default Company Address';
    }
}
if (!function_exists('getCompanyPhone')) {
    function getCompanyPhone()
    {
        // Assuming you want to get the latest business record
        $latestBusiness = businesses::latest()->first();

        // Check if a record is found before accessing the property
        if ($latestBusiness) {
            return $latestBusiness->phone_number;
        }

        // Handle the case when no business record is found
        return 'Default Company Phone';
    }
}
if (!function_exists('getCompanyEmail')) {
    function getCompanyEmail()
    {
        // Assuming you want to get the latest business record
        $latestBusiness = businesses::latest()->first();

        // Check if a record is found before accessing the property
        if ($latestBusiness) {
            return $latestBusiness->email;
        }

        // Handle the case when no business record is found
        return 'Default Company Mail';
    }
}
