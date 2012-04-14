<?php
if (!defined('INCLUDED_AMEMBER_CONFIG')) 
    die("Direct access to this location is not allowed");

$notebook_page = 'WP App Store';
config_set_notebook_comment($notebook_page, 'Handles receiving the WP App Store\'s sale postback');
if (file_exists($rm = dirname(__FILE__)."/readme.txt"))
    config_set_readme($notebook_page, $rm);

add_config_field('payment.wpappstore.api_key', 'WP App Store API Key',
    'text', nl2br('Get your API key by logging into your <a href="https://wpappstore.com/dashboard/" target="_blank">WP App Store dashboard</a>.
    The API key received in the postback will be compared to the API key you
    set here to verify the postback is authentic.'),
    $notebook_page, 
    '','','',
    array());
add_config_field('payment.wpappstore.sku_prefix', 'SKU Vendor Prefix',
    'text', nl2br('Because SKUs need to be unique across the entire WP App Store,
    it is highly recommended you add a common prefix to your SKUs. For example,
    you might want your SKUs to look like \'ACME-21\', so you would enter the
    prefix \'ACME-\' here.'),
    $notebook_page, 
    '','','',
    array());
add_config_field('payment.wpappstore.new_member_email', 'Send new members an email',
    'checkbox', '',
    $notebook_page, 
    '','','',
    array('default' => '1'));
add_config_field('payment.wpappstore.new_member_email_subject', 'New Member Email Subject',
    'text', '',
    $notebook_page, 
    '','','',
    array('default' => 'Your new account login details'));
add_config_field('payment.wpappstore.new_member_email_body', 'New Member Email Body',
    'textarea', nl2br('
    The following variables will be replaced with the actual values:
    
    {FIRST_NAME} - User\'s first name
    {LAST_NAME} - User\'s last name
    {EMAIL} - User\'s email address
    {USERNAME} - The automatically generated username for this new account
    {PASSWORD} - The automatically generated password for this new account
    {LOGIN_URL} - URL to the aMember login page
    
    These variables can also be used in the subject field as well.
    '),
    $notebook_page, 
    '','','',
    array(
        'rows' => 12,
        'cols' => 80,
        'store_type' => 2, // blob
        'default' => "Hi {FIRST_NAME} {LAST_NAME},\r\n\r\nYou can login to your new account with the following:\r\n\r\n{LOGIN_URL}\r\nUsername: {USERNAME}\r\nPassword: {PASSWORD}\r\n\r\n"
    ));
add_config_field('payment.wpappstore.pay_confirmed_email', 'Send member a payment confirmed email',
    'checkbox', '',
    $notebook_page, 
    '','','',
    array('default' => '1'));
add_config_field('payment.wpappstore.pay_confirmed_email_subject', 'Payment Confirmed Email Subject',
    'text', '',
    $notebook_page, 
    '','','',
    array('default' => 'Your payment has been received'));
add_config_field('payment.wpappstore.pay_confirmed_email_body', 'Payment Confirmed Email Body',
    'textarea', nl2br('
    The following variables will be replaced with the actual values:
    
    {FIRST_NAME} - User\'s first name
    {LAST_NAME} - User\'s last name
    {EMAIL} - User\'s email address
    {PRODUCT_NAME} - Name of the product
    {PRODUCT_TYPE} - Product type, i.e. theme/plugin
    {COST_SUBTOTAL} - Subtotal of the order
    {COST_TOTAL} - Total cost of the order
    {CUSTOMER_SITE_URL} - URL to the customer\'s WP installation folder
    {LOGIN_URL} - URL to the aMember login page
    {WPAS_ORDER_ID} - WP App Store order ID.
    {BEGIN_DATE} - Date the subscription starts
    {EXPIRE_DATE} - Date the subscription ends
    
    These variables can also be used in the subject field as well.
    '),
    $notebook_page, 
    '','','',
    array(
        'rows' => 12,
        'cols' => 80,
        'store_type' => 2, // blob
        'default' => "Hi {FIRST_NAME} {LAST_NAME},\r\n\r\nWe have received notification of your WP App Store purchase ({PRODUCT_NAME} - #{WPAS_ORDER_ID}) and have updated your account accordingly. To review your account and request support, login at {LOGIN_URL}.\r\n\r\nA WP App Store receipt with the details of your order should arrive shortly.\r\n\r\n"
    ));
add_config_field('payment.wpappstore.disable_postback_log', 'Disable logging data received',
    'checkbox', '',
    $notebook_page, 
    '','','',
    array());
add_config_field('payment.wpappstore.disable_error_log', 'Disable error logging (not recommended)',
    'checkbox', '',
    $notebook_page, 
    '','','',
    array());
