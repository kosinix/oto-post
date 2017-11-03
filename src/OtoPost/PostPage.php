<?php
namespace OtoPost;

class PostPage extends Core\AutoInject {

	public function run(){
		
        // Custom button in post editor
		add_action( 'media_buttons', array( $this, 'media_button' ) );

		// Add hook for admin footer
        add_action('admin_footer', array( $this, 'admin_footer') );

        // Remove metaboxes
        // add_action( 'admin_menu', array( $this, 'add_meta_boxes' ) );

	}
	
	public function media_button($editor_id){
		if($editor_id=='content'){
            $settings = $this->plugin->get('fetcher')->get_settings();
            
            echo '<button id="oto-generate-content" type="button" class="button">' . __( "Generate Content", "oto-post" ) . '</button>';
            // echo '<div class="oto-options-modal" id="oto-options">
            // <input name="oto-keywords" type="text" value="">'.print_r($settings,1).'
            // </div>';
        }
	}

    public function admin_footer(){
        
        $this->plugin->get('view')->render('oto-post-modal.php');
        
        
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
