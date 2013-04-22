LarsTreePanelFolderLinks = function() {
    LarsTreePanelFolderLinks.superclass.constructor.call(this, {
        id: 'resources-tree',
        margins: '0 0 0 0',
	    enableDD:true,
        dropConfig : {
		    appendOnly:true
		},
		bodyBorder: false,
        animate: false,
		shim: false,
        rootVisible: false,
        lines: true,
        autoScroll: true,
        containerScroll: true,
        root: new Ext.tree.AsyncTreeNode({
                text: 'ressources', 
                loader: new Ext.tree.TreeLoader({
                	dataUrl: 'lars_folder.php',
					baseParams: {task: "getResources"}
					}),
				id:'folder'
            }),
		tbar: [{
            iconCls:'folder-add',
            text:'',
            tooltip: Lars.main.tree.add_resource_folder,
            handler: function(){ 
            			this.win = new LarsResourcesAddWindow();
            			this.win.show();
           				this.win.setZIndex(90000);
					},
            scope: this
            			},'->',{
            iconCls:'icon-refresh',
            text:'',
            tooltip: Lars.refresh,
            handler: function(){
            	Ext.getCmp('resources-tree').root.reload();
            }
        }]             
    }
    );
	new Ext.tree.TreeSorter(this, {folderSort: true});    
	this.expand();
    this.getSelectionModel().on({
        'selectionchange' : function(sm, node){
            if(node){
                this.fireEvent('projectselect', node.attributes.id);
            }
        },
        scope:this
    });
	this.addEvents({schuelerEditTab: true});
	this.on('contextmenu', this.onContextMenu, this);
	this.on({
		dblClick: { 
			fn: function(node, a){
				if (node.isLeaf() && node.attributes.iconCls != "link"){
					Ext.getCmp('main-tabs').fireEvent('viewIFrameTab', node);
				} else if (node.attributes.iconCls == "report"){
					Ext.getCmp('main-tabs').fireEvent('viewPackage', node);
				} else if (node.attributes.lars_ref.match(window.location.host)){
					LarsViewer.QuestionTabOrBrowserOpen(node);
				} else if (node.attributes.lars_ref.length > 5 && !node.attributes.lars_ref.match(window.location.host)){
                    LarsViewer.QuestionBrowserOpenNode(node);
				}
			}
		}
	});
	this.on({
		click: { 
			fn: function(node, a){
				node.toggle();
				}
		}
	});
	
    this.on('render', function() {
		this.root.on({
					scope:this.el
					,beforeload:this.el.mask.createDelegate(this.el, [Lars.msg.loading_data])
					,load:this.el.unmask
					,loadexception:this.el.unmask
				});
	});	
	
	
	this.on("render", function(g){
		var dropOff = new Ext.dd.DropZone(g.getEl(), {
			ddGroup : 'resources',
			onDragDrop: function(e, id){
				alert("dragdrop");
			},
			
			notifyDrop : function(dd, e, data) {
				var t = e.getTarget('div.x-panel');
				this.win = new LarsResourcesAddNameWindow(data.node);
				this.win.show();
   				this.win.setZIndex(90000);
			},
			onContainerOver : function(source, e, data) { 
				return this.dropAllowed; 
				}
		});
	});
	
};

