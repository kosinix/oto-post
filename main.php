<?php
/*
Plugin Name: Oto Post
Plugin URI: #
Description: Create post from thin air and publish it automatically.
Version: 1.1.0
Author: 
License: GPLv2
Domain Path: /languages
Text Domain: oto-post
*/

require_once __DIR__.'/src/autoload.php';

register_activation_hook(__FILE__, function(){
    $settings = get_option('oto_post_settings', array());
    if(!isset($settings['cron_key'])){
        $settings['cron_key'] = '';
    }
    if(trim($settings['cron_key'])===''){
        $settings['cron_key'] = \OtoPost\Std\Random::string(24);
        update_option('oto_post_settings', $settings);
    }
});
