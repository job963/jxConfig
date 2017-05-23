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
 * @copyright (C) 2015-2017 Joachim Barthel
 * @author    Joachim Barthel <jobarthel@gmail.com>
 *
 */

class jxconfig extends oxAdminDetails {

    /**
     *
     * @var type 
     */
    protected $_sThisTemplate = "jxconfig.tpl";
    

    /**
     * Displays the entries of database table oxconfig as readable table
     * 
     * @return type Description
     */
    public function render() 
    {
        parent::render();

        $myConfig = oxRegistry::getConfig();
        $oDb = oxDb::getDb( oxDB::FETCH_MODE_ASSOC );
        
        if ($myConfig->getBaseShopId() == 'oxbaseshop') {
            // CE or PE shop
            $sShopId = "'{$myConfig->getBaseShopId()}'";
        } else {
            // EE shop
            $sShopId = "{$myConfig->getBaseShopId()}";
        }
        
        $sExtension = $this->getConfig()->getRequestParameter( 'jx_extension' );
        $sVarname = $this->getConfig()->getRequestParameter( 'jx_varname' );
        $sVarvalue = $this->getConfig()->getRequestParameter( 'jx_varvalue' );

        $aConfigItems = $this->_requestConfigData($sExtension, $sVarname, $sVarvalue, 'html');
        
        $sSql = "SELECT DISTINCT oxmodule "
                . "FROM oxconfig "
                . "WHERE oxshopid = {$sShopId} ";
        $rs = $oDb->Execute($sSql);
        $aExtensions = array();
        while (!$rs->EOF) {
            array_push($aExtensions, $rs->fields);
            $rs->MoveNext();
        }
        

        $this->_aViewData["sExtension"] = $sExtension;
        $this->_aViewData["sVarname"] = $sVarname;
        $this->_aViewData["sVarvalue"] = $sVarvalue;
        $this->_aViewData["aConfigItems"] = $aConfigItems;
        $this->_aViewData["aExtensions"] = $aExtensions;

        $oModule = oxNew('oxModule');
        $oModule->load('jxconfig');
        $this->_aViewData["sModuleId"] = $oModule->getId();
        $this->_aViewData["sModuleVersion"] = $oModule->getInfo('version');

        return $this->_sThisTemplate;
    }
	
	
    /**
     * Exports the records from oxconfig as JSON download
     * 
     * @return null
     */
    public function jxExportConfigData () 
    {
        $aConfigItems = $this->_requestConfigData($sExtension, $sVarname, $sVarvalue, 'json');
        $sJson = json_encode($aConfigItems, JSON_PRETTY_PRINT);

        header("Content-type: application/json");
        header("Content-length: ".strlen($sJson));
        header("Content-disposition: attachment; filename=\"oxconfig-export.json\"");
        
        echo $sJson;
        
        exit();
        
        return;
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
    private function _requestConfigData($sExtension = '', $sVarname = '', $sVarvalue = '', $sType = 'html') 
    {
        $oConfig = oxRegistry::getConfig();
        $oDb = oxDb::getDb( oxDB::FETCH_MODE_ASSOC );
        
        $sSql = "SELECT oxmodule, oxvarname, oxvartype, DECODE(oxvarvalue, " . $oDb->quote($oConfig->getConfigParam('sConfigKey')) . ") AS oxvarvaluedecoded "
                . "FROM oxconfig "
                . "WHERE oxshopid = " . $oDb->quote($oConfig->getBaseShopId()) . " "
                    . "AND oxmodule LIKE '%{$sExtension}%' "
                    . "AND oxvarname LIKE '%{$sVarname}%' "
                    . "AND DECODE(oxvarvalue, " . $oDb->quote($oConfig->getConfigParam('sConfigKey')) . ") LIKE '%{$sVarvalue}%' "
                . "ORDER BY oxmodule, oxvarname ASC ";

        try {
            $rs = $oDb->Select($sSql);
        }
        catch (Exception $e) {
            echo $e->getMessage();
        }
        $aConfigItems = array();
        while (!$rs->EOF) {
            array_push($aConfigItems, $rs->fields);
            $rs->MoveNext();
        }

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
     * Filters the output by object types and/or texts
     * 
     * @param type   $sReport       Object type/table, eg. article, user, ...
     * @param string $sFreeRegexp   Regular expression for filtering by text
     * 
     * @return string   SQL statement 
     */
    private function _createKeywordFilter( $sReport, $sFreeRegexp )
    {
        switch ( $sReport ) {

            case 'article':
                $aKeywords = array('oxarticles','oxartextends');
                break;

            case 'category':
                $aKeywords = array('oxarticles','oxartextends');
                break;

            case 'user':
                $aKeywords = array('oxuser','oxnewssubscribed','oxremark');
                break;

            case 'order':
                $aKeywords = array('oxorder','oxorderarticles');
                break;

            case 'payment':
                $aKeywords = array('oxpayments');
                break;

            case 'module':
                $aKeywords = array('oxconfig','oxconfigdisplay','oxtplblocks');
                break;

            case 'regexp':
                if (empty($sFreeRegexp)) {
                    $sFreeRegexp = '.';
                }
                $aKeywords = array( $sFreeRegexp );
                break;

            default:    // all
                return '';
                break;
        }
        
        if (count($aKeywords) > 1) {
            $sRegex = implode( '|', $aKeywords );
        } else {
            $sRegex = $aKeywords[0];
        }
        
        return "AND  l.oxsql REGEXP '" . $sRegex . "' ";
    }
	
}