Ext.extend(LarsTreePanelFolderLinks, Ext.tree.TreePanel, {
    onContextMenu : function(node, e){
        if (node.isLeaf()){
            this.menu = new Ext.menu.Menu({
                id:'feeds-ctx',
                items: [{
                    text: Lars.main.tree.download,
		            id: 'add23121',
		            handler : function(){
						LarsGridConfig.downloadFile("tools/get.php?object="+rec.data.id);
		            },
		            iconCls: 'page-save'
                },{
		            text: Lars.rename,
		            id: 'add2314561',
		            handler : function(){
		            	this.win = new LarsChangeDescWindow(node);
		            	this.win.show();
       					this.win.setZIndex(90000);
		            },
		            iconCls: 'rename'
	        	},{
                    text: Lars.del,
		            id: 'add13243',
		            handler: function(){
	            	Ext.Msg.confirm(
						Lars.del,
						 Lars.main.grid.del_confirm, 
						function(btn){
							if (btn == 'yes'){
				            	Ext.Ajax.request({
					        		scope: this,
									url: 'lars_json.php',
									params: {id: node.id,
								   			name: node.attributes.origName,
								   			task: 'deleteItem'},
									success: function(response, options) {
										var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
										if (responseData.success){
											node.remove();
										} else {
											Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.msg.failure_delete, 5);
										}
									}
				            	});
							}
					}, this);
	            	},
		            iconCls: 'delete'
                },{
		            iconCls:'copy',
		            text:Lars.copy_document,
		            tooltip: Lars.copy_document_tt,
			            handler : function(a,b, c, d){
			            	node.data = {OBJ_DESC : node.text};
			            	fileRecordToCopy = node; 
			            	packageNodeToCopy = false;
			            },
            		scope: this
        		}                	
        		]
            });
            if (node.attributes.mimeType.match("text")){
            	this.menu.add({
                    text: Lars.main.show,
		            id: 'add2',
		            handler : function(){
                		Ext.getCmp('main-tabs').fireEvent('viewTab', node);
		            },
		            iconCls: 'page'
                },{
                    text: Lars.main.edit,
		            id: 'add1',
		            handler : function(){
                		Ext.getCmp('main-tabs').fireEvent('schuelerEditTab', node);
		            },
		            iconCls: 'editPage'
                })
            }
        } else if (node.attributes.iconCls != "folder-link"){
            this.menu = new Ext.menu.Menu({
                id:'feeds-ctx',
                items: [{
                    text: Lars.main.show_as_package,
		            id: 'add23121',
		            handler : function(){
                		Ext.getCmp('main-tabs').fireEvent('viewPackage', node);
		            },
		            iconCls: 'package-go'
                },{
                	text: Lars.main.tree.add_package_here,
            		id: 'add3342344',
            		handler: function(){
            			this.win = new AssignmentWindow(node);
            			this.win.show();
       					this.win.setZIndex(90000);
                	},
                	iconCls: 'report-add'
	        	},{
		            iconCls:'folder-add',
		            text:Lars.main.tree.add_folder_here,
		            handler: function(){ 
		            			this.win = new LarsAddFolderLinksWindow(node.attributes.id);
		           				this.win.show();
		           				this.win.setZIndex(90000);
							},
		            scope: this
    			},{
                    text: Lars.main.refresh_here,
		            id: 'add223453121',
		            handler : function(){
                		node.reload();
		            },
		            iconCls: 'icon-refresh'
                },{
		            text: Lars.rename,
		            id: 'add2314561',
		            handler : function(){
		            	this.win = new LarsChangeDescWindow(node);
		            	this.win.show();
       					this.win.setZIndex(90000);
		            },
		            iconCls: 'rename'
	        	},{
                    text: Lars.main.tree.folder_properties,
		            id: 'add2314521',
		            menu:  {
						items:[{
			                    text: Lars.main.tree.folder_as_package,
					            id: 'add23516721',
					            handler : function(){
			                		LarsGridConfig.changeAttribute(node.id, "OBJ_TYPE", "ASSIGNMENT_PACKAGE", node.attributes.OBJ_TYPE);
			                		node.parentNode.reload();
					            },
					            iconCls: 'report'
							},{
			                    text: Lars.main.tree.folder_as_normal,
					            id: 'add235121',
					            handler : function(){
			                		LarsGridConfig.changeAttribute(node.id, "OBJ_TYPE", "", node.attributes.OBJ_TYPE)
			                		node.parentNode.reload();
					            },
					            iconCls: 'folder'
							}]},
		            iconCls: 'folder-config'
                },{
                    text: Lars.del,
		            id: 'add13452443',
		            handler: function(){
	            	Ext.Msg.confirm(
						Lars.del,
						 Lars.main.tree.delete_folder_confirm, 
						function(btn){
							if (btn == 'yes'){
				            	Ext.Ajax.request({
					        		scope: this,
									url: 'lars_json.php',
									params: {id: node.id,
								   			name: node.attributes.origName,
								   			task: 'deleteItem'},
									success: function(response, options) {
										var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
										if (responseData.success){
											node.remove();
										} else {
											Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.msg.failure_delete, 5);
										}
									}
				            	});
							}
					}, this);
	            	
	            	},
		            iconCls: 'delete'
                }]
            });
	            if (node.attributes.OBJ_TYPE == "ASSIGNMENT_PACKAGE"){
						this.menu.add({
		                    text: Lars.main.tree.copy_package,
				            id: 'add2334d514561',
				            handler : function(){
				            	fileRecordToCopy = false; 
				            	packageNodeToCopy = node;
				            },
				            iconCls: 'copy'
		                });
	            } else {
						this.menu.add({
		                    text: Lars.main.tree.copy_folder,
				            id: 'add2334d54514561',
				            handler : function(){
				            	fileRecordToCopy = false; 
				            	packageNodeToCopy = node;
				            },
				            iconCls: 'copy'
		                });
	            }
            
        	} else {
            	this.menu = new Ext.menu.Menu({});
            }
            if (node.attributes.iconCls == "folder-link"){
            	this.menu.add({
			            text: Lars.rename,
			            id: 'add2314561',
			            handler : function(){
			            	this.win = new LarsChangeDescWindow(node);
			            	this.win.show();
           					this.win.setZIndex(90000);
			            },
			            iconCls: 'rename'
		        	},{
	            		text: Lars.main.tree.add_package_here,
	            		id: 'add54634',
	            		handler: function(){
	            			this.win = new AssignmentWindow(node);
	            			this.win.show();
           					this.win.setZIndex(90000);
	                	},
	                	iconCls: 'report-add'
		        	},{
			            iconCls:'folder-add',
			            text:Lars.main.tree.add_folder_here,
			            handler: function(){ 
			            			this.win = new LarsAddFolderLinksWindow(node.attributes.id);
			           				this.win.show();
			           				this.win.setZIndex(90000);
								},
			            scope: this
	    			},{
	                    text: Lars.main.refresh_here,
			            id: 'add223453121',
			            handler : function(){
	                		node.reload();
			            },
			            iconCls: 'icon-refresh'
                	},{
                    text: Lars.main.tree.del_link,
		            id: 'add231231',
		            handler: function(){
	            	Ext.Msg.confirm(
						Lars.del,
						 Lars.main.tree.del_confirm_link, 
						function(btn){
							if (btn == 'yes'){
				            	Ext.Ajax.request({
					        		scope: this,
									url: 'lars_json.php',
									params: {id: node.id,
								   			name: node.attributes.origName,
								   			task: 'deleteItem'},
									success: function(response, options) {
										var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
										if (responseData.success){
			                				node.parentNode.reload();
										} else {
											Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.msg.failure_delete, 5);
										}
									}
				            	});
							}
					}, this);
	            	},
		            iconCls: 'delete'
                	
                })
                
            }
                if (packageNodeToCopy && !node.isLeaf()){
	            	this.menu.add({						
	            		text: Lars.main.tree.insert_here,
			            id: 'add23141972340233',
			            handler : function(){
			            	Ext.Msg.confirm(
								Lars.main.tree.copy_confirm_1,
								 Lars.main.tree.copy_confirm_2_folder+packageNodeToCopy.attributes.text+Lars.main.tree.copy_confirm_3+node.attributes.text+Lars.main.tree.copy_confirm_4, 
								function(btn){
									if (btn == 'yes'){
									    Ext.Ajax.request({
									        url: 'lars_json.php',
									        params: {	
									    				task: "copyFolder",
									    				sourceId: packageNodeToCopy.id,
									    				targetId: node.id
									        		},
									        success: function(response, options){
												var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
												if (responseData.success){
											        Ext.ux.ToastLars.msg(Lars.msg.success_copy, responseData.name ? responseData.name : " ", 3);
													node.reload();
												} else {
													Ext.ux.ToastLars.msg(Lars.msg.failure_copy, responseData.name ? responseData.name : " ", 5);
												}
									        },
									        failure: function(){
												Ext.ux.ToastLars.msg(Lars.msg.failure_copy, "", 3);
									        },
									        scope: this
									    });  
									}
							}, this);
			            },
			            iconCls: "paste"
			            });
              }
                if (fileRecordToCopy && !node.isLeaf()){
	            	this.menu.add({						
	            		text: Lars.main.tree.copy_document,
			            id: 'add231419472340233',
			            handler : function(){
			            	Ext.Msg.confirm(
								Lars.main.tree.copy_confirm_1,
								 Lars.main.tree.copy_confirm_2_document+fileRecordToCopy.data.OBJ_DESC+Lars.main.tree.copy_confirm_3+node.attributes.text+Lars.main.tree.copy_confirm_4, 
								function(btn){
									if (btn == 'yes'){
									    Ext.Ajax.request({
									        url: 'lars_json.php',
									        params: {	
									    				task: "copyFile",
									    				sourceId: fileRecordToCopy.id,
									    				targetId: node.id
									        		},
									        success: function(response, options){
												var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
												if (responseData.success){
											        Ext.ux.ToastLars.msg(Lars.msg.success_copy, responseData.name ? responseData.name : " ", 3);
													node.reload();
												} else {
													Ext.ux.ToastLars.msg(Lars.msg.failure_copy, responseData.name ? responseData.name : " ", 5);
												}
									        },
									        failure: function(){
												Ext.ux.ToastLars.msg(Lars.msg.failure_copy, "", 3);
									        },
									        scope: this
									    });  
									}
							}, this);
			            },
			            iconCls: "paste"
			            });
              }
			if (node.attributes.lars_ref.match(window.location.host)) {
				this.menu.add({
					text: Lars.main.grid.open_link_2,
					id: 'add223453121',
					handler: function(){
						LarsViewer.QuestionTabOrBrowserOpen(node);
					},
					scope: this,
					iconCls: 'link'
				});
			}else if (node.attributes.lars_ref.length > 5) {
				this.menu.add({
					text: Lars.main.grid.open_link_2,
					id: 'add223453121',
					handler: function(){
						LarsViewer.QuestionBrowserOpenNode(node);
					},
					scope: this,
					iconCls: 'link'
				});
			}
        this.menu.showAt(e.getXY());
    },
   
    afterRender : function(){
        LarsTreePanelFolderLinks.superclass.afterRender.call(this);
		this.el.on({
			contextmenu:{fn:function(){return false;},stopEvent:true}
		});
    }
    
});