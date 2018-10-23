[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]


<style>
    @media screen {
        #liste tr:hover td {
            background-color: #e0e0e0;
        }

        #liste td.activetime {
            background-image: url(bg/ico_activetime.png);
            min-width: 17px;
            background-position: center center;
            background-repeat: no-repeat;
        }
        .listitem, .listitem2 {
            padding-left: 4px;
            padding-right: 16px;
            white-space: nowrap;
        }
        pre {
            font: 12px Trebuchet MS, Tahoma, Verdana, Arial, Helvetica, sans-serif; 
            white-space: pre-wrap; 
            margin: 2px;
        }
    }
    
    @media print {
        body, p {
            font: 12px Trebuchet MS, Tahoma, Verdana, Arial, Helvetica, sans-serif; 
        }
    }
</style>


<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="jx_voucherserie_show">
</form>


<div style="height:100%; width:98%; overflow:hidden; padding-left:16px;">
    <div style="position:absolute;top:4px;right:8px;color:gray;font-size:0.9em;border:1px solid gray;border-radius:3px;">
        &nbsp;[{$sModuleId}]&nbsp;[{$sModuleVersion}]&nbsp;
    </div>
               
    <div id="liste" style="border:0px solid gray; padding:4px; width:99%; height:92%; [{*overflow-y:scroll;*}] [{*float:left;*}]">
        <div style="height: 12px;"></div>
            
        <form name="jxconfig" id="jxconfig" action="[{ $oViewConf->getSelfLink() }]" method="post">
            [{ $oViewConf->getHiddenSid() }]
            <input type="hidden" name="oxid" value="[{ $oxid }]">
            <input type="hidden" name="cl" value="jxconfig">
            <input type="hidden" name="fnc" value="">
            
            <table cellspacing="0" cellpadding="0" border="0" width="98%">
                <thead>
                    <tr>
                        <td class="listfilter first">
                            <div class="r1"><div class="b1">
                                <select name="jx_extension" onchange="document.jxconfig.submit();">
                                [{foreach item=aExtension from=$aExtensions}]
                                    <option value="[{$aExtension.oxmodule}]" [{if $aExtension.oxmodule==$sExtension}]SELECTED[{/if}]>[{$aExtension.oxmodule}]</option>
                                [{/foreach}]
                                </select>
                            </div></div>
                        </td>
                        <td class="listfilter">
                            <div class="r1"><div class="b1">
                                <input type="text" name="jx_varname" value="[{if $sVarname!="%"}][{$sVarname}][{/if}]" />
                            </div></div>
                        </td>
                        <td class="listfilter"><div class="r1"><div class="b1"></div></div></td>
                        <td class="listfilter">
                            <div class="r1"><div class="b1">
                                <input type="text" name="jx_varvalue" value="[{if $sVarvalue!="%"}][{$sVarvalue}][{/if}]" size="40" />
                                <input class="listedit" name="submitit" value="Suchen" onclick="Javascript:document.search.lstrt.value=0;" type="submit">
                            </div></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="listheader" style="width: 15%;" id="headcol1">&nbsp;[{ oxmultilang ident="JXCONFIG_EXTENSION" }]</td>
                        <td class="listheader" style="width: 25%;" id="headcol2">&nbsp;[{ oxmultilang ident="JXCONFIG_VARNAME" }]</td>
                        <td class="listheader" style="width: 5%;" id="headcol3">&nbsp;[{ oxmultilang ident="JXCONFIG_VARTYPE" }]</td>
                        <td class="listheader" style="width: 55%;" id="headcol4">&nbsp;[{ oxmultilang ident="JXCONFIG_VARVALUE" }]</td>
                    </tr
                </thead>
            </table>
        </form>

                    
        <div style="height: 95%; width:100%; overflow: scroll;">
            <table cellspacing="0" cellpadding="0" border="0" width=100%">
                <tbody style="width:100%;overflow:scroll;">
                [{foreach item=aConfig from=$aConfigItems}]
                    [{ cycle values="listitem,listitem2" assign="listclass" }]
                    <tr>
                        <td class="[{ $listclass }]" style="height: 15px; [{*width:200px;*}]" id="bodycol1">&nbsp;<nobr>[{$aConfig.oxmodule}]</nobr></td>
                        <td class="[{ $listclass }]" style="width: 25%;" id="bodycol2">[{$aConfig.oxvarname}]</td>
                        <td class="[{ $listclass }]" style="width: 5%;" id="bodycol3">[{$aConfig.oxvartype}]</td>
                        <td class="[{ $listclass }]" style="width: 55%;" id="bodycol4">[{if $aConfig.oxvarvaluedecoded|substr:0:5 == 'Array'}]<pre>[{$aConfig.oxvarvaluedecoded}]</pre>[{else}][{$aConfig.oxvarvaluedecoded}][{/if}]</td>
                    </tr>
                [{/foreach}]
                </tbody>
            </table>
        </div>
    </div>

</div>

<script type="text/javascript">
    document.getElementById("headcol1").style.width = document.getElementById("bodycol1").offsetWidth;
    document.getElementById("headcol2").style.width = document.getElementById("bodycol2").offsetWidth;
    document.getElementById("headcol3").style.width = document.getElementById("bodycol3").offsetWidth;
    document.getElementById("headcol4").style.width = document.getElementById("bodycol4").offsetWidth;
    //alert(document.getElementById("bodycol1").offsetWidth);
</script>
    
[{*include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"*}]

</div>

<div class="actions">

<ul>
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.print" href="#" onClick="Javascript:window.print();return false;" [{*target="edit"*}]>[{ oxmultilang ident="JXCONFIG_PRINT" }]</a> |</li>
    <li>
        <form name="jxconfigexport" id="btn.export" action="[{ $oViewConf->getSelfLink() }]" method="post">
            [{ $oViewConf->getHiddenSid() }]
            <input type="hidden" name="oxid" value="[{ $oxid }]">
            <input type="hidden" name="cl" value="jxconfig">
            <input type="hidden" name="fnc" value="jxExportConfigData">
            <a href="#" onClick="Javascript:document.jxconfigexport.submit();">[{ oxmultilang ident="JXCONFIG_EXPORT" }]</a>
        </form>
    </li>
</ul>
</div>

</div>

[{ oxscript }]

</body>
</html>
