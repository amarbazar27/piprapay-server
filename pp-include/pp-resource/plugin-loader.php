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
        $page = $_GET['page'] ?? '';
        if (!$page) {
            exit("Missing plugin page.");
        }
        
        list($mainFolder, $pluginFolder) = explode('--', $page);
        if (!preg_match('/^[a-zA-Z0-9-_]+$/', $mainFolder) || !preg_match('/^[a-zA-Z0-9-_]+$/', $pluginFolder)) {
            exit("Invalid plugin path.");
        }
        
        $response_plugin = json_decode(getData($db_prefix.'plugins', 'WHERE plugin_slug="'.$pluginFolder.'" AND status="active"'), true);
        if($response_plugin['status'] == true){
            
        }else{
            exit("Invalid plugin path.");
        }
        
        $pluginPath = __DIR__ . "/../../pp-content/plugins/{$mainFolder}/{$pluginFolder}";
        $classFile = "{$pluginPath}/{$pluginFolder}-class.php";
        
        if (!file_exists($classFile)) {
            exit("Plugin main file not found.");
        }
        
        require_once $classFile;
        
        // Expected admin function: plugin-folder name + _admin_page
        $function = str_replace('-', '_', $pluginFolder) . '_admin_page';
        if (function_exists($function)) {
            call_user_func($function);
        } else {
            echo "Admin page function not found in plugin.";
        }
    }
?>