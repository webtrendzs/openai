<?php

/*
 * This file is part of contao chatGPT-3 bundle.
 *
 * (c) Ben Mosong
 *
 * @license LGPL-3.0-or-later
 */

use OpenAI\ChatGPT\ModuleChatGPT;

// Backend modules
$GLOBALS['BE_MOD']['content']['chatGPTServices'] = array(
    'tables' => array('tl_chatgpt_services', 'tl_chatgpt_subscribers')
);

// Front end modules
$GLOBALS['FE_MOD']['openai'] = array(
    'chatGPT' => ModuleChatGPT::class
);
