<?php

namespace OtoPost;

use Symfony\Component\DomCrawler\Crawler;
use Curly\Curl;

class EzineMarkFetcher {

    /**
    * Search 
    * @param string $keywords The keyword to search.
    * @param string $domain The grouping of article to search in. Values: 
    *   0 - All Articles
    *   business
    *   education
    *   lifestyle
    *   vehicle
    *   jobs
    *   leisure
    *   technology
    *   health
    *   society
    *   travel
    *   video
    * @return array Array of article URLs.
    */
    function search($keywords, $domain='0'){
        $curl = new Curl();
        $result = $curl->post('http://ezinemark.com/a/search.php',
            array(
                'domain' => $domain,
                'keyword' => trim($keywords)
            )
        );

        if($result->isError()){
            return array();
        }

        $crawler = new Crawler($result->result);
        $crawler = $crawler->filter('.dps a'); // TODO: Structure finding

        $pagesLinks = array();
        foreach ($crawler as $domElement) {
            $pagesLinks[] = $domElement->getAttribute('href');
        }

        $pagesLinks = array_filter($pagesLinks, function($value){
            return (trim($value)!=='#'); // Ignore href="#"
        });
        $links1 = $this->parseResultPage($result->result);
        $links2 = $this->wadeThruPages($pagesLinks);

        return array_merge($links1, $links2);
    }

    // Get the content of a single article
    public function getArticle($url){
        // Fetch url
        $curl = new Curl();
        $result = $curl->get($url);
        $structure = array();
        if($result->isOk()){
            $structure['title'] = $this->getTitle($result->result);
            $structure['content'] = $this->getContent($result->result);
        } else {
            var_dump($result->httpCode);
            var_dump($result->error);
        }
        return $structure;
    }

    function getContent($html){
        $out = '';
        $crawler = new Crawler($html);
        $crawler = $crawler->filter('#art_content'); // TODO: Structure finding
        foreach ($crawler as $domElement) {
            $out = $domElement->ownerDocument->saveHTML($domElement);
        }
        return strip_tags($out);
    }

    function getTitle($html){
        $out = '';
        $crawler = new Crawler($html);
        $crawler = $crawler->filter('.d_ctitle > h1'); // TODO: Structure finding
        foreach ($crawler as $domElement) {
            $out = $domElement->ownerDocument->saveHTML($domElement);
        }
        return $out;
    }
    // Get the rest of search results from other pages in pagination
    function wadeThruPages($pagesLinks){
        $curl = new Curl();
        
        $links = array();
        foreach($pagesLinks as $pageLink){
            $result = $curl->get($pageLink);
            if($result->isOk()){
                $links2 = $this->parseResultPage($result->result);
                $links = array_merge($links, $links2);
            }
        }

        return $links;
        
    }

    // Parse page and get urls
    function parseResultPage($result){
        $links = array();
        $crawler = new Crawler($result);
        $crawler = $crawler->filter('.l_search h3 a'); // TODO: Structure finding

        foreach ($crawler as $domElement) {
            $links[] = $domElement->getAttribute('href');
        }
        return $links;
    }
}