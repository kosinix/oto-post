<?php 

namespace OtoPost;

use Symfony\Component\DomCrawler\Crawler;
use OtoPost\Curly\Curl;
use OtoPost\Std\Collection;

class BigContentFetcher extends Core\AutoInject {

    protected $username;
    protected $password;
    public function __construct($username, $password){
        $this->username = $username;
        $this->password = $password;
    }

    function keywordSelect($settings){
		// Get keyword from settings
		if(!isset($settings['keywords'])){
			$this->plugin->get('logger')->log('Error: !isset $settings[keywords]');
			return false;
		}

		$keywords = Collection::shuffle($settings['keywords']);
		$set = Collection::first($keywords); 

        if(''==trim($set['keyword'])){
            $this->plugin->get('logger')->log('Error: $keywords empty');
			return false;
        }
		return $set;
	}

    function search($keywords){

        $userPass = sprintf('%s:%s', $this->username, $this->password);
        $options['CURLOPT_HEADER'] = true;
        $options['CURLOPT_HTTPHEADER'] = array(
            'Content-Type: text/html', 
            'Authorization: Basic '.base64_encode($userPass)
        );
        // $options['CURLOPT_USERPWD'] = sprintf('%s:%s', $this->username, $this->password);
        $curl = new Curl($options);
        $result = $curl->get('https://members.bigcontentsearch.com/articles/@@search?SearchableText='.urlencode($keywords));
        // $result = new Curly\CurlResult(file_get_contents('D:\webserver\htdocs\samplecontent.html'), 200);

        if(!$result->isOk()){
            $this->plugin->get('logger')->log(sprintf('Curl HTTP code "%s"', $result->httpCode));
            $this->plugin->get('logger')->log(sprintf('Curl error "%s"', $result->error));
            return array();
        }

        $links = $this->scrapeResultLinks($result->result);

        return $links;
    }

    protected function scrapeResultLinks($content){
        $crawler = new Crawler($content);
        $crawler = $crawler->filter('a.article-title');

        $links = array();
        foreach ($crawler as $domElement) {
            $links[] = $domElement->getAttribute('href');
        }

        $links = array_filter($links, function($value){
            return (trim($value)!=='#'); // Ignore href="#"
        });
        return $links;
    }

    // Get the content of a single article
    public function getArticle($url){
        		
        $this->plugin->get('logger')->log(sprintf('Fetching url "%s"', $url));

        $userPass = sprintf('%s:%s', $this->username, $this->password);
        $options['CURLOPT_HEADER'] = true;
        $options['CURLOPT_HTTPHEADER'] = array(
            'Content-Type: text/html', 
            'Authorization: Basic '.base64_encode($userPass)
        );
        // $options['CURLOPT_USERPWD'] = sprintf('%s:%s', $this->username, $this->password);
        $curl = new Curl($options);
        $result = $curl->get($url);
        if(!$result->isOk()){
            $this->plugin->get('logger')->log(sprintf('Curl HTTP code "%s"', $result->httpCode));
            $this->plugin->get('logger')->log(sprintf('Curl error "%s"', $result->error));
            return false;
        }
        return $this->getContent($result->result);
    }

    function getContent($html){
        $out = '';
        $crawler = new Crawler($html);
        $crawler = $crawler->filter('#article-content'); // TODO: Structure finding
        foreach ($crawler as $domElement) {
            $out = $domElement->ownerDocument->saveHTML($domElement);
        }
        return ($out);
    }

    function getTitle($html){
        $out = '';
        $crawler = new Crawler($html);
        $crawler = $crawler->filter('#parent-fieldname-title'); // TODO: Structure finding
        foreach ($crawler as $domElement) {
            $out = $domElement->ownerDocument->saveHTML($domElement);
        }
        return strip_tags($out);
    }

    function spinText($text, $email, $key){
        $curl = new Curl();
        $result = $curl->post('http://www.spinrewriter.com/action/api', array(
            'email_address' => $email,
            'api_key' => $key,
            'action' => 'unique_variation',
            'text' => $text
        ));
        if(!$result->isOk()){
            $this->plugin->get('logger')->log('Spin text...');
            $this->plugin->get('logger')->log(sprintf('Curl HTTP code "%s"', $result->httpCode));
            $this->plugin->get('logger')->log(sprintf('Curl error "%s"', $result->error));

            return false;
        } 
        $json = json_decode($result->result, true);
        return $json['response'];
    }
}