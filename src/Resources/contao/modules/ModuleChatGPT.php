<?php

/*
 * This file is part of contao escorts bundle.
 *
 * (c) Ben Mosong
 *
 * @license LGPL-3.0-or-later
 */

namespace OpenAI\ChatGPT;

use Contao\BackendTemplate;
use Contao\FrontendUser;
use Contao\StringUtil;
use Contao\System;
use Contao\Module;
use Contao\Input;
use Contao\File;
use Contao\Database;
use OpenAI\ChatGPT\Library\Completions\Completion;
use OpenAI\ChatGPT\Library\Instructor;

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

		if ($request && $container->get('contao.routing.scope_matcher')->isBackendRequest($request)) {
			$objTemplate = new BackendTemplate('be_wildcard');
			$objTemplate->wildcard = '### ' . $GLOBALS['TL_LANG']['FMD']['chatGPT'][0] . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = StringUtil::specialcharsUrl(System::getContainer()->get('router')->generate('contao_backend', array('do' => 'themes', 'table' => 'tl_module', 'act' => 'edit', 'id' => $this->id)));

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

		$this->loadLanguageFile('default');

		$objSession = System::getContainer()->get('contao.session.contao_frontend');

		$objSession->set('products', $this->getUserProducts());

		$this->Template->requestToken = System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue();

		$this->Template->userProducts = $objSession->get('products');
		$this->Template->letsGo = $GLOBALS['TL_LANG']['MSC']['letsGo'];
		$this->Template->howToHelp = $GLOBALS['TL_LANG']['MSC']['howToHelp'];
		$this->Template->noInstructions = $GLOBALS['TL_LANG']['MSC']['noInstructions'];
		$this->Template->noAssistant = $GLOBALS['TL_LANG']['MSC']['noAssistant'];
		$this->Template->noQuery = $GLOBALS['TL_LANG']['MSC']['noQuery'];
		$this->Template->letsGo = $GLOBALS['TL_LANG']['MSC']['letsGo'];

		if (Input::post('FORM_SUBMIT') == 'chat') {

			$instructions = [
				"message_id" => Input::post('message_id'),
				"action" => Input::post('instruction'),
				"assistant" => $objSession->get('products')[Input::post('assistant')]['introduction'],
				"query" => Input::post('query'),
				"model" => $this->chatGPTModel,
				"chat" => $objSession->get('chat')
			];

			$objInstructor = new Instructor($instructions['assistant']);

			$objCompletion = new Completion();

			$objCompletion->chat(
				$objInstructor->instruct(
					$instructions
				),
				function ($abort = null) use ($objCompletion, $objSession) {
					if ($abort) {
						echo $objCompletion->answer($objSession->get('chat'));
						exit;
					} else {
						echo $objCompletion->answer($objSession->get('chat'));
						exit;
					}
				}
			);
		}
	}

	private function generateAssets()
	{

		$container = System::getContainer();

		$strTypedPath = 'assets/openai/js/typed.min.js';
		$strAppPath = 'assets/openai/js/app.min.js';
		$strCssPath = 'assets/openai/css/app.min.css';

		File::putContent($strAppPath, file_get_contents(__DIR__ . '/../../assets/app.js'));
		File::putContent($strTypedPath, file_get_contents(__DIR__ . '/../../assets/typedumd.js'));
		File::putContent($strCssPath, file_get_contents(__DIR__ . '/../../assets/app.css'));

		$GLOBALS['TL_BODY'][] = sprintf('<script src="%s"></script>', $strTypedPath);
		$GLOBALS['TL_BODY'][] = sprintf('<script src="%s"></script>', $strAppPath);
		$GLOBALS['TL_HEAD'][] = sprintf('<link rel="stylesheet" href="%s" >', $strCssPath);
	}

	private function getUserProducts()
	{
		$objDatabase = Database::getInstance();

		$memberProducts = array_map('intval', $this->User->chatGPTServices);

		$objServices = $objDatabase
			->prepare("SELECT * from tl_chatgpt_services where id IN (" . implode(",", $memberProducts) . ") AND published=?")
			->execute(1);

		while ($objServices->next()) {

			$services[$objServices->id] = array(
				'name' => $objServices->serviceName,
				'description' => $objServices->description,
				'introduction' => $objServices->introduction
			);
		}

		return $services;
	}
}

class_alias(ModuleChatGPT::class, 'ModuleChatGPT');
