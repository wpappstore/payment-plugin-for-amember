<?php
if (!defined('INCLUDED_AMEMBER_CONFIG')) 
    die("Direct access to this location is not allowed");

class payment_wpappstore extends amember_payment {
    var $public = 0;
    
    var $title = 'WP App Store';
    var $description = "Handles receiving the WP App Store's sale postback";
    var $fixed_price=0;
    var $recurring=0;
    var $built_in_trials=0;
    
    function init() {
        parent::init();
        
        add_payment_field('wpas_order_id', 'WP App Store Order ID', 'readonly');
        add_payment_field('wpas_site_url', 'WP App Store Customer Site URL', 'readonly');
        
        add_member_field('wpas_customer_id', 'WP App Store Customer ID', 'readonly');
    }
    
    function do_payment($payment_id, $member_id, $product_id,
            $price, $begin_date, $expire_date, &$vars){
        exit();
    }
    
    function process_postback($vars) {
        global $db;
        
        // Check API key to verify authenticity
        if (!isset($vars['api_key']) || $this->config['api_key'] != $vars['api_key']) {
            $this->postback_error('API key does not match.');
        }
        
        // Error if no email for some reason
        if (!isset($vars['customer']['email']) || $vars['customer']['email'] == '') {
            $this->postback_error('No email address received.');
        }
        
        // Error if no SKU for some reason
        if (!isset($vars['product']['sku']) || $vars['product']['sku'] == '') {
            $this->postback_error('No SKU received.');
        }

        $product_id = $vars['product']['sku'];
        if ($this->config['sku_prefix']) {
            $product_id = preg_replace('@^' . preg_quote($this->config['sku_prefix']) . '@', '', $product_id);
        }

        $product =& get_product($product_id);
        if (!isset($product->config['product_id'])) {
            $this->postback_error("No product with id '$product_id'.");
        }
        
        // Get existing user or create new user
        $users = $db->users_find_by_string($vars['customer']['email'], 'email', 1);
        if (isset($users[0]['member_id'])) {
            $member_id = $users[0]['member_id'];
        }
        else {
            $user = array(
                'name_f' => $vars['customer']['first_name'],
                'name_l' => $vars['customer']['last_name'],
                'email' => $vars['customer']['email']
            );
            
            $user['login'] = generate_login($user);
            $user['pass'] = generate_password();

            if (isset($vars['customer']['id'])) {
                $user['wpas_customer_id'] = $vars['customer']['id'];
            }
            
            $member_id = $db->add_pending_user($user);
            
            if ($this->config['new_member_email'] != '') {
                $this->send_new_user_email($user);
            }
        }
        
        // Add payment
        $begin_date = $product->get_start();
        $expire_date = $product->get_expire($begin_date);
        $paysys_id = $this->get_plugin_name();
        $receipt_id = $vars['order_id'];
        $amount = $vars['costs']['subtotal'];
        $completed = '1';
        $data = array();
        
        if (isset($vars['order_id'])) {
            $data['wpas_order_id'] = $vars['order_id'];
        }

        if (isset($vars['site_url'])) {
            $data['wpas_site_url'] = $vars['site_url'];
        }
        
        $data['wpas_data'] = $vars;

        $payment = compact('member_id', 'product_id', 'begin_date', 'expire_date', 'paysys_id', 'receipt_id', 'amount', 'completed', 'data');
        $db->add_payment($payment);
        
        if ($this->config['pay_confirmed_email'] != '') {
            $this->send_pay_confirmed_email($vars, $payment);
        }
    }
    
    function send_new_user_email($user) {
        global $config, $db;

        $user['login_url'] = $config['root_url'] . '/member.php';
        
        $map = array(
            'FIRST_NAME' => 'name_f',
            'LAST_NAME' => 'name_l',
            'EMAIL' => 'email',
            'USERNAME' => 'login',
            'PASSWORD' => 'pass',
            'LOGIN_URL' => 'login_url'
        );
        
        $body = $this->config['new_member_email_body'];
        $subject = $this->config['new_member_email_subject'];
        
        foreach ($map as $var => $key) {
            if (!isset($user[$key])) continue;
            $body = str_replace('{' . $var . '}', $user[$key], $body);
            $subject = str_replace('{' . $var . '}', $user[$key], $subject);
        }
        
        //$db->log_error(print_r(compact('body', 'subject'), true));
        
        mail_customer($user['email'], $body, $subject);
    }

    function send_pay_confirmed_email($vars, $payment) {
        global $config, $db;

        $replaces = array(
            'FIRST_NAME' => $vars['customer']['first_name'],
            'LAST_NAME' => $vars['customer']['last_name'],
            'EMAIL' => $vars['customer']['email'],
            'LOGIN_URL' => $config['root_url'] . '/member.php',
            'PRODUCT_NAME' => $vars['product']['name'],
            'PRODUCT_TYPE' => $vars['product']['type'],
            'COST_SUBTOTAL' => number_format($vars['costs']['subtotal'],2),
            'COST_TOTAL' => number_format($vars['costs']['total'],2),
            'CUSTOMER_SITE_URL' => $vars['site_url'],
            'WPAS_ORDER_ID' => $vars['order_id'],
            'BEGIN_DATE' => $payment['begin_date'],
            'EXPIRE_DATE' => $payment['expire_date']
        );
        
        $to = $vars['customer']['email'];
        $subject = $this->config['pay_confirmed_email_subject'];
        $body = $this->config['pay_confirmed_email_body'];
        
        foreach ($replaces as $var => $val) {
            $body = str_replace('{' . $var . '}', $val, $body);
            $subject = str_replace('{' . $var . '}', $val, $subject);
        }
        
        //$db->log_error(print_r(compact('to', 'body', 'subject'), true));
        
        mail_customer($to, $body, $subject);
    }

    function postback_log($msg='') {
        global $db;
        if ($this->config['disable_postback_log'] != '') return;
        $plugin = $this->get_plugin_name();
        $db->log_error("$plugin DEBUG: $msg<br />\n".nl2br(print_r($this->postback_vars, true)));
    }

    function postback_error($err) {
        global $db;
        $plugin = $this->get_plugin_name();
        $err = "$plugin ERROR: $err<br />\n".nl2br(print_r($this->postback_vars, true));
        if ($this->config['disable_error_log'] != '') $db->log_error($err);
        fatal_error($err);
    }
}

$pl = & instantiate_plugin('payment', 'wpappstore');
