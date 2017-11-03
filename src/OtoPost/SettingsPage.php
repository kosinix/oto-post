<?php
namespace OtoPost;

class SettingsPage extends Core\AdminSubPage {
	protected $plugin;

	public function inject( $plugin ){
        $this->plugin = $plugin;
    }

	public function run(){
		parent::run();
        
		

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

	
}
