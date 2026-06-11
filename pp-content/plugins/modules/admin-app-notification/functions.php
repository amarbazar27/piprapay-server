<?php
if (!defined('pp_allowed_access')) {
    die('Direct access not allowed');
}

// Hooks
add_action('pp_transaction_ipn', 'admin_app_push_notification_transaction_admin_ipn');
add_action('pp_invoice_ipn', 'admin_app_push_notification_invoice_admin_ipn');

// ✅ Helper: Send OneSignal Notification to Admin
function send_admin_onesignal_notification($title, $message) {
    $plugin_slug = 'admin-app-notification';
    $settings = pp_get_plugin_setting($plugin_slug);
    
    if(isset($settings['panel_id']) && $settings['panel_id'] !== ""){
        
    }else{
        return;
    }

    $app_id = '0e793dbc-2992-4192-9b80-7a9056dbce59';
    $api_key = 'os_v2_app_bz4t3pbjsjazfg4apkifnw6olearz772hhiuduumhgogmn2ty2j4m3swp5nd6buwqr2yvawffhia2eyy23bwlcfcdcjau5xsun5saca';

    $fields = [
        'app_id' => $app_id,
        'include_external_user_ids' => [$settings['panel_id']],
        'headings' => ['en' => $title],
        'contents' => ['en' => $message],
    ];

    $fields_json = json_encode($fields);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Basic ' . $api_key,
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_json);
    curl_setopt($ch, CURLOPT_POST, true);

    curl_exec($ch);
    curl_close($ch);
}

// ✅ Transaction Notification to Admin
function admin_app_push_notification_transaction_admin_ipn($transaction_id) {
    $transaction = pp_get_transation($transaction_id);
    $t = $transaction['response'][0];

    $status = ucfirst($t['transaction_status']);
    $amount = $t['transaction_amount'];
    $currency = $t['transaction_currency'];
    $name = $t['c_name'];
    $tx_id = $t['transaction_id'] ?? $transaction_id;

    $title = "Transaction $status";
    $message = "ID: $tx_id | Amount: $amount $currency from $name";

    send_admin_onesignal_notification($title, $message);
}

// ✅ Invoice Notification to Admin
function admin_app_push_notification_invoice_admin_ipn($invoice_id) {
    $invoice = pp_get_invoice($invoice_id);
    $i = $invoice['response'][0];

    $status = ucfirst($i['i_status']);

    $subtotal = 0;
    $totalVat = 0;
    $totalDiscount = 0;
    
    $response_items = pp_get_invoice_items($i['i_id']);
    foreach ($response_items['response'] as $item) {
        $quantity = isset($item['quantity']) ? floatval($item['quantity']) : 0;
        $amount = isset($item['amount']) ? floatval($item['amount']) : 0;
        $discount = isset($item['discount']) ? floatval($item['discount']) : 0;
        $vatPercentage = isset($item['vat']) ? floatval($item['vat']) : 0;
    
        $itemSubtotal = $quantity * $amount;
    
        $itemDiscount = min($discount, $itemSubtotal);
        $totalDiscount += $itemDiscount;

        $itemAmountAfterDiscount = $itemSubtotal - $itemDiscount;
        $itemVat = $itemAmountAfterDiscount * ($vatPercentage / 100);
        $totalVat += $itemVat;

        $subtotal += $itemSubtotal;
    }
    
    $shipping = pp_get_invoice($i['i_id']);
    $shipping = $shipping['response'][0]['i_amount_shipping'];

    $amount = $subtotal - $totalDiscount + $totalVat + $shipping;
    
    $currency = $i['i_currency'];
    $invoice_no = $i['i_id'];
    $name = $i['c_name'];

    $title = "Invoice $status";
    $message = "Invoice #$invoice_no | Amount: $amount $currency from $name";

    send_admin_onesignal_notification($title, $message);
}