<?php

namespace OpenAI\GPT3\Library;

use Symfony\Component\HttpClient\HttpClient;

class Api {

    /* HTTP Client */
    protected $client;

    /* Configs */
    protected $config;

    protected $headers = array();

    public function __construct($configs = false) {

        if($configs && is_array($configs)) {

            foreach($configs as $config => $value) {
                $this->config[$config] = $value;
            }
        }

        $this->client = HttpClient::create();
    }

    public function __get( $key )
    {
        return $this->config[$key];
    }

    public function __set($key, $value)
    {
        $this->config[$key] = $value;
    }

    public function configs() {
        return $this;
    }

    public function getApiUrl($path)
    {
        return 'https://' . ($this->debug ? 'sandbox.' : 'api.') . 'openai.com/' . $this->api_version . $path;
    }

    public function auth() {

        $this->addHeader("Authorization", "Bearer " . $this->api_key);

        if($this->organization) {
            $this->addHeader("OpenAI-Organization", $this->organization);
        }
        
        return $this;
    }

    public function post($path, $instructions) {

        $this->auth();
        
        $response = $this->client->request('POST', $this->getApiUrl($path), [
            'headers' => $this->headers,
            'json' => $instructions
        ]);
        
        return $response;
    }

    public function get($path) {

        $this->auth();

        $response = $this->client->request('GET', $this->getApiUrl($path), [
            'headers' => $this->headers
        ]);
        
        return $response;
    }

    public function addHeader($header, $value) {
        
        $this->headers[$header] = $value;
        return $this;
    }

    public function removeHeader($header) {

        unset($this->headers[$header]);
        return $this;
    }

}