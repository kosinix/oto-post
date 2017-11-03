<?php

namespace OtoPost;

define('OTO_POST_PHP', '5.3');


// Make sure we are on supported PHP version
add_action( 'admin_init', function() {

    if ( version_compare(PHP_VERSION, OTO_POST_PHP, '<') && is_admin() && current_user_can( 'activate_plugins' ) &&  is_plugin_active( 'oto-post/main.php' ) ) {
        add_action( 'admin_notices', function (){
            ?><div class="error notice-error">
            <p><?php echo sprintf(__('Oto Post deactivated. PHP version must be %s and above. This server has PHP %s', 'oto-post'), OTO_POST_PHP, PHP_VERSION); ?></p>
            </div><?php
        });

        deactivate_plugins( 'oto-post/main.php' ); 

        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }

    
});



// Lets begin
$otoPostInstance = null;

add_action('plugins_loaded', function() {
    global $otoPostInstance;
    
    $plugin = new Core\Servicer();

    $plugin->set('path', realpath(plugin_dir_path(dirname(__FILE__))) . DIRECTORY_SEPARATOR);
    $plugin->set('url', plugin_dir_url(dirname(__FILE__)));
    $plugin->set('mainFile', 'main.php');
    $plugin->set('adminUrl', get_admin_url(null, 'options-general.php?page=oto-post'));
    $plugin->set('viewFolder', $plugin->get('path').'views'.DIRECTORY_SEPARATOR);
    $plugin->set('languagePath', basename($plugin->get('path')).'/languages/');
    $plugin->set('debug', true);
    $plugin->set('nonceName', 'footer_editor_nonce');
    $plugin->set('nonceAction', 'footer_editor_action');
    $plugin->set('slug', function ( $plugin ) {
        return basename($plugin->get('path')).'/'.$plugin->get('mainFile');
    });
    $plugin->set('pluginHeaders', function ( $plugin ){

        $default_headers = array(
            'name' => 'Plugin Name',
            'plugin_uri' => 'Plugin URI',
            'version' => 'Version',
            'author' => 'Author',
            'author_uri' => 'Author URI',
            'license' => 'License',
            'license_uri' => 'License URI',
            'domain_path' => 'Domain Path',
            'text_domain' => 'Text Domain'
        );
        return get_file_data( $plugin->get('path').DIRECTORY_SEPARATOR. $plugin->get('mainFile'), $default_headers, 'plugin' ); // WP Func
    });
    $plugin->set('version', function ( $plugin ){
        $h = $plugin->get('pluginHeaders');
        return $h['version'];
    });
    $plugin->set('textdomain', function ( $plugin ){
        $h = $plugin->get('pluginHeaders');
        return $h['text_domain'];
    });
    $plugin->set('view', function($plugin){
        return new Core\View($plugin->get('viewFolder'));
    });
    $plugin->set('assetLoader', function(){
        return new AssetLoader();
    });

    $plugin->set('fetcher', function(){
        return new Fetcher();
    });

    $plugin->set('ajax', function(){
        return new Ajax();
    });
    $plugin->set('settingsPage', function(){
        return new SettingsPage(
            'options-general.php', 'Oto Post', 'Oto Post', 'manage_options', 'oto-post'
        );
    });
    

    $plugin->set('ezineMarkFetcher', function(){
        return new EzineMarkFetcher();
    });

    $plugin->set('bigContentFetcher', function(){
        return new BigContentFetcher('seandrayton@gmail.com', '4JFPkeYI');
    });

    $plugin->set('logger', function($plugin){
        $transports = array(
            // new Log\Transport\Out(),
            new Log\Transport\File($plugin->get('path').'logs/log.txt')
        );
        return new Log\Logger($transports);
    });

    $plugin->set('post', function($plugin){
        return new Core\Post();
    });

    $plugin->set('dateTime', function($plugin){
        return '\OtoPost\Std\DateTime';
    });

    $plugin->set('dateTimeCreateFromString', function($plugin){
        return function($stringTime) use($plugin){
            $dateTime = $plugin->get('dateTime');
            return $dateTime::createFromString($stringTime);
        };
    });

    $plugin->set('restApi', function(){
        return new RestApi();
    });

    $plugin->set('postPage', function(){
        return new PostPage();
    });
    
    $plugin->run();
   
    $otoPostInstance = $plugin;

    
});
