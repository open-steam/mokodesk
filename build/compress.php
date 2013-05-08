<?php

$baseDir = dirname(dirname(__FILE__));
$publicDir = $baseDir . "/public";
$tmpDir = $baseDir . "/var/tmp";
$compressorCmd = "java -jar " . $baseDir . "/build/yuicompressor-2.4.7.jar";

$ext_js_files = array(
        $baseDir . "/libary/javascript/ext2/adapter/ext/ext-base.js",
        $baseDir . "/libary/javascript/ext2/ext-all.js",
    );

$lang_de_colloquial_js_files = array(
        $baseDir . "/src/moko_lang/ext-lang-de.js",
        $baseDir . "/src/moko_lang/lars_de.js",
    );

$lang_de_formal_js_files = array(
        $baseDir . "/src/moko_lang/ext-lang-de.js",
        $baseDir . "/src/moko_lang/lars_de.js",
        $baseDir . "/src/moko_lang/moko_de.js",
    );

$lang_en_colloquial_js_files = array(
        $baseDir . "/src/moko_lang/ext-lang-en.js",
        $baseDir . "/src/moko_lang/lars_en.js",
    );

$lang_en_formal_js_files = array(
        $baseDir . "/src/moko_lang/ext-lang-en.js",
        $baseDir . "/src/moko_lang/lars_en.js",
        $baseDir . "/src/moko_lang/moko_en.js",
    );

$lang_fr_js_files = array(
        $baseDir . "/src/moko_lang/ext-lang-fr.js",
        $baseDir . "/src/moko_lang/lars_fr.js",
    );

$ux_js_files = array(
        $baseDir . "/libary/javascript/ux/Ext.ux.TinyMCE.js",
        $baseDir . "/libary/javascript/ux/Ext.ux.ToastLars.js",
        $baseDir . "/libary/javascript/ux/Ext.ux.ToastLarsDiscussion.js",
        $baseDir . "/libary/javascript/ux/miframe.js",
        $baseDir . "/libary/javascript/ux/TabCloseMenu.js",
        $baseDir . "/libary/javascript/ux/uxmedia.js",
        $baseDir . "/libary/javascript/ux/uxflash.js",
    );

$add_js_files = array(
        $baseDir . "/libary/javascript/ASCIIMathML2wMnGFallback.js",
        $baseDir . "/libary/javascript/copy.js",
    );

$menu_js_files = array(
        $baseDir . "/libary/javascript/menu/EditableItem.js",
        $baseDir . "/libary/javascript/menu/RangeMenu.js",
    );

$grid_js_files = array(
        $baseDir . "/libary/javascript/grid/Ext.Grid.CheckColumn.js",
        $baseDir . "/libary/javascript/grid/Ext.ux.grid.RowActions.js",
        $baseDir . "/libary/javascript/grid/Ext.ux.grid.Search.js",
        $baseDir . "/libary/javascript/grid/RowExpander.js",
    );

$moko_js_files = array(
        $baseDir . "/src/moko/LarsViewer.js",
        $baseDir . "/src/moko/LarsAddAssignmentWindow.js",
        $baseDir . "/src/moko/LarsAddFolderLinksWindow.js",
        $baseDir . "/src/moko/LarsAddFolderWindow.js",
        $baseDir . "/src/moko/LarsAddLinkWindow.js",
        $baseDir . "/src/moko/LarsAddSchuelerWindow.js",
        $baseDir . "/src/moko/LarsBrowseFileWindow.js",
        $baseDir . "/src/moko/LarsChangeDescWindow.js",
        $baseDir . "/src/moko/LarsCommentWindow.js",
        $baseDir . "/src/moko/LarsCustomImagePanel.js",
        $baseDir . "/src/moko/LarsCustomPanelTextChange.js",
        //$baseDir . "/src/moko/LarsCustomSite.js",
        $baseDir . "/src/moko/LarsDesktop.js",
        $baseDir . "/src/moko/LarsDesktopDiscussion.js",
        $baseDir . "/src/moko/LarsDesktopErrorWindow.js",
        $baseDir . "/src/moko/LarsDesktopGrid.js",
        $baseDir . "/src/moko/LarsDesktopNorth3.js",
        $baseDir . "/src/moko/LarsDesktopNotes.js",
        $baseDir . "/src/moko/LarsDocumentsSubscriptionGrid.js",
        $baseDir . "/src/moko/LarsDocumentsSubscriptionWindow.js",
        $baseDir . "/src/moko/LarsGroupsAddUserWindow.js",
        $baseDir . "/src/moko/LarsGroupsDesktopsGrid.js",
        $baseDir . "/src/moko/LarsGroupsDesktopsGridWindow.js",
        $baseDir . "/src/moko/LarsGroupsRightsGrid.js",
        $baseDir . "/src/moko/LarsGroupsRightsGridWindow.js",
        $baseDir . "/src/moko/LarsGroupsTreePanel.js",
        $baseDir . "/src/moko/LarsGroupsTreePanelWindow.js",
        $baseDir . "/src/moko/LarsHtmlMessageWindow.js",
        $baseDir . "/src/moko/LarsHtmlPanel.js",
        $baseDir . "/src/moko/LarsIFramePanel.js",
        $baseDir . "/src/moko/LarsIFramePanelInternet.js",
        $baseDir . "/src/moko/LarsJoinChat.js",
        $baseDir . "/src/moko/LarsMainPanel.js",
        $baseDir . "/src/moko/LarsMessageWindow.js",
        $baseDir . "/src/moko/LarsNewHtmlTextWindow.js",
        $baseDir . "/src/moko/LarsNewHtmlTextWindowNew.js",
        $baseDir . "/src/moko/LarsOverrides.js",
        $baseDir . "/src/moko/LarsPackageDiscussion.js",
        $baseDir . "/src/moko/LarsPackageGrid.js",
        $baseDir . "/src/moko/LarsPackagePanel.js",
        $baseDir . "/src/moko/LarsResourcesAddNameWindow.js",
        $baseDir . "/src/moko/LarsResourcesAddWindow.js",
        $baseDir . "/src/moko/LarsResourcesPanel.js",
        $baseDir . "/src/moko/LarsSetPackageRightsWindow.js",
        $baseDir . "/src/moko/LarsTopicsPanel.js",
        $baseDir . "/src/moko/LarsTopicsPanelTeacher.js",
        $baseDir . "/src/moko/LarsTreePanel.js",
        $baseDir . "/src/moko/LarsTreePanelArchiv.js",
        $baseDir . "/src/moko/LarsTreePanelArchivWindow.js",
        $baseDir . "/src/moko/LarsTreePanelBin.js",
        $baseDir . "/src/moko/LarsTreePanelBinWindow.js",
        $baseDir . "/src/moko/LarsTreePanelDesk.js",
        $baseDir . "/src/moko/LarsTreePanelDeskOthers.js",
        $baseDir . "/src/moko/LarsTreePanelFolderLinks.js",
        $baseDir . "/src/moko/LarsTreePanelLinks.js",
        $baseDir . "/src/moko/LarsUpdater.js",
        $baseDir . "/src/moko/LarsUploadFileWindow.js",
        $baseDir . "/src/moko/LarsUploadImageWindow.js",
        $baseDir . "/src/moko/LarsVersion.js",
        $baseDir . "/src/moko/LarsVoiceChat.js",
    );

