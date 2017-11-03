<?php

namespace OtoPost;

class Ajax extends Core\AutoInject {
    function run(){
        $post = $_POST;

        // Trigger auto posting on frontend when logged out
        add_action( 'wp_ajax_nopriv_oto_post_now', array($this, 'trigger'));
        
        // On admin
        add_action( 'wp_ajax_oto_post_now', array($this, 'trigger'));

        // Save
        add_action( 'wp_ajax_oto_post_save', array($this, 'wp_ajax_oto_post_save'));

        // Restore
        add_action( 'wp_ajax_oto_post_restore', array($this, 'wp_ajax_oto_post_restore'));
    }
    protected function _processPost($post){
        $content = $post['content'];
        parse_str($content, $data);
        $data = array_merge($this->plugin->get('fetcher')->defaults(), $data);
        update_option('oto_post_settings', $data);
        return $data;
    }

    
    protected function _nonce_check($post){
        if ( false === wp_verify_nonce( $post['nonce'], 'oto_post_ajax' ) ) {
            wp_send_json(__('Wrong NONCE value.', 'oto-post'), 400);
        }
    }

    public function trigger(){
        $post = $_POST;
        $this->_nonce_check($post);

        $settings = $this->plugin->get('fetcher')->get_settings();
		list($header, $msg, $isPosted) = $this->plugin->get('restApi')->doBigContent($settings['cron_key']);

        wp_send_json(
            array(
                'header' => $header,
                'message' => $msg,
                'isPosted' => $isPosted,
            )
        );
    }

    public function wp_ajax_oto_post_save(){
        $post = $_POST;
        
        $this->_nonce_check($post);

        if(!isset($post['fields'])){

            wp_send_json(__('Missing form fields.', 'oto-post'), 400);
        }
        // wp_send_json(__('Test ajax error.', 'oto-post'), 400);
        // throw new \Exception('Test server error');
        
        // Do save 
        $data = array();
        $fields = $post['fields'];
        $counter = 0;
        $keywords = array();
        foreach($fields as $index=>$field){
            if($field['name'] === 'keyword'){
                $keywords[$counter]['keyword'] = $field['value'];
                unset($fields[$index]);
            }
            if($field['name'] === 'category'){
                $keywords[$counter]['category'] = $field['value'];
                unset($fields[$index]);
                
            }
            if($field['name'] === 'tag'){
                $keywords[$counter]['tag'] = $field['value'];
                unset($fields[$index]);
                
                $counter++;
            }
            if($field['name'] !== 'keyword' && $field['name'] !== 'category' && $field['name'] !== 'tag'){
                $save[$field['name']] = $field['value'];
            }
        }
        $keywords = array_filter($keywords, function($value){
            return trim($value['keyword']);
        });
        $save['keywords'] = $keywords;
        $fields = array_merge($this->plugin->get('fetcher')->defaults(), $save);
        update_option('oto_post_settings', $fields);

        
        // if($result===false){
        //     wp_send_json(__('Save fail.', 'oto-post'), 400);
        // }

        
        $result = print_r($data, 1);
        
        wp_send_json(
            array(
                'message' => __('Changes saved', 'oto-post' ),
                'data' => $result
            )
        );
        
    }

    function wp_ajax_oto_post_restore() {
        $post = $_POST;
        $this->_nonce_check($post);

        
        // wp_send_json(__('Test ajax error.', 'oto-post'), 400);
        // throw new \Exception('Test server error');
        
        delete_option('oto_post_settings');
        
        wp_send_json(
            array(
                'message' => __('Changes saved', 'oto-post' ),
                'data' => $result
            )
        );
        
    }
}