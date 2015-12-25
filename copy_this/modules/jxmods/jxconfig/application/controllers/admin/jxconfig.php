<?php
/**
 *    This file is part of the module jxAdminLog for OXID eShop Community Edition.
 *
 *    The module jxAdminLog for OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    The module jxAdminLog for OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      https://github.com/job963/jxAdminLog
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @copyright (C) 2015 Joachim Barthel
 * @author    Joachim Barthel <jobarthel@gmail.com>
 *
 */

class jxconfig extends oxAdminDetails {

    protected $_sThisTemplate = "jxconfig.tpl";

    /**
     * Displays the latest log entries
     */
    public function render() 
    {
        parent::render();

        $myConfig = oxRegistry::getConfig();
        $oDb = oxDb::getDb( oxDB::FETCH_MODE_ASSOC );
        
        $sExtension = $this->getConfig()->getRequestParameter( 'jx_extension' );
        if (empty($sExtension)) {
            $sExtension = '*';
        }
        $sVarname = $this->getConfig()->getRequestParameter( 'jx_varname' );
        if (empty($sVarname)) {
            $sVarname = '*';
        }
        $sVarvalue = $this->getConfig()->getRequestParameter( 'jx_varvalue' );
        if (empty($sVarvalue)) {
            $sVarvalue = '*';
        }
        //echo $sExtension . ' / ' . $sVarname . ' / ' . $sVarvalue;

        $sSql = "SELECT oxmodule, oxvarname, oxvartype, DECODE(oxvarvalue, " . $oDb->quote($myConfig->getConfigParam('sConfigKey')) . ") AS oxvarvaluedecoded "
                . "FROM `oxconfig` "
                . "/*WHERE oxmodule = '{$sExtension}'*/ "
                . "/*ORDER BY `OXMODULE`, `OXVARNAME` ASC*/; ";

        try {
        $rs = $oDb->Execute($sSql);
        }
        catch (Exception $e) {
            echo $e->getMessage();
        }
        $aConfigItems = array();
        while (!$rs->EOF) {
            if (( strpos($rs->fields['oxmodule'], $sExtension) !== FALSE ) || ($sExtension == '*')) {
                if ((strpos(strtolower($rs->fields['oxvarname']), strtolower($sVarname)) !== FALSE ) || ($sVarname == '*')) {
                    if ((strpos(strtolower($rs->fields['oxvarvaluedecoded']), strtolower($sVarvalue)) !== FALSE ) || ($sVarvalue == '*')) {
                        array_push($aConfigItems, $rs->fields);
                    }
                }
            }
            $rs->MoveNext();
        }
        //echo '<pre>';
        //print_r($aConfigItems);
        //echo '</pre>';

        foreach ($aConfigItems as $key => $aConfigItem) {
            if (($aConfigItems[$key]['oxvartype'] == 'arr') || ($aConfigItems[$key]['oxvartype'] == 'aarr')) {
                //
                $aConfigItems[$key]['oxvarvaluedecoded'] = print_r( unserialize( $aConfigItems[$key]['oxvarvaluedecoded'] ), TRUE);
            }
            $aSort[] = $aConfigItem[$key]['oxmodule'] . '-' . $aConfigItem[$key]['oxvarname'];
        }
        array_multisort($aSort, SORT_ASC, $aConfigItems);

        
        $sSql = "SELECT DISTINCT oxmodule FROM oxconfig ";
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
    
    
    private function _keywordHighlighter( $sText ) 
    {
        $aSearch = array(
            'insert',
            'update',
            'delete'
        );
        $aReplace = array(
            '<span style="color:green;">insert</span>',
            '<span style="color:blue;">update</span>',
            '<span style="color:red;">delete</span>'
        );
        
        $sText = str_replace($aSearch, $aReplace, $sText);

        return $sText;
    }
    
    
    /*
    public function deleteVoucher() 
    {
        $sVoucherId = oxRegistry::getConfig()->getRequestParameter( 'voucherdelid' );
        
        //echo 'deleteVoucher='.$sVoucherId;
        $sSql = "DELETE FROM oxvouchers WHERE oxid = '{$sVoucherId}' ";
        $oDb = oxDb::getDb();
        $oDb->Execute($sSql);
        $oDb = null;
        
        return;
    }
    */
	
}
