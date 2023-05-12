<?php

namespace OpenAI\ChatGPT\Library;


class Instructor
{

    /* who do you want this assistant to immitate? HR, Product manager? */
    public $assistant;

    /* custom options provided by the user */
    protected $instructions = [];

    /* model to use, defaults to gtp-4 */
    protected $model = "gpt-4";

    public $hit_api = true;

    public function __construct($who)
    {

        $this->assistant = $who;
        $this->beginChat();
    }

    public function __set($key, $value)
    {
        $this->{$key} = $value;
    }


    public function improveAnswer($arrinstructions)
    {

        $this->prepareMessage($arrinstructions);

        $this->updateInstructions([
            ["role" => "user", "content" => $arrinstructions['query']]
        ]);
    }

    public function summarizeText($arrinstructions)
    {

        $this->prepareMessage($arrinstructions);

        $this->updateInstructions([
            ["role" => "user", "content" => $arrinstructions['query']]
        ]);
    }

    public function converse($arrinstructions)
    {

        $this->prepareMessage($arrinstructions);

        $this->updateInstructions([
            ["role" => "user", "content" => $arrinstructions['query']]
        ]);
    }

    public function instruct($arrinstructions)
    {

        if (!$this->hit_api) {
            return null;
        }

        if (array_key_exists("model", $arrinstructions)) {
            $this->model = $arrinstructions['model'];
        }

        switch ($arrinstructions['action']) {
            case "improve":
                $this->improveAnswer($arrinstructions);
            case "summarize":
                $this->summarizeText($arrinstructions);
            default:
                if (!empty($arrinstructions['chat'])) {
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

    public function updateInstructions($newInstructions)
    {

        $this->instructions = array_merge($this->instructions, $newInstructions);
    }

    protected function getMessageById($message_id, $messages)
    {
        $text = "";
        foreach ($messages as $key => $message) {
            list($time, $role, $id) = explode("=>", $key);
            if ($id == $message_id) {
                $text = $message;
                break;
            }
        }

        return $text;
    }

    protected function prepareMessage($arrinstructions)
    {

        $currentMessages = $arrinstructions['chat'];

        $message = $this->getMessageById($arrinstructions['message_id'], $currentMessages);

        $this->beginChat();

        $this->updateInstructions([
            ["role" => "assistant", "content" => $message]
        ]);
    }

    protected function beginChat()
    {
        $this->instructions = [];
        $this->updateInstructions([
            ["role" => "system", "content" => $this->assistant]
        ]);
    }
}
