<?php
    if (file_exists(__DIR__."/../../pp-config.php")) {
        if (file_exists(__DIR__.'/../../maintenance.lock')) {
            if (file_exists(__DIR__.'/../../pp-include/pp-maintenance.php')) {

            }else{
                die('System is under maintenance. Please try again later.');
            }
            exit();
        }else{
            if (file_exists(__DIR__.'/../../pp-include/pp-controller.php')) {
                if (file_exists(__DIR__.'/../../pp-include/pp-view.php')) {
    
                }else{
                    echo 'System is under maintenance. Please try again later.';
                    exit();
                }
            }else{
                echo 'System is under maintenance. Please try again later.';
                exit();
            }
            
            if (file_exists(__DIR__.'/../../pp-include/pp-model.php')) {
                include(__DIR__."/../../pp-include/pp-model.php");
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
        $themeFolder = $global_setting_response['response'][0]['gateway_theme'];
        
        $themePath = __DIR__ . "/../../pp-content/themes/gateway/".$themeFolder;
        $classFile = "{$themePath}/{$themeFolder}-class.php";
        
        if (!file_exists($classFile)) {
            exit("Theme main file not found.");
        }
        
        require_once $classFile;
        
        // Expected admin function: plugin-folder name + _admin_page
        $function = str_replace('-', '_', $themeFolder) . '_admin_page';
        if (function_exists($function)) {
            call_user_func($function);
        } else {
            echo "Customization feature not available in your current theme.";
        }
    }
?>