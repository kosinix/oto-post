<?php

namespace OtoPost;
/**
* Class for handling styles and scripts
*/
class AssetLoader extends Core\AutoInject{
	public function run() {
        // Front scripts
        add_action( 'wp_enqueue_scripts', array($this, 'wp_enqueue_scripts') , 10);

		// Admin scripts
		add_action( 'admin_enqueue_scripts', array($this, 'admin_enqueue_scripts') , 10);


	}
    public function wp_enqueue_scripts( $hook ) {
            
        wp_register_script( 'oto-post-trigger', $this->plugin->get('url').'js/trigger.js', array('jquery'), $this->plugin->get('version') );
        wp_localize_script( 'oto-post-trigger', 'oto_post_vars',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'oto_post_ajax' )
            )
        );
        wp_enqueue_script( 'oto-post-trigger' );

    }

    public function admin_enqueue_scripts( $hook ) {
        if( $hook == 'settings_page_oto-post' or $hook == 'post-new.php' or $hook == 'post.php'){ // Limit loading to certain admin pages
            
            wp_enqueue_style( 'oto-post-style', $this->plugin->get('url').'css/style.css', array(), $this->version  );
            
        }

        if( $hook == 'settings_page_oto-post'){

            // Allow translation to script texts
            wp_register_script( 'oto-post-script', $this->plugin->get('url').'js/script.js', array('jquery'), $this->plugin->get('version') );
            wp_localize_script( 'oto-post-script', 'oto_post_vars',
                array(
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'nonce' => wp_create_nonce( 'oto_post_ajax' )
                )
            );
            wp_enqueue_script( 'oto-post-script' );
        }
        if($hook == 'post-new.php' or $hook == 'post.php'){
            wp_register_script( 'oto-post-generate', $this->plugin->get('url').'js/generate.js', array('jquery'), $this->plugin->get('version') );
            wp_localize_script( 'oto-post-generate', 'oto_post_vars',
                array(
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'nonce' => wp_create_nonce( 'oto_post_ajax' )
                )
            );
            wp_enqueue_script( 'oto-post-generate' );
        }
    }
}