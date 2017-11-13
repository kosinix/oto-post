<?php 

namespace OtoPost\Wp;

class Tag {
    /**
    * @return int|bool Category ID or false if non-existent. 
    */
    public static function isExist($id){
        $term = term_exists($id, 'post_tag');
		if ($term === 0 || $term === null ) {
			return false;
		} 

        $id = $term;
        if(is_array($term)){
            $id = $term['term_id'];
        }
        return $id;
    }

    
}