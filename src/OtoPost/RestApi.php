<?php
namespace OtoPost;

use OtoPost\Std\Collection;
use OtoPost\Std\DateTime;
use OtoPost\Wp\Post;
use OtoPost\Wp\Category;


class RestApi extends Core\AutoInject {
	
	// Run should only contain hooks and filters
	public function run( ){

		

		// $settings = $this->plugin->get('fetcher')->get_settings();
		// list($header, $msg, $isPosted) = $this->plugin->get('restApi')->doBigContent($settings['cron_key']);

		// Register the rest route here.
		add_action( 'rest_api_init', function () {
				
				// register_rest_route( 'oto-post/v1', 'post-now',array(
	
				// 	'methods'  => 'GET',
				// 	'callback' => array($this, 'routeBigContent')
	
				// ) );
 
				// register_rest_route( 'oto-post/v1', 'post-now-2',array(
	
				// 	'methods'  => 'GET',
				// 	'callback' => array($this, 'routeEzineMark')
	
				// ) );

		} );
		
    }

	public function routeEzineMark($request){

		
		if($request['key'] !== 'somekeys'){
			return array(
				'message'=>'fail'	
			);
		}
		
		$links = $this->plugin->get('ezineMarkFetcher')->search('anti aging skin cream for the new generation', 0);
        
        $blackList = array();

        // Randomize
        shuffle($links);

        // Get one link
        $link = array_pop($links);
        // echo sprintf('Chosen "%s".', $link)."\n";

        // Ideally add it to black list so not to use it again
        $blackList[] = $link;


        $structure = $this->plugin->get('ezineMarkFetcher')->getArticle($link);

		return array(
			'url' => $this->plugin->get('url'),
			'message'=>'done',
			'str' => $structure
		);
	}

	public function routeBigContent($request){
		
		list($header, $msg, $status) = $this->doBigContent($request['key']);
		header('Content-Type: text/plain');
		header($header);
		echo $msg;
		exit;
	}

	public function doBigContent($key) {

		// Load oto post settings
		$settings = $this->plugin->get('fetcher')->get_settings();


		if($key !== $settings['cron_key']){
			return array('HTTP/1.1 403 Forbidden', '', false);
		}
		
		// Check
		if(!$this->_isTimeToPost($settings)){
			$this->plugin->get('logger')->log('NOT time to post');
			return array('HTTP/1.1 200 Ok', 'NOT time to post', false);
		}

		
		// Random keyword. Returns set.
		$keywords = $this->plugin->get('bigContentFetcher')->keywordSelect($settings);
		if($keywords===false){
			return array('HTTP/1.1 400 Bad Request', 'Keyword error', false);
		}
		
		// Search content based on keywords
		$links = $this->plugin->get('bigContentFetcher')->search($keywords['keyword']);
		if(empty($links)){
			return array('HTTP/1.1 400 Bad Request', 'Search error', false);

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
			return array('HTTP/1.1 400 Bad Request', '', false);
		}
		$content = ltrim($content, '<div id="article-content">');
		$content = rtrim($content, '</div>');
		$content = str_replace('<br>', "\n", $content);

		// Spin content
		$spun = $this->plugin->get('bigContentFetcher')->spinText($content, $settings['spin_username'], $settings['spin_api']);
		if($spun === false){
			return array('HTTP/1.1 400 Bad Request', '', false);
		}
		
		// print_r($article);
		// print_r($spun);

		$title = $this->_extractTitleFromContent($spun);
		$spun = str_replace($title, '', $spun);
		$spun = strip_tags($spun);
		$dateTime = $this->plugin->get('dateTime');
		$dateTime = $dateTime::createNow();
		$dateTime->addHour(get_option( 'gmt_offset' ));

		// Add post to its set's categories
		$categoryNames = explode(',', $keywords['category']);
		$categoryIds = array();
		foreach($categoryNames as $categoryName){
			if($categoryName!=''){
				// If it doesnt exist, add it and get ID
				$categoryId = Category::isExist($categoryName);
				if($categoryId===false){
					$categoryId = Category::create(array(
						'name' => $categoryName
					));
				}
				$categoryIds[] = $categoryId;
			}
		}
		
		$postId = Post::create(array(
			'title' => $title,
            'content' => $spun,
            'date' => $dateTime->getStringTime(),
			'category' => $categoryIds
		));
		
		wp_set_post_tags( $postId, $keywords['tag']);

		return array('HTTP/1.1 200 Ok', sprintf('Article %s posted.', $postId), true);
		
	}

	// TODO: Use all categories
	protected function _randomCategory($catString){
		$catArray = explode(',', $catString);
		$catArray = Collection::shuffle($catArray);
		return trim(Collection::first($catArray));
	}
	
	protected function _extractTitleFromContent($content){
		$needle = "\n";
		$pos = strpos($content, $needle);
		if($pos!==false){
			return strip_tags(substr($content, 0 , $pos));
		}
		return '';
	}
	
	protected function _isTimeToPost($settings){
		$dateTime1 = $this->_getLastPostDate(); // Last post date. If no post, its 0.
		// Now time with offset
		$dateTime2 = $this->plugin->get('dateTime');
		$dateTime2 = $dateTime2::createNow();

		$gmtOffset = get_option( 'gmt_offset' );
		$dateTime2->addHour($gmtOffset);

		$randomHours = $this->_randomHour($settings['post_interval_min'], $settings['post_interval_max']); // Generate a random hour in this range

		$diff = $dateTime2->getUnixTime() - $dateTime1->getUnixTime();
		if($diff > $randomHours){
			return true;
		} 
		return false;
	}

	/**
	* Get last post date
	* If not post, return time 0
	*/
	protected function _getLastPostDate(){
		// Get last post date
		$posts = $this->plugin->get('post')->getAll();
		if(empty($posts)){
			$class = $this->plugin->get('dateTime');
			return new $class(0);
		}
		$post = Collection::first($posts);
		$dateTimeCreateFromString = $this->plugin->get('dateTimeCreateFromString');
		return $dateTimeCreateFromString($post['post_date']);
	}

	/**
	* Get random hour ranging from $hrMin to $hrMax and pad it with random minutes and seconds
	* 
	* @param int $hrMin Hours in 0 - 23
	* @param int $hrMax Hours in 0 - 23
	* @return int In seconds
	*/
	protected function _randomHour($hrMin, $hrMax){
		if($hrMin < 0) $hrMin = 0;
		if($hrMax > 23) $hrMin = 23;
		
		$hour = rand($hrMin, $hrMax);
		$minute = rand(0, 59);
		$second = rand(0, 59);

		return ($hour * 3600) + ($minute * 60) + $second; // Return in seconds
	}
}