<?php
namespace OtoPost;

class SettingsPage extends Core\AdminSubPage {
	protected $plugin;

	public function inject( $plugin ){
        $this->plugin = $plugin;
    }

	public function run(){
		parent::run();
		

		// add_action( 'media_buttons', function($editor_id){
		// 	if($editor_id=='content'){
		// 		echo '<button id="oto-generate-content" type="button" class="button">' . __( "Generate Content", "oto-post" ) . '</button>';
		// 		echo '<div class="oto-options-modal" id="oto-options">
        //         <input name="oto-keywords" type="text" value="">
        //         </div>';
		// 	}
		// } );

		// Remove metaboxes
        // add_action( 'admin_menu', array( $this, 'add_meta_boxes' ) );

		$plugin = $this->plugin;

		add_filter( 'plugin_action_links', array($this, 'action_links'), 10, 2);
	}

	public function action_links( $links, $file ) {
		if ( $this->plugin->get('slug') == $file ) {
			$links['settings'] = sprintf('<a href="%s">%s</a>', $this->plugin->get('adminUrl'), __('Settings', 'oto-post'));
		}
		return $links;
	}

	public function render_page(){
		$settings = $this->plugin->get('fetcher')->get_settings();
        // print_r($settings);
		$this->plugin->get('view')->render('settings.php', $settings);
	}

	/**
     * Add Meta Boxes
     *
     * Add custom metaboxes to our custom post type
     */
    public function add_meta_boxes(){
        
        add_meta_box(
            'oto-post-metabox',
            __('Oto Post', 'oto-post'),
            array( $this, 'render_oto_post_meta_box' ),
            'post' ,
            'side',
            'default'
        );
        
        
    }

	public function render_oto_post_meta_box( $post ){
        
        $vars = array();
        $vars['post'] = $post;
        if(empty($post->post_name)){
            $vars['shortcode'] = '..';
            $vars['template_code'] = '...';
        } else {
            $vars['shortcode'] = '[cycloneslider id="'.$post->post_name.'"]';
            $vars['template_code'] = '<?php if( function_exists(\'cyclone_slider\') ) cyclone_slider(\''.$post->post_name.'\'); ?>';
        }
        
        $this->plugin->get('view')->render('oto-post-metabox.php', $vars);

    }
}
