<?php

/*
 * This file is part of contao chatGPT-3 bundle.
 *
 * (c) Ben Mosong
 *
 * @license LGPL-3.0-or-later
 */

use OpenAI\GPT3\ModuleChatGPT;

// Front end modules
$GLOBALS['FE_MOD']['openai'] = array(
    'chatGPT' => ModuleChatGPT::class
);