<?php

use Contao\Backend;
use Contao\Config;
use OpenAI\ChatGPT\Library\Api;

// contao/dca/tl_module.php

$GLOBALS['TL_DCA']['tl_module']['palettes']['chatGPT'] = '{title_legend},name,headline,type,chatGPTModel;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$GLOBALS['TL_DCA']['tl_module']['fields']["chatGPTModel"] = array(
    'inputType'               => 'select',
    'options_callback'        => array('tl_chatgpt_module', 'getModels'),
    'eval'                    => array('chosen' => true, 'tl_class' => 'w50'),
    'sql'                     => "varchar(64) COLLATE ascii_bin NOT NULL default 'gpt-4'"
);

class tl_chatgpt_module extends Backend
{

    /**
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function getModels()
    {
        $api = new Api(Config::get('openai'));
        $response = $api->get("/models");
        $models = json_decode($response->getContent());

        return array_map(function ($val) {
            return $val->id;
        }, $models->data);
    }
}
