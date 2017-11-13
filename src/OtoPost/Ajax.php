<?php

namespace OtoPost;

use OtoPost\Std\Collection;

class Ajax extends Core\AutoInject {
    function run(){
        $post = $_POST;

        // Auto post handler
        // Trigger auto posting on frontend when logged out
        add_action( 'wp_ajax_nopriv_oto_post_now', array($this, 'trigger_post_now'));
        // On admin
        add_action( 'wp_ajax_oto_post_now', array($this, 'trigger_post_now'));

        // Generate content handler
        add_action( 'wp_ajax_oto_post_generate', array($this, 'generate_content')); // Generate content

        // Save
        add_action( 'wp_ajax_oto_post_save', array($this, 'wp_ajax_oto_post_save'));

        // Restore
        add_action( 'wp_ajax_oto_post_restore', array($this, 'wp_ajax_oto_post_restore'));
    }
    
    // Ajax triggered auto post
    public function trigger_post_now(){
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

    // Generate content for the Post screen
    function generate_content(){
        $post = $_POST;
        
        $this->_nonce_check($post);

        // Load oto post settings
        $settings = $this->plugin->get('fetcher')->get_settings();

        // Random keyword. Returns set.
        $keywordSet = $this->plugin->get('bigContentFetcher')->keywordSelect($settings);
        if($keywordSet===false){
            wp_send_json(__('Keyword error', 'oto-post' ), 400);
		}
        
        // Search content based on keywords
        $links = $this->plugin->get('bigContentFetcher')->search($keywordSet['keyword']);
		if(empty($links)){
            wp_send_json(__('Search error', 'oto-post' ), 400);
		}

        // Get first link
		$link = Collection::first($links);

		// Load black list to compare against link
		$blackList = explode("\n", $settings['black_list']);
		$blackList = Collection::filter($blackList, function($value){
			return trim($value);
		});

		// Rotate links if its on black list
		$counter = 0;
		while(in_array($link, $blackList)){
			
			$links = Collection::shuffle($links);
			$link = Collection::first($links);
			
			if($counter++>100) {
				break;
			}
		}
		$this->plugin->get('fetcher')->update_blacklist($link);

		// Get article title and content
		$content = $this->plugin->get('bigContentFetcher')->getArticle($link);
		if($content===false){
			wp_send_json(__('Article fetch error', 'oto-post' ), 400);
		}
		$content = ltrim($content, '<div id="article-content">');
		$content = rtrim($content, '</div>');
		$content = str_replace('<br>', "\n", $content);

        $title = $this->_extractTitleFromContent($content);
        $content = strip_tags($content);


        wp_send_json(
            array(
                'message' => __('AJAX', 'oto-post' ),
                'data' => array(
                    'links' => $links,
                    'link' => $link,
                    'keywords' => $keywordSet['keyword'],
                    'title' => $title,
                    'content' => $content,
                )
            )
        );
    }

    protected function _extractTitleFromContent($content){
		$needle = "\n";
		$pos = strpos($content, $needle);
		if($pos!==false){
			return strip_tags(substr($content, 0 , $pos));
		}
		return '';
	}

    protected function _nonce_check($post){
        if ( false === wp_verify_nonce( $post['nonce'], 'oto_post_ajax' ) ) {
            wp_send_json(__('Wrong NONCE value.', 'oto-post'), 400);
        }
    }
}