<?php
namespace OtoPost;

/**
* Simple class for fetching db data
*/
class Fetcher extends Core\AutoInject {
	
	public function get_settings(){
		$settings = get_option('oto_post_settings', array());
		return array_merge($this->defaults(), $settings);
	}

	public function defaults(){
		return array(
            'keywords' => array(
                array('keyword'=>'', 'category'=>'', 'tag'=>'')   
            ),
            'post_interval_min' => 168, // hours
            'post_interval_max' => 169,
            'bc_username' => '',
            'bc_password' => '',
			'spin_username' => '',
            'spin_api' => '',
            'black_list' => '',
            'cron_key' => '',
        );
	}

	
}
