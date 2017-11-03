<?php 
namespace OtoPost\Curly;

class Curl {
    protected $curl;
    public $options;
    public function __construct($options = array()){
        $defaults = array(
            'CURLOPT_HEADER' => false,
            'CURLOPT_HTTPHEADER' => array(), // Array of string eg. array('Content-Type: text/html', 'Authorization: Basic c2VhbmRyYXl0b25AZ21haWwuY29tOjRKRlBrZVlJ')
            'CURLOPT_USERPWD' => '', // Eg. 'someone@gmail.com:password'

            'CURLOPT_RETURNTRANSFER' => true,
            'CURLOPT_FOLLOWLOCATION' => true,
            'CURLOPT_SSL_VERIFYPEER' => false, // Insecure SSL
            'CURLOPT_USERAGENT' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13',
        );
        $options = array_merge($defaults, $options);

        $this->curl = curl_init();
        
        $this->reset($options);

        $this->options = $options;
        
    }
    public function reset($options){
        curl_reset($this->curl);

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER , $options['CURLOPT_RETURNTRANSFER']);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, $options['CURLOPT_FOLLOWLOCATION']);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, $options['CURLOPT_SSL_VERIFYPEER']);
        curl_setopt($this->curl, CURLOPT_USERAGENT, $options['CURLOPT_USERAGENT']);

        if($options['CURLOPT_HEADER']){
            curl_setopt($this->curl, CURLOPT_HEADER, true);
        }
        if(!empty($options['CURLOPT_HTTPHEADER'])){
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $options['CURLOPT_HTTPHEADER']);
        }
        if($options['CURLOPT_USERPWD']){
            curl_setopt($this->curl, CURLOPT_USERPWD, $options['CURLOPT_USERPWD']);
        }

    }

    public function get($url){

        $this->reset($this->options); // Prevent post and get conflicts

        curl_setopt($this->curl, CURLOPT_URL, $url);

        return new CurlResult(
            curl_exec($this->curl),
            curl_getinfo($this->curl, CURLINFO_HTTP_CODE),
            curl_error($this->curl)
        );
        
    }

    function post($url, $fields=array()){
        
        $this->reset($this->options); // Prevent post and get conflicts
        
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($fields));

        return new CurlResult(
            curl_exec($this->curl),
            curl_getinfo($this->curl, CURLINFO_HTTP_CODE),
            curl_error($this->curl)
        );
    }

    public function close(){
        curl_close($this->curl); // Consume this instance
    }

}