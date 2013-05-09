MainPanel = function() {
    MainPanel.superclass.constructor.call(this, {
        id: 'main-tabs',
        activeTab: 0,
        margins: '0 0 0 0',
        frame: false,
        bodyBorder: false,
        enableTabScroll: true,
        plain:true,
        resizeTabs: true,
        tabWidth: 150,
        minTabWidth: 120,
        enableTabScroll: true,
        plugins: new Ext.ux.TabCloseMenu()
	});

	this.on("schuelerEditTab",
		function(node, color){
			Ext.ux.ToastLars.msg(Lars.msg.loading, Lars.msg.loading_editor, 2);
			this.openTabEdit(node, color);}, this
		);
	this.on("viewTab",
		function(node, color){
			this.openTabView(node, color);}
		);
	this.on("viewIFrameTab",
		function(node, color){
			this.openTabIFrame(node, color);}
		);
	this.on("viewIFrameTabAll",
		function(node, color){
			this.openTabIFrameInternet(node, color);}
		);
	this.on("viewPackage",
			this.openTabPackage, this
		);
};

Ext.extend(MainPanel, Ext.TabPanel, {

    openTabEdit : function(node, color) {
        var frm;
        var htmlValue;
        var id = node.id;
        if (!(frm = this.getItem(id+'editorTab'))) {
            Ext.Ajax.request({
            	scope: this,
				url: 'lars_edit.php',
				params: {id: node.id,
			   			task: 'edit'},
				success: function(responseA, optionsA) {
					var responseData = Ext.util.JSON.decode(responseA.responseText);//passed back from server
					htmlValue = responseData.html;
					if (responseData.success){
						frm = new Ext.form.FormPanel({
							iconCls: 'editPage',
							layout: 'fit',
							title: '<span style="color: '+color+';">'+node.text+'</span>',
							tabTip: node.text,
							id: id+'editorTab',
							height: 500,
							minWidth: 200,
							minHeight: 200,
							autoScroll: true,
							bodyBorder: false,
							border: false,
							resizable: true,
							frame: false,
							closable:true,
							buttons: [
								{
									text: Lars.button_save,
									handler: function() {
										// Sync value
										tinyMCE.triggerSave();
										var f = Ext.getCmp(node.id+"editor");
										f.syncValue();

										// Submit the form
										frm.getForm().submit({
												url: "lars_edit.php",
												params: {
													id: id,
													task: "saveEdit",
													origValue: htmlValue ? htmlValue : ""
													},
												method: "POST",
												success: function(responseB, optionsB) {
													var responseData = Ext.util.JSON.decode(optionsB.response.responseText);//passed back from server
													htmlValue = frm.getForm().getValues().textField;
													if (responseData.success){
														Ext.ux.ToastLars.msg(Lars.msg_success_new_content, "", 2);
														Ext.getCmp(id+'editor').destroy();
														Ext.getCmp('main-tabs').remove(Ext.getCmp(id+'editorTab'));
														if (Ext.getCmp(id+'view')){
															// update content in HTML Panel
															Ext.getCmp(id+'view').reload();
														}
														var packageGrid = {};
														if (node.parentElement.match("Grid")){
														    packageGrid = Ext.getCmp(node.parentElement);
															// update content if it is already opened in package view
															var record = packageGrid.store.getById(node.id);
											                var row = (packageGrid.store.indexOf(record) >= 0) ? packageGrid.getView().getRow(packageGrid.store.indexOf(record)) : packageGrid.store.indexOf(record);
											                if (row < 0){
											                	packageGrid.store.load();
											                } else if (Ext.fly(row).hasClass('x-grid3-row-expanded') && record.iFrame){
										                		record.iFrame.setSrc();
											                }
														}
														if (node.parentElement.match('discussion-grid')){
															packageGrid = Ext.getCmp(node.parentElement);
															packageGrid.store.load({params: Ext.apply({},packageGrid.store.lastOptions.params)});;
														}
													} else {
														Ext.ux.ToastLars.msg(Lars.msg.failure, responseData.message, 5);
													}
													},
												failure: function(responseB, optionsB) {
													var responseData = Ext.util.JSON.decode(optionsB.response.responseText);//passed back from server
													Ext.ux.ToastLars.msg(Lars.msg.failure, responseData.message ? responseData.message : "", 5);
													htmlValue = frm.getForm().getValues().textField;
													if (responseData.changed){
														var oldName = responseData.oldName;
														var oldId = responseData.oldId;
										    			this.winChanged = new LarsNewHtmlTextWindowNew(oldId, oldName, htmlValue);
										    			this.winChanged.show();
										   				this.winChanged.setZIndex(90003);
														Ext.getCmp(id+'editor').destroy();
														Ext.getCmp('main-tabs').remove(Ext.getCmp(id+'editorTab'));
														Ext.Msg.show({
														   title:Lars.main.editor.save_info_1,
														   msg: Lars.main.editor.save_info_2,
														   buttons: Ext.Msg.OK,
														   animEl: 'elId',
														   icon: Ext.MessageBox.INFO
														});
													}
												}
										}); //form submit end
									}
								},{
									text: Lars.closedown,
									handler: function() {
										Ext.getCmp(id+'editor').destroy();
										Ext.getCmp('main-tabs').remove(Ext.getCmp(id+'editorTab'));
									}
								}

								],
								items: [
								{
									xtype: "tinymce",
									width: 300,
									height: 500,
									id: node.id+"editor",
									name: "textField",
									tinymceSettings: {
										theme : "advanced",
										language: Lars.tinyMceLanguage,
										verify_html : true,
										content_css: "moko/css/tinyMCE.css",
										plugins: "table,emotions,searchreplace,asciimath,asciisvg,media,paste,bid_tooltip",
										theme_advanced_buttons1 : "fontselect,formatselect,fontsizeselect,bold,italic,underline,sub,sup,separator,justifyleft,justifycenter,justifyright,separator,forecolor,backcolor",
										theme_advanced_buttons2 : "hr,charmap,separator,emotions,image,media,link,unlink,separator,bullist,numlist,tablecontrols,visualaid,asciimath,asciimathcharmap,asciisvg",
										theme_advanced_buttons3 : "undo,redo,separator,pasteword,separator,removeformat,separator,search,separator,fullscreen,code,bid_tooltip",
										theme_advanced_fonts : "Times New Roman=times new roman,times,serif;Arial=arial,helvetica,sans-serif;Courier New=courier new,courier,monospace;AkrutiKndPadmini=Akpdmi-n",
										theme_advanced_toolbar_location : "top",
										theme_advanced_toolbar_align : "left",
										theme_advanced_statusbar_location : "none",
										theme_advanced_resizing : false,
										convert_urls : false, // IMPORTANT!
									    AScgiloc : 'http://www.bid-owl.de/tools/asciisvg/svgimg.php',	//TODO: Server URI
									    ASdloc : 'http://www.bid-owl.de/mokodesk/moko/tiny_mce/plugins/asciisvg/js/d.svg', //TODO: Diese Adresse dynamisch ersetzen beim Speichern!
										extended_valid_elements : "embed[*]", //TODO
										file_browser_callback : function fileBrowserCallBack(field_name, url, type, win) {
														  this.win = new LarsBrowseFileWindow(win.document.forms[0].elements[field_name], node);
														  this.win.show();
									          				this.win.setZIndex(90010);
														 }
									},
									value: responseData.html
								}
								]
						});
				        Ext.getCmp('main-tabs').add(frm);
						Ext.getCmp('main-tabs').setActiveTab(frm);
					} else {
						Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.main.editor.document_not_available, 5);
					}
					},
			   failure: function(response,options){
					Ext.ux.ToastLars.msg(Lars.msg.failure, "");
			   }
			});
        }
        this.setActiveTab(frm);
    },
    openTabIFrame : function(node, color){
        var tab;
        var id = node.id+"viewIFrame";
        if (!(tab = this.getItem(id))){
			tab = new LarsIFramePanel({
				id:'desktop-grid-row-'+node.id,
				defaultSrc: "tools/get.php?object="+node.id,
		        loadMask: true,
				id: id,
				iconCls: node.attributes.iconCls || 'page',
				title: '<span style="color: '+color+';">'+node.text+'</span>',
				listeners: {domready:LarsViewer.LinkInterceptorIFrame},
	        	tabTip: node.text,
				layout: "fit",
				closable:true,
				autoScroll: true,
				node: node
			});
	        Ext.getCmp('main-tabs').add(tab);
			Ext.getCmp('main-tabs').setActiveTab(tab);
        }
    this.setActiveTab(tab);
    },

    openTabIFrameInternet : function(node, color){
        var tab;
        var id = node.id+"viewIFrame";
        if (!(tab = this.getItem(id))){
			tab = new LarsIFramePanelInternet({
				id:'desktop-grid-row-'+node.id,
				defaultSrc: node.attributes.lars_ref,
		        loadMask: true,
				id: id,
				edit: false,
				iconCls: node.attributes.iconCls,
				title: '<span style="color: '+color+';">'+node.text+'</span>',
				listeners: {domready:LarsViewer.LinkInterceptorIFrame},
	        	tabTip: node.text,
				layout: "fit",
				closable:true,
				autoScroll: true,
				node: node
			});
            tab.on('documentloaded', function(frame){
                var frameSet = frame.getWindow();
                for (var i = 0, l1 = frameSet.frames.length; i < l1; i++) {
			        var elements = frameSet.frames[i].document.getElementsByTagName( 'a' );
			        for (var i2 = 0, aElement = elements[i2], l2 = elements.length; i2 < l2; i2++) {
		                aElement = elements[i2];
	                	aElement.target = "_blank";
		                aElement.addEventListener( "click", function(e,t){
		                if (e.currentTarget.href.match(window.location.host)){
	                        tab.setSrc(e.currentTarget.href);
		                } else {
							LarsViewer.QuestionBrowserOpen(e);
						}
                        e.preventDefault();
                        return false;
		        		}, false );
					}
             	}
			});
	        Ext.getCmp('main-tabs').add(tab);
			Ext.getCmp('main-tabs').setActiveTab(tab);
        }

    this.setActiveTab(tab);
    },


    openTabView : function(node, color){
        var tab;
        var htmlValue;
        var id = node.id+"view";
        if (!(tab = this.getItem(id))){
            Ext.Ajax.request({
			   scope: this,
			   url: 'lars_json.php',
			   params: {id: node.id,
			   			task: 'view'},
				success: function(response,options) {
					var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
					if (responseData.success){
						tab = new LarsHTMLPanel({
							id: id,
							iconCls: 'page',
							title: '<span style="color: '+color+';">'+node.text+'</span>',
				        	tabTip: node.text,
				        	bodyStyle: 'padding:5px 5px 5px 5px; background:white; border-style:solid;border-color:#99bbe8;border-width:1;',
							layout: "fit",
							closable:true,
							html: responseData.html,
							autoScroll: true,
							node: node
						});
				        Ext.getCmp('main-tabs').add(tab);
						Ext.getCmp('main-tabs').setActiveTab(tab);
					} else {
						Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.main.editor.document_not_available, 5);
					}
					},
			   failure: function(response,options){
					Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.main.editor.document_not_available, 5);
			   }
			});
        }
    this.setActiveTab(tab);

    },
    openTabPackage : function(node){
        var tab;
        var htmlValue;
        var id = node.id;
		if (!(tab = this.getItem("package-panel-"+id))){

			// Unterscheidung zwischen Ordner und Aufgabenpaketen
			if (node.ownerTree && node.isLeaf()==false && node.ownerTree.id.match("topics") && !node.attributes.iconCls.match("report")){
				var discussionPanel = new LarsPackageDiscussion(node, {region:'center', collapsible: false}, node.attributes.groupColor);

			    tab = new LarsPackagePanel({
					iconCls: node.attributes.iconCls,
					border: false,
					bodyBorder: false,
					closable: true,
					id: "discussion-"+id,
					listeners: LarsViewer.LinkInterceptor,
					node: node,
					tabTip: node.text+Lars.main.grid.package_append,
					groupColor: node.attributes.groupColor,
					title: '<span style="color: '+node.attributes.groupColor+';">'+node.text+'</span>',
					layout: 'border',
					margins: '0 0 0 0',
					items: [
						discussionPanel //!
						]
				});
				discussionPanel.startAutoUpdate("d"+node.id);
				discussionPanel.on("destroy", function(){discussionPanel.stopAutoUpdate("d"+node.id)}, this);
			} else {
				var discussionPanel = new LarsPackageDiscussion(node, {region:'south', collapsible: true}, node.attributes.groupColor);

				var grid = new PackageGrid(node, node.attributes.groupColor);
				grid.getView().getRowClass = LarsGridConfig.applyRowClass;

			    tab = new LarsPackagePanel({
			        defaults: {
					    collapsible: true,
					    split: true
					},
					iconCls: node.attributes.iconCls,
					border: false,
					bodyBorder: false,
					closable: true,
					id: "package-panel-"+node.id,
					node: node,
					listeners: LarsViewer.LinkInterceptor,
					node: node,
					tabTip: node.text+Lars.main.grid.package_append,
					groupColor: node.attributes.groupColor,
					title: '<span style="color: '+node.attributes.groupColor+';">'+node.text+'</span>',
					layout: 'border',
					margins: '0 0 0 0',
					items: [
						grid, //!
						discussionPanel
						]
				});
				grid.startAutoUpdate("p"+node.id);
				discussionPanel.startAutoUpdate("d"+node.id);
				discussionPanel.on("destroy", function(){discussionPanel.stopAutoUpdate("d"+node.id)}, this);
				grid.on("destroy", function(){grid.stopAutoUpdate("p"+node.id)}, this);
			}
			tab.on(
				'activate',
				function(panel){
					if (!panel.node){return;};
					var treeMine = Ext.getCmp("topics-tree");
					var treeOthers = Ext.getCmp("topics-tree-t");
					var treeResources = Ext.getCmp("resources-tree");
					var nodeToSelect = treeOthers.getNodeById(panel.node.id);
					nodeToSelect = nodeToSelect ? nodeToSelect : treeMine.getNodeById(panel.node.id);
					if (nodeToSelect){
						treeOthers.expandPath(nodeToSelect.getPath());
						treeMine.expandPath(nodeToSelect.getPath());
						nodeToSelect.select();
					}
					var nodeToSelect2 = treeResources.getNodeById(panel.node.id);
					if (!nodeToSelect2){return;};
					treeResources.expandPath(nodeToSelect2.getPath());
					nodeToSelect2.select();
					}
			)
	        Ext.getCmp('main-tabs').add(tab);
	        Ext.getCmp('main-tabs').setActiveTab(tab);
	        Ext.getCmp('main-tabs').syncSize();
			if (!(node.ownerTree && node.isLeaf()==false && node.ownerTree.id.match("topics"))){grid.store.load({
				callback: function(){
						for(var index=0; index<grid.store.data.length; index++) {
							if (grid.store.data.items[index].data.LARS_TYPE == '1' || grid.store.data.items[index].data.LARS_TYPE == '3'){
								grid.expander.expandRow.defer(500, grid.expander, [index]);
							}
						}
					}
				});}
			discussionPanel.store.load();
		}
        this.setActiveTab(tab);
    },
    archivePackage: function(node){
 	   Ext.Ajax.request({
		   scope: this,
		   url: 'lars_json.php',
		   params: {id: node.id,
		   			task: 'archivePackage'},
			success: function(response,options) {
				var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
				if (responseData.success){
					Ext.ux.ToastLars.msg(Lars.main.tree.package_moved_msg, "", 5);
				} else {
					Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.main.tree.package_moved_msg, 5);
				}
			}
		});
	},
    archiveGroupPackage: function(node, archive_id){
 	   Ext.Ajax.request({
		   scope: this,
		   url: 'lars_json.php',
		   params: {id: node.id,
		   			task: 'archiveGroupPackage',
		   			archiveId: archive_id},
			success: function(response,options) {
				var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
				if (responseData.success){
					Ext.ux.ToastLars.msg(Lars.main.tree.package_moved_msg, "", 5);
				} else {
					Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.main.tree.package_moved_msg, 5);
				}
			}
		});
	}
}
);