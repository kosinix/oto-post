<?php 

namespace OtoPost\Core;

class Post {
    /**
    * Get all post with any status except auto-draft
    *
    * @return array Returns an array of sliders
    */
    public function getAll( $args=array() ){
        $defaults = array(
            'post_type' => 'post',
            'post_status' => array('any'), // As long as it exist, get it
			'numberposts' => -1 // Get all
        );
        $args = wp_parse_args($args, $defaults);
        
        return $this->getPosts( $args );
    }

    /**
	 * Wrapper for WP get_posts.
	 *
	 * @param array $args The same as WP get_posts
	 *
	 * @return array An assoc array of posts or empty array
	 */
	public function getPosts( array $args ) {
		$posts   = get_posts( $args ); // Returns array
		$results = array(); // Store it here
		if ( ! empty( $posts ) and is_array( $posts ) ) {
			foreach ( $posts as $index => $post ) {
				$results[ $index ] = (array) $post; // Obj to assoc array
			}
		}
		return $results;
	}
}