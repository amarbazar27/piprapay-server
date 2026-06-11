<?php
    if (file_exists(__DIR__."/../pp-config.php")) {
        if (file_exists(__DIR__.'/../maintenance.lock')) {
            if (file_exists(__DIR__.'/../pp-include/pp-maintenance.php')) {

            }else{
                die('System is under maintenance. Please try again later.');
            }
            exit();
        }else{
            if (file_exists(__DIR__.'/../pp-include/pp-controller.php')) {
                if (file_exists(__DIR__.'/../pp-include/pp-view.php')) {
    
                }else{
                    echo 'System is under maintenance. Please try again later.';
                    exit();
                }
            }else{
                echo 'System is under maintenance. Please try again later.';
                exit();
            }
            
            if (file_exists(__DIR__.'/../pp-include/pp-model.php')) {
                include(__DIR__."/../pp-include/pp-model.php");
            }else{
                echo 'System is under maintenance. Please try again later.';
                exit();
            }
        }
    }else{
        echo 'System is under maintenance. Please try again later.';
        exit();
    }
    
    if (!defined('pp_allowed_access')) {
        die('Direct access not allowed');
    }

    if(isset($global_user_login) && $global_user_login == true){
        if(isset($_POST['webpage'])){
            $webpage = escape_string($_POST['webpage']);
    
            if($webpage == ""){
                echo json_encode(['status' => "false", 'message' => 'Something Wrong!']);
            }else{
                if (file_exists(__DIR__.'/../pp-include/pp-resource/'.$webpage.'.php')) {
                    include(__DIR__.'/../pp-include/pp-resource/'.$webpage.'.php');
                }else{
                    echo 'System is under maintenance. Please try again later.';
                    exit();
                }
            }
            exit();
        }
    }
?>