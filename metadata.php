<?php
/**
 * Metadata version
 */
$sMetadataVersion = '2.0';
 
/**
 * Module information
 * 
 * @link      https://github.com/job963/jxConfig
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @copyright (C) 2015-2018 Joachim Barthel
 * @author    Joachim Barthel <jobarthel@gmail.com>
 * 
 */
$aModule = array(
    'id'            => 'jxconfig',
    'title'         => 'jxConfig - Display of Shop and Module Configurations',
    'description'   => array(
                        'de' => 'Anzeige der Shop- und Moduleinstellungen als Gesamtbericht.',
                        'en' => 'Display of shop and module settings as complete report.'
                        ),
    'thumbnail'     => 'jxconfig.png',
    'version'       => '0.3.1',
    'author'        => 'Joachim Barthel',
    'url'           => 'https://github.com/job963/jxConfig',
    'email'             => 'jobarthel@gmail.com',
    'extend'        => array(
                        ),
    'controllers'   => array(
                        'jxconfig' => JxMods\JxConfig\Application\Controller\Admin\JxConfig::class
                            ),
    'files'         => array(
                        'jxconfig'     	=> 'jxmods/jxconfig/Application/Controller/Admin/jxconfig.php'
                        ),
    'templates'     => array(
                        'jxconfig.tpl'  => 'jxmods/jxconfig/Application/views/admin/tpl/jxconfig.tpl'
                        ),
    'events'        => array(
                        ),
    'settings'      => array(
                        )
);