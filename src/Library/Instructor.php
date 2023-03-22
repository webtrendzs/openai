<?php

namespace OpenAI\GPT3\Library;

use OpenAI\GPT3\Library\Api;


class Instructor {

    /* who do you want this assistant to immitate? HR, Product manager? */
    public $assistant;

    /* custom options provided by the user */
    protected $instructions = [];

    /* model to use, defaults to gpt-3.5-turbo */
    protected $model = "gpt-3.5-turbo";

    public $hit_api = true;

    public function __construct($who, $describeMe = null) {

        $this->assistant = $who;
        $this->beginChat($describeMe);
    }

    public function __set($key, $value)
    {
        $this->{$key} = $value;
    }


    public function improveAnswer($arrinstructions) {

        $this->prepareMessage($arrinstructions);
        
        $this->updateInstructions([
            ["role" => "user", "content" => "Improve this answer so that you: \n" . $arrinstructions['query']]
        ]);
    }

    public function summarizeText($arrinstructions) {

        $this->prepareMessage($arrinstructions);

        $this->updateInstructions([
            ["role" => "user", "content" => "Summarize the text such that: \n
                ".$arrinstructions['query']]
        ]);
        
    }

    public function converse($arrinstructions) {

        $this->prepareMessage($arrinstructions);

        $this->updateInstructions([
            ["role" => "user", "content" => $arrinstructions['query']]
        ]);
        
    }

    public function instruct($arrinstructions) {

        if(!$this->hit_api) {
            return null;
        }

        switch($arrinstructions['action']) {
            case "improve":
                $this->improveAnswer($arrinstructions);
            case "summarize":
                $this->summarizeText($arrinstructions);
            default:
                if(!empty($arrinstructions['chat'])) {
                    $this->converse($arrinstructions);
                } else {
                    $this->updateInstructions([
                        ["role" => "user", "content" => $arrinstructions['query']]
                    ]);
                }
                
        }

        return [
            "model" => $this->model,
            "messages" => $this->instructions
        ];
    }

    public function updateInstructions($newInstructions) {

        $this->instructions = array_merge($this->instructions, $newInstructions);
        
    }

    protected function getMessageById($message_id, $messages) {
        $text = "";
        foreach($messages as $key => $message) {
			list($time, $role, $id) = explode("=>", $key);
			if ($id == $message_id) {
                $text = $message;
                break;
            }
		}
        
        return $text;
    }

    protected function prepareMessage($arrinstructions) {

        $currentMessages = $arrinstructions['chat'];

        $message = $this->getMessageById($arrinstructions['message_id'], $currentMessages);

        $this->beginChat();
        
        $this->updateInstructions([
            ["role" => "assistant", "content" => $message]
        ]);
    }

    protected function beginChat($describeMe = null) {
        $this->instructions = [];
        $this->updateInstructions([ 
			["role" => "system", "content" => $describeMe? $describeMe : "You are a helpful assistant as ".$this->assistant]
        ]);
    }

}