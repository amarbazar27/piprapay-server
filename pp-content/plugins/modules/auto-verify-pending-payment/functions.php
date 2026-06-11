<?php
    if (!defined('pp_allowed_access')) {
        die('Direct access not allowed');
    }

    // Register the hook on plugin load
    add_action('pp_cron', 'pp_auto_verify_pending_payment');
    
    function pp_auto_verify_pending_payment() {
        global $conn;
    
        $transaction = pp_get_transation(null, 'WHERE transaction_status="pending"');
        
        foreach($transaction['response'] as $tran){
            $verify_status = pp_verify_transaction($tran['pp_id'], $tran['payment_method_id'], '--', $tran['payment_verify_id']);
            
            if($verify_status['status'] == true){
                pp_set_transaction_byid($tran['pp_id'], $tran['payment_method_id'], $tran['payment_method'], $tran['payment_sender_number'], $tran['payment_verify_id'], 'completed', $verify_status['response'][0]['id']);
            }
        }
    }
?>