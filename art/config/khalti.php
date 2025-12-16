<?php
// Khalti Payment Gateway Configuration
// For production, replace with your actual Khalti API credentials

define('KHALTI_PUBLIC_KEY', 'test_public_key_dc74e0fd57cb46cd93832aee0a507256'); // Test public key
define('KHALTI_SECRET_KEY', 'test_secret_key_24be8b0d31a3452cad5f9c5e42111979'); // Test secret key
define('KHALTI_BASE_URL', 'https://a.khalti.com/api/v2/');

// Khalti API endpoints
define('KHALTI_INITIATE_URL', KHALTI_BASE_URL . 'epayment/initiate/');
define('KHALTI_VERIFY_URL', KHALTI_BASE_URL . 'epayment/lookup/');

// Website configuration
define('WEBSITE_URL', 'http://localhost/artstore_deploy_ready/art/');
define('SUCCESS_URL', WEBSITE_URL . 'payment_success.php');
define('FAILURE_URL', WEBSITE_URL . 'checkout.php');

// Khalti configuration for ePayment
define('KHALTI_CONFIG', [
    'return_url' => SUCCESS_URL,
    'website_url' => WEBSITE_URL,
    'amount' => 0, // Will be set dynamically
    'purchase_order_id' => '', // Will be set dynamically
    'purchase_order_name' => 'Art Purchase',
    'customer_info' => [
        'name' => '', // Will be set dynamically
        'email' => '', // Will be set dynamically
        'phone' => '' // Will be set dynamically
    ]
]);
?>