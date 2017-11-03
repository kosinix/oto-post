<?php 

namespace OtoPost\Wp;

class Post {
    /**
    * @return int|bool Post ID on success or false on fail. 
    */
    public static function create($props){
        $defaults = array(
            'title' => '',
            'content' => '',
            'status' => 'publish',
            'date' => '',
			'category' => array()
        );
        $props = array_merge($defaults, $props);
        $item = array(
            'ID' => 0,
			'post_title' => $props['title'],
			'post_type' => 'post',
			'post_status' => $props['status'],
			'post_content' => $props['content'],
			'post_date' => $props['date'],
			'post_category' => $props['category']
        );

        $id = wp_insert_post($item);
		if($id===0){
			return false;
		}
		return $id;
    }
}