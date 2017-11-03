<?php 

namespace OtoPost\Wp;

class Category {
    /**
    * @return int|bool Category ID or false if non-existent. 
    */
    public static function isExist($id){
        $term = term_exists($id, 'category');
		if ($term === 0 || $term === null ) {
			return false;
		} 

        $id = $term;
        if(is_array($term)){
            $id = $term['term_id'];
        }
        return $id;
    }

    /**
    * @return int|bool Category ID or false on fail. 
    */
    public static function create($props){
        $defaults = array(
            'name' => '',
            'description' => '',
            'slug' => '',
            'parent' => ''
        );
        $props = array_merge($defaults, $props);
        $cat = array(
            'cat_name' => $props['name'], 
            'category_description' => $props['description'], 
            'category_nicename' => $props['slug'], 
            'category_parent' => $props['parent']
        );

        return wp_insert_category($cat);
    }
}