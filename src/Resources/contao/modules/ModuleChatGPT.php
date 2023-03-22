<?php

/*
 * This file is part of contao escorts bundle.
 *
 * (c) Ben Mosong
 *
 * @license LGPL-3.0-or-later
 */

namespace OpenAI\GPT3;

use Contao\BackendTemplate;
use Contao\FrontendUser;
use Contao\StringUtil;
use Contao\System;
use Contao\Module;
use Contao\Input;
use Contao\Config;
use Contao\Session;
use Contao\File;
use OpenAI\GPT3\Library\Completions\Completion;
use OpenAI\GPT3\Library\Instructor;


/**
 * Front end module "Module ChatGPT-3".
 */
class ModuleChatGPT extends Module
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_chat_gpt3';

	/**
	 * Display a wildcard in the back end
	 *
	 * @return string
	 */
	public function generate()
	{
		$container = System::getContainer();
		$request = $container->get('request_stack')->getCurrentRequest();
		
		if ($request && $container->get('contao.routing.scope_matcher')->isBackendRequest($request))
		{
			$objTemplate = new BackendTemplate('be_wildcard');
			$objTemplate->wildcard = '### ' . $GLOBALS['TL_LANG']['FMD']['chatGPT'][0] . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = StringUtil::specialcharsUrl(System::getContainer()->get('router')->generate('contao_backend', array('do'=>'themes', 'table'=>'tl_module', 'act'=>'edit', 'id'=>$this->id)));

			return $objTemplate->parse();
		}


		return parent::generate();
	}

	/**
	 * Generate the module
	 */
	protected function compile()
	{
		$this->import(FrontendUser::class, 'User');

		$this->generateAssets();

		$objSession = System::getContainer()->get('session');

		$sessionBag = $objSession->getBag('contao_frontend');
		
		$this->Template->requestToken = System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue();

		if(Input::post('FORM_SUBMIT') == 'chat') {

			$instructions = [
				"message_id" => Input::post('message_id'),
				"action" => Input::post('instruction'),
				"assistant" => Input::post('assistant'),
				"query" => Input::post('query'),
				"chat" => $sessionBag->get('chat')
			];

			$objInstructor = new Instructor($instructions['assistant']);

			$objCompletion = new Completion();

			//$objInstructor->hit_api = false;

			$objCompletion->chat($objInstructor->instruct(
				$instructions), 
				function($abort = false) use ($objCompletion) {

					if($abort) {
						echo $objCompletion->answer($sessionBag->get('chat'));
						exit;
					} else {
						echo $objCompletion->answer($sessionBag->get('chat'));
						exit;
					}
				
			});
		}
		
	}

	private function generateAssets() {

		$container = System::getContainer();

		$strRootDir = $container->getParameter('kernel.project_dir');
		$strWebDir = StringUtil::stripRootDir($container->getParameter('contao.web_dir'));

		$strTypedPath = 'assets/openai/js/typed.min.js';
		$strAppPath = 'assets/openai/js/app.min.js';
		$strCssPath = 'assets/openai/css/app.min.css';

		File::putContent($strAppPath, file_get_contents(__DIR__.'/../../assets/app.js'));
		File::putContent($strTypedPath, file_get_contents(__DIR__.'/../../assets/typedumd.js'));
		File::putContent($strCssPath, file_get_contents(__DIR__.'/../../assets/app.css'));
		
		$GLOBALS['TL_BODY'][] = sprintf('<script src="%s"></script>', $strTypedPath);
		$GLOBALS['TL_BODY'][] = sprintf('<script src="%s"></script>', $strAppPath);
		$GLOBALS['TL_HEAD'][] = sprintf('<link rel="stylesheet" href="%s" >', $strCssPath);

	}

}

class_alias(ModuleChatGPT::class, 'ModuleChatGPT');