$moko_css_files = array(
        $baseDir . "/src/moko_css/Ext.ux.grid.RowActions.css",
        $baseDir . "/src/moko_css/filetype.css",
        $baseDir . "/src/moko_css/larsDesktop.css",
        $baseDir . "/src/moko_css/rowactions.css",
    );

$ext_css_files = array(
        $baseDir . "/libary/javascript/ext2/resources/css/ext-all.css",
    );

//LarsSchreibtischMin_colloquial.js
$tmpFile = $publicDir  . "/moko/js/LarsSchreibtisch.js";
passthru("cat " . implode(" ", array_merge($ext_js_files, $ux_js_files, $add_js_files, $menu_js_files, $grid_js_files, $lang_de_colloquial_js_files, $moko_js_files)) . " > " . $tmpFile);
passthru($compressorCmd . " " . $tmpFile . " -o " . $publicDir . "/moko/js/LarsSchreibtischMin_colloquial.js");

//LarsSchreibtischMin_formal.js
passthru("cat " . implode(" ", array_merge($ext_js_files, $ux_js_files, $add_js_files, $menu_js_files, $grid_js_files, $lang_de_formal_js_files, $moko_js_files)) . " > " . $tmpFile);
passthru($compressorCmd . " " . $tmpFile . " -o " . $publicDir . "/moko/js/LarsSchreibtischMin_formal.js");

//LarsSchreibtischMinEn.js
passthru("cat " . implode(" ", array_merge($ext_js_files, $ux_js_files, $add_js_files, $menu_js_files, $grid_js_files, $lang_en_colloquial_js_files, $moko_js_files)) . " > " . $tmpFile);
passthru($compressorCmd . " " . $tmpFile . " -o " . $publicDir . "/moko/js/LarsSchreibtischMinEn.js");

//LarsSchreibtischMinEn_formal.js
passthru("cat " . implode(" ", array_merge($ext_js_files, $ux_js_files, $add_js_files, $menu_js_files, $grid_js_files, $lang_en_formal_js_files, $moko_js_files)) . " > " . $tmpFile);
passthru($compressorCmd . " " . $tmpFile . " -o " . $publicDir . "/moko/js/LarsSchreibtischMinEn_formal.js");

//LarsSchreibtischMinFr.js
passthru("cat " . implode(" ", array_merge($ext_js_files, $ux_js_files, $add_js_files, $menu_js_files, $grid_js_files, $lang_fr_js_files, $moko_js_files)) . " > " . $tmpFile);
passthru($compressorCmd . " " . $tmpFile . " -o " . $publicDir . "/moko/js/LarsSchreibtischMinFr.js");

//LarsSchreibtischCssMin.css
$tmpFile = $publicDir  . "/moko/css/LarsSchreibtischCss.css";
passthru("cat " . implode(" ", array_merge($moko_css_files)) . " > " . $tmpFile);
passthru($compressorCmd . " " . $tmpFile . " -o " . $publicDir . "/moko/css/LarsSchreibtischCssMin.css");

//ext-allMin.css
$tmpFile = $publicDir  . "/moko/css/ext-all.css";
passthru("cat " . implode(" ", array_merge($ext_css_files)) . " > " . $tmpFile);
passthru($compressorCmd . " " . $tmpFile . " -o " . $publicDir . "/moko/css/ext-allMin.css");

//tiny_mce/plugins/asciisvg/js/ASCIIsvgPIMin.js

//tiny_mce/plugins/asciimath/js/ASCIIMathMLwFallbackMin.jss