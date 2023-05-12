<?php

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

use Contao\Backend;
use Contao\DataContainer;
use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_chatgpt_services'] = array(
    // Config
    'config' => array(
        'dataContainer'               => DC_Table::class,
        //'ctable'                      => array('tl_chatgpt_subscribers'),
        'enableVersioning'            => true,
        'sql' => array(
            'keys' => array(
                'id' => 'primary'
            )
        )
    ),

    // List
    'list' => array(
        'sorting' => array(
            'mode'                    => DataContainer::MODE_SORTABLE,
            'panelLayout'             => 'filter;search',
            'defaultSearchField'      => 'serviceName'
        ),
        'label' => array(
            'fields'                  => array('serviceName'),
            'format'                  => '%s'
        ),
        'operations' => array(
            'edit',
            'copy',
            'cut',
            'delete',
            'toggle',
            'show'
        )
    ),

    // Palettes
    'palettes' => array(
        'default'                     => '{title_legend},serviceName,category,description,introduction;{publish_legend},published,start,stop'
    ),

    // Fields
    'fields' => array(

        'id' => array(
            'label'                   => array('ID'),
            'search'                  => true,
            'sql'                     => "int(10) unsigned NOT NULL auto_increment"
        ),
        'sorting' => array(
            'sql'                     => "int(10) unsigned NOT NULL default 0"
        ),
        'tstamp' => array(
            'sql'                     => "int(10) unsigned NOT NULL default 0"
        ),
        'serviceName' => array(
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory' => true, 'basicEntities' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'category' => array(
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory' => true, 'basicEntities' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'description' => array(
            'inputType'               => 'textarea',
            'eval'                    => array('style' => 'height:60px', 'decodeEntities' => true, 'tl_class' => 'clr'),
            'sql'                     => "text NULL"
        ),
        'introduction' => array(
            'inputType'               => 'textarea',
            'eval'                    => array('style' => 'height:60px', 'decodeEntities' => true, 'tl_class' => 'clr'),
            'sql'                     => "text NULL"
        ),
        'groups' => array(
            'filter'                  => true,
            'inputType'               => 'checkbox',
            'foreignKey'              => 'tl_member_group.name',
            'eval'                    => array('mandatory' => true, 'multiple' => true),
            'sql'                     => "blob NULL",
            'relation'                => array('type' => 'hasMany', 'load' => 'lazy')
        ),
        'cssClass' => array(
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('maxlength' => 64, 'tl_class' => 'w50'),
            'sql'                     => "varchar(64) NOT NULL default ''"
        ),
        'published' => array(
            'toggle'                  => true,
            'filter'                  => true,
            'inputType'               => 'checkbox',
            'eval'                    => array('doNotCopy' => true),
            'sql'                     => array('type' => 'boolean', 'default' => false)
        ),
        'start' => array(
            'inputType'               => 'text',
            'eval'                    => array('rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'),
            'sql'                     => "varchar(10) NOT NULL default ''"
        ),
        'stop' => array(
            'inputType'               => 'text',
            'eval'                    => array('rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'),
            'sql'                     => "varchar(10) NOT NULL default ''"
        )
    )
);

/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @internal
 */
class tl_chatgpt_services extends Backend
{
    /**
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
    }
}
