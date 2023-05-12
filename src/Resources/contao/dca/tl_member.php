<?php


use Contao\CoreBundle\DataContainer\PaletteManipulator;

// Extend default palette
PaletteManipulator::create()
    ->addField(array('chatGPTServices'), 'groups_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_member');

// contao/dca/tl_module.php

$GLOBALS['TL_DCA']['tl_member']['fields']["chatGPTServices"] = array(
    'inputType'               => 'checkbox',
    'foreignKey'                => 'tl_chatgpt_services.serviceName',
    'eval'                    => array('multiple' => true, 'tl_class' => 'w50'),
    'sql'                     => "blob NULL"
);
