<?php
/**
 * Metadata version
 */
$sMetadataVersion = '1.1';
 
/**
 * Module information
 */
$aModule = array(
    'id'           => 'jxconfig',
    'title'        => 'jxConfig - Display of Shop Configuration',
    'description'  => array(
                        'de' => 'Anzeige der Shop- und Moduleinstellungen als Gesamtbericht.',
                        'en' => 'Display of shop and module settings as complete report.'
                        ),
    'thumbnail'    => 'jxconfig.png',
    'version'      => '0.1.3',
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