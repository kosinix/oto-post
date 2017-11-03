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
                array('keyword'=>'', 'category'=>'', 'tag'=>'', 'tag_image'=>'')   
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

    public function update_blacklist($link){
        $settings = $this->get_settings();
		$blackList = explode("\n", $settings['black_list']);
		if(!in_array($link, $blackList)){
			$blackList[] = $link;
		}
		$blackList = implode("\n", $blackList);
		$settings['black_list'] = $blackList;
		update_option('oto_post_settings', $settings);
    }
	
}
