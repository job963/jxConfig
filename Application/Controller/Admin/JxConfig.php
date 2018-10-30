<?php
/**
 *    This file is part of the module jxConfig for OXID eShop Community Edition.
 *
 *    The module jxConfig for OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    The module jxConfig for OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      https://github.com/job963/jxConfig
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @copyright (C) 2015-2018 Joachim Barthel
 * @author    Joachim Barthel <jobarthel@gmail.com>
 *
 */

namespace JxMods\JxConfig\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Request;


class JxConfig extends AdminDetailsController
{

    /**
     * Displays the entries of database table oxconfig as readable table
     * 
     * @return string Filename of template file
     */
    public function render() 
    {

        /**
         * @var Request $request 
         */
        $request = Registry::getRequest();

        /**
         * @var Module $module
         */
        $module = oxNew(Module::class);

        $jxExtension = $request->getRequestEscapedParameter( 'jx_extension' );
        $jxVarName = $request->getRequestEscapedParameter( 'jx_varname' );
        $jxVarValue = $request->getRequestEscapedParameter( 'jx_varvalue' );

        $this->_aViewData['sExtension'] = $jxExtension;
        $this->_aViewData['sVarname'] = $jxVarName;
        $this->_aViewData['sVarvalue'] = $jxVarValue;
        
        $this->_aViewData['aConfigItems'] = $this->_getConfigData($jxExtension, $jxVarName, $jxVarValue, 'html');
        $this->_aViewData['aExtensions'] = $this->_getExtensionNames();

        $module->load( 'jxconfig' );
        $this->_aViewData['sModuleId'] = $module->getId();
        $this->_aViewData['sModuleVersion'] = $module->getInfo('version');
        
        parent::render();
        
        return "jxconfig.tpl";
    }
	
	
    /**
     * Exports the records from oxconfig as JSON download
     * 
     * @return null
     */
    public function jxExportConfigData () 
    {
        $aConfigItems = $this->_getConfigData($sExtension, $sVarname, $sVarvalue, 'json');
        $sJson = json_encode($aConfigItems, JSON_PRETTY_PRINT);

        header("Content-type: application/json");
        header("Content-length: ".strlen($sJson));
        header("Content-disposition: attachment; filename=\"oxconfig-export.json\"");
        
        echo $sJson;
        
        exit();
        
        return;
    }
    
    
    /**
     * Get all used module names of table oxconfig
     * 
     * @return array $aExtensionNames
     */
    private function _getExtensionNames() 
    {
        $sSql = "SELECT DISTINCT oxmodule "
                . "FROM oxconfig "
                . "WHERE oxshopid = " . $this->getConfig()->getBaseShopId() . ";";

        $aExtensionNames = $this->_fetchAllRecords($sSql);
        
        return $aExtensionNames;
    }
    
    
    /**
     * Retrieves and decodes records from table oxconfig filtered by the given parameters
     * 
     * @param string $sExtension    Optional filter string for extensions
     * @param string $sVarname      Optional filter string for variables
     * @param string $sVarvalue     Optional filter string criteria for values
     * @param string $sType         Optional output type parameter
     * 
     * @return array    Decoded values from oxconfig as array
     */
    private function _getConfigData($sExtension = '', $sVarname = '', $sVarvalue = '', $sType = 'html') 
    {
        $oDb = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);
        
        $sSql = "SELECT oxmodule, oxvarname, oxvartype, DECODE(oxvarvalue, " . $oDb->quote($this->getConfig()->getConfigParam('sConfigKey')) . ") AS oxvarvaluedecoded "
                . "FROM oxconfig "
                . "WHERE oxshopid = " . $this->getConfig()->getBaseShopId() . " "
                    . "AND oxmodule LIKE '%{$sExtension}%' "
                    . "AND oxvarname LIKE '%{$sVarname}%' "
                    . "AND DECODE(oxvarvalue, " . $oDb->quote($this->getConfig()->getConfigParam('sConfigKey')) . ") LIKE '%{$sVarvalue}%' "
                . "ORDER BY oxmodule, oxvarname ASC ";

        $aConfigItems = $this->_fetchAllRecords($sSql);

        foreach ($aConfigItems as $key => $aConfigItem) {
            if (($aConfigItems[$key]['oxvartype'] == 'arr') || ($aConfigItems[$key]['oxvartype'] == 'aarr') || (substr($aConfigItems[$key]['oxvarvaluedecoded'],0,2) == 'a:')) {
                // Unserializing of arrays
                if ($sType == 'html') {
                    $aConfigItems[$key]['oxvarvaluedecoded'] = print_r( unserialize( $aConfigItems[$key]['oxvarvaluedecoded'] ), TRUE);
                }
                else {
                    $aConfigItems[$key]['oxvarvaluedecoded'] = unserialize( $aConfigItems[$key]['oxvarvaluedecoded'] );
                }
            }
        }
        
        return $aConfigItems;
    }
    
    
    /**
     * Fetches all records of the given select statement
     * 
     * @param string $query
     * 
     * @return array
     */
    private function _fetchAllRecords(string $query)
    {
        $oDb = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);
        
        try {
            $resultSet = $oDb->select( $query );
        }
        catch (Exception $e) {
            echo $e->getMessage();
        }

        return $resultSet->fetchAll();
    }
    
	
}
