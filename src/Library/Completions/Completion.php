<?php

namespace OpenAI\GPT3\Library\Completions;

use Symfony\Component\HttpClient\HttpClient;
use OpenAI\GPT3\Library\Api;
use Contao\Config;
use Contao\Session;


class Completion {

    /* Api */
    protected $api;

    /* prompt */
    public $content;

    public function __construct() {

        $this->api = new Api(Config::get('openai'));
    }

    public function __set($key, $value)
    {
        $this->{$key} = $value;
    }

    public function chat($instructions, $callback)
    {
        if(is_null($instructions)) {
            $callback("This call was aborted because there were no instructions to send to our assistant");
        }
        
        $response = $this->api->post('/chat/completions', $instructions);
        
        $status_code = $response->getStatusCode();

        $objSession = Session::getInstance();

        $chat = !empty($instructions['chat']) ? $instructions['chat'] : [];
        
        if($status_code == 200) {
            $this->content = json_decode($response->getContent());
            $choices = $this->content->choices[0];
            // dump($chat);
            $objSession->set('chat', array_merge(
                $chat, 
                [time().'=>'.$choices->message->role."=>".$this->content->id => $choices->message->content]
            ));
            
            $callback();
        } else {
            $callback();
        }

    }

    public function answer($messages) {

        $chats = [];
		
		foreach($messages as $key => $message) {
			list($time, $role, $id) = explode("=>", $key);
			$chats[] = ['timestamp' => $time, 'role'=> $role, 'id' => $id, 'content' => $message];
		}

		return json_encode(array_pop($chats));
    }

    public function choices() {
        return $this->itemizeChoices($this->content->choices[0]->text);
    }

    protected function itemizeChoices($choices) {
        return array_filter(explode("\n", $choices));
    }

}