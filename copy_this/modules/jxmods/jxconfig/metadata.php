<?php
/**
 * Metadata version
 */
$sMetadataVersion = '1.1';
 
/**
 * Module information
 * 
 * @link      https://github.com/job963/jxConfig
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @copyright (C) 2015-2017 Joachim Barthel
 * @author    Joachim Barthel <jobarthel@gmail.com>
 * 
 */
$aModule = array(
    'id'           => 'jxconfig',
    'title'        => 'jxConfig - Display of Shop and Modules Configuration',
    'description'  => array(
                        'de' => 'Anzeige der Shop- und Moduleinstellungen als Gesamtbericht.',
                        'en' => 'Display of shop and modules settings as complete report.'
                        ),
    'thumbnail'    => 'jxconfig.png',
    'version'      => '0.2.0',
    'author'       => 'Joachim Barthel',
    'url'          => 'https://github.com/job963/jxConfig',
    'email'        => 'jobarthel@gmail.com',
    'extend'       => array(
                        ),
    'files'        => array(
                        'jxconfig'     	=> 'jxmods/jxconfig/application/controllers/admin/jxconfig.php'
                        ),
    'templates'    => array(
                        'jxconfig.tpl'  => 'jxmods/jxconfig/application/views/admin/tpl/jxconfig.tpl'
                        ),
    'events'       => array(
                        ),
    'settings' => array(
                        )
);