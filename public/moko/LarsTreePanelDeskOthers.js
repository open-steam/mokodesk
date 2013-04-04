LarsTreePanelDeskOthers = function() {

    LarsTreePanelDeskOthers.superclass.constructor.call(this, {
			        id: "topics-tree-t",
			        rootVisible:false,
			        autoScroll:true,
			        animate: false,
			        iconCls: 'folder-table',
			        title: Lars.main.tree.desktops_title,
			        enableDD:true,
			        enableDrop:true,
			        dropConfig : {
					    appendOnly:true
					},
			        
			
					root: new Ext.tree.AsyncTreeNode({
			                text: 'root', 
			                loader: new Ext.tree.TreeLoader({
                				loadMask: {msg: Lars.main.tree.load_mask_desktops},
					            timeout: 90000,
								dataUrl:'lars_json.php',
								baseParams: {task: "getOwnStudents"}
							}),
							id:'source'
			        }),
					tbar: [{
				            iconCls:'user-add',
				            text:'',
				            tooltip: Lars.main.tree.embed_desktop,
				            handler: function(){ 
				            			this.win = new LarsAddSchuelerWindow();
				            			this.win.show();
	           							this.win.setZIndex(90000);
									},
				            scope: this
				            			},{
				            iconCls:'group-add',
				            text:'',
				            tooltip: Lars.main.tree.embed_desktop_group,
				            handler: function(){ 
				            			this.win = new LarsGroupsDesktopsGridWindow();
				            			this.win.show();
	           							this.win.setZIndex(90000);
	           							this.win.items.items[0].store.load();
									},
				            scope: this
            			},{
				            iconCls:'doc-others',
				            text:'',
//				            tooltip: Lars.main.tree.embed_desktop_group,
				            tooltip: "Auswahl für die Anzeige neuer Dokumente und Nachrichten", //TODO: Sprachen
				            handler: function(){ 
				            			this.win = new LarsDocumentsSubscriptionWindow();
				            			this.win.show();
	           							this.win.setZIndex(90000);
	           							this.win.items.items[0].store.load();
									},
				            scope: this
            			}
						
						,'->',{
				            iconCls:'icon-refresh',
				            text:'',
				            tooltip: Lars.refresh,
				            handler: function(){
				            	Ext.getCmp('topics-tree-t').root.reload(this.onManualReload);
				            },
				            scope: this
			        }] 
			    });
	new Ext.tree.TreeSorter(this, {
		folderSort: true,
		sortType: function(node) {
		    return node.attributes.iconCls+" "+Ext.util.Format.stripTags(node.attributes.text).toUpperCase();
	    }
	});
    var a = this.root;
    this.root.on("load",function(){this.onReload(this.root);}, this);
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
	            if (node.attributes.iconCls == "folder-archiv"){
					if (this.win = Ext.getCmp('custom-tree-win-'+node.id)){
						this.win.setZIndex(90000);
					} else {
		            	this.win = new LarsTreePanelArchivWindow(node);
		            	this.win.show();
       					this.win.setZIndex(90000);								
					}
	            }
				else{
					Ext.getCmp('main-tabs').fireEvent('viewPackage', node);
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
	
    this.on('beforenodedrop', function(dropEvent)
    	{
			var is_file = dropEvent.dropNode.leaf;
		    var copy = new Ext.tree.TreeNode(
		          Ext.apply({}, dropEvent.dropNode.attributes) 
		    );
		    dropEvent.dropNode = copy;
		    dropEvent.dropNode.attributes.iconCls = "report";
		    dropEvent.dropNode.attributes.groupColor = dropEvent.target.attributes.groupColor;
		    dropEvent.dropNode.leaf = true;
		    dropEvent.dropNode.attributes.leaf = true;
    		if (is_file){
            	Ext.Msg.confirm(
					Lars.main.tree.package_convert_confirm_1,
					 Lars.main.tree.package_convert_confirm_2, 
					function(btn){
						if (btn == 'yes'){
						    Ext.Ajax.request({
						        url: 'lars_json.php',
						        params: {	
						    				task: "copyFilePackage",
						    				sourceId: dropEvent.dropNode.id,
						    				targetId: dropEvent.target.id
						        		},
						        success: function(response, options){
									var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
									htmlValue = responseData.html;
									if (responseData.success){
								        Ext.ux.ToastLars.msg(Lars.msg.success_copy, responseData.name ? responseData.name : " ", 3);
								        dropEvent.dropNode.id = responseData.newId;
								        dropEvent.dropNode.attributes.id = responseData.newId;
									} else {
										Ext.ux.ToastLars.msg(Lars.msg.failure_copy, responseData.name ? responseData.name : " ", 3);
										dropEvent.dropNode.remove()
									}
									Ext.getCmp("topics-tree-t").el.unmask();
						        },
						        failure: function(){
									Ext.ux.ToastLars.msg(Lars.msg.failure_copy, "", 3);
									Ext.getCmp("topics-tree-t").el.unmask();
						        },
						        scope: this
						    });  
						}
				}, this);		    	
		    } else if (dropEvent.dropNode.attributes.OBJ_TYPE != "ASSIGNMENT_PACKAGE"){
				dropEvent.cancel = true;
				Ext.Msg.show({
				   title:Lars.msg.failure_copy,
				   msg: Lars.main.tree.msg_failure_no_package,
				   buttons: Ext.Msg.OK,
				   animEl: 'elId',
				   icon: Ext.MessageBox.ERROR
				});
		    } else {
			    Ext.Ajax.request({
			        url: 'lars_json.php',
			        params: {	
			    				task: "copyPackage",
			    				sourceId: dropEvent.dropNode.id,
			    				targetId: dropEvent.target.id
			        		},
			        success: function(response, options){
						var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
						if (responseData.success){
					        Ext.ux.ToastLars.msg(Lars.msg.success_copy, responseData.name ? responseData.name : " ", 3);
					        dropEvent.dropNode.id = responseData.newId;
					        dropEvent.dropNode.attributes.id = responseData.newId;
					        dropEvent.dropNode.attributes.origName = responseData.origName;
						} else {
							Ext.ux.ToastLars.msg(Lars.msg.failure_copy, responseData.name ? responseData.name : " ", 3);
							dropEvent.dropNode.remove()
						}
						Ext.getCmp("topics-tree-t").el.unmask();
			        },
			        failure: function(){
						Ext.ux.ToastLars.msg(Lars.msg.failure_copy, "", 3);
						Ext.getCmp("topics-tree-t").el.unmask();
			        },
			        scope: this
			    });    		
		    }
		    
		    
		}, this);

    this.on('render', function() {
		this.root.on({
					scope:this.el
					,beforeload:this.el.mask.createDelegate(this.el, [Lars.msg.loading_data])
					,load:this.el.unmask
					,loadexception:this.el.unmask
				});
	});	
		
};

Ext.extend(LarsTreePanelDeskOthers, Ext.tree.TreePanel, {
    onContextMenu : function(node, e){
            if (node.attributes.iconCls == "folder-archiv"){
	            this.menu = new Ext.menu.Menu({
	                id:'topics-tree-ctx',
	                items: [{
	                    text: Lars.main.tree.show_archive,
			            id: 'add231156',
			            handler : function(){
							if (this.win = Ext.getCmp('custom-tree-win-'+node.id)){
								this.win.setZIndex(90000);
							} else {
				            	this.win = new LarsTreePanelArchivWindow(node);
				            	this.win.show();
		       					this.win.setZIndex(90000);								
							}
			            },
			            iconCls: 'folder-archiv'
	                }]
	            });
            }
            else if (node.isLeaf()){
	            this.menu = new Ext.menu.Menu({
	                id:'topics-tree-ctx',
	                items: [{
	                    text: Lars.main.tree.show_package,
			            id: 'add2311',
			            handler : function(){
	                		Ext.getCmp('main-tabs').fireEvent('viewPackage', node);
			            },
			            iconCls: 'table-go'
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
	                    text: Lars.main.tree.copy_package,
			            id: 'add23345324514561',
			            handler : function(){
			            	packageNodeToCopy = node;
			            },
			            iconCls: 'copy'
	                },{
                    text: Lars.del,
		            id: 'add1324423',
		            handler: function(){
	            	Ext.Msg.confirm(
						Lars.del,
						 Lars.main.tree.del_confirm_package, 
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
											var id = node.id;
											if (Ext.getCmp(id)){
												Ext.getCmp('main-tabs').remove(Ext.getCmp(id));
											}
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
	            if ((parseInt(node.attributes.state) == 4) && node.attributes.archivable != "false"){
	            	this.menu.add({
			            iconCls:'database-save',
			            text:Lars.main.tree.move_to_archive,
			            tooltip: Lars.main.tree.move_to_archive_tt,
			            id: 'add232453',
			            handler : function(){
	                		Ext.getCmp('main-tabs').archiveGroupPackage(node, node.attributes.archiv);
	                		this.root.reload();
			            },
			            scope: this
	            	})
	            }

            } else if (node.attributes.iconCls != "user" && node.attributes.iconCls != "group"){//TODO: Fehler in icons?!
	            this.menu = new Ext.menu.Menu({
	                id:'topics-tree-ctx',
	                items: [{
	            		text: Lars.main.tree.add_package,
	            		id: 'add34',
	            		handler: function(){
	            			this.win = new AssignmentWindow(node);
	            			this.win.show();
           					this.win.setZIndex(90000);
							Ext.Msg.show({
							   title:Lars.msg.attention,
							   msg: Lars.main.tree.add_package_advice,
							   buttons: Ext.Msg.OK,
							   animEl: 'elId',
							   icon: Ext.MessageBox.INFO
							});
           					
	                	},
	                	iconCls: 'folder-add'
	                },{
						text: Lars.main.tree.open_learn_process,
			            id: 'add231419703',
			            handler : function(){
        					Ext.getCmp("topics-tree-t").el.mask(Lars.msg.loading, 'x-mask-loading');
						    Ext.Ajax.request({
						        url: 'lars_json.php',
						        params: {	
						    				task: "getLernstand",
						    				id: node.id
						        		},
						        success: function(response, options){
									var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
									htmlValue = responseData.html;
									if (responseData.success){
										var lernstandNode = {
												id: responseData.id,
												text: responseData.text
										};
				                		Ext.getCmp('main-tabs').fireEvent('viewTab', lernstandNode);
									}
									Ext.getCmp("topics-tree-t").el.unmask();
						        },
						        failure: function(){
									Ext.getCmp("topics-tree-t").el.unmask();
						        },
						        scope: this
						    });
			            	
			            },
			            iconCls: 'book-open'
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
		            id: 'add132443',
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
	            if (packageNodeToCopy){
            	 if (packageNodeToCopy.attributes.iconCls.match("report"))
	            	this.menu.add({						
	            		text: Lars.main.tree.insert_here,
			            id: 'add23141970233',
			            handler : function(){
			            	Ext.Msg.confirm(
								Lars.main.tree.copy_confirm_1,
								 Lars.main.tree.copy_confirm_2+packageNodeToCopy.attributes.text+Lars.main.tree.copy_confirm_3+node.attributes.text+Lars.main.tree.copy_confirm_4+Lars.main.tree.copy_confirm_5,
								function(btn){
									if (btn == 'yes'){
										Ext.ux.ToastLars.msg(Lars.main.tree.copy_progress_1, Lars.main.tree.copy_progress_2, 5);
										Ext.Ajax.request({
									        url: 'lars_json.php',
									        params: {	
									    				task: "copyPackage",
									    				sourceId: packageNodeToCopy.id,
									    				targetId: node.id
									        		},
									        success: function(response, options){
												var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
												if (responseData.success){
													newId = responseData.newId;
													origName = responseData.origName;
													newNode = new Ext.tree.TreeNode({
														leaf:true, 
														id: newId, 
														text: packageNodeToCopy.text, 
														iconCls: "report",
														ownerTree: node.ownerTree,
														state: 0
														});
													newNode.attributes.state = 0;
													newNode.attributes.origName = origName;
													node.appendChild(newNode);
											        Ext.ux.ToastLars.msg(Lars.msg.success_copy, responseData.name ? responseData.name : " ", 3);
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
            } else if (node.attributes.iconCls == "user" || node.attributes.iconCls == "group"){
	            this.menu = new Ext.menu.Menu({
	                id:'topics-tree-ctx',
	                items: [{
			            iconCls:'folder-add',
			            text:Lars.main.tree.add_folder,
			            handler: function(){ 
									if (this.win = Ext.getCmp('custom-tree-win-'+node.id)){
										this.win.setZIndex(90000);
									} else {
						            	this.win = new LarsAddFolderWindow(node);
						            	this.win.show();
				       					this.win.setZIndex(90000);								
									}
								},
			            scope: this
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
                    text: Lars.main.tree.del_desktop_link,
		            id: 'add23123413',
		            handler: function(){
	            	Ext.Msg.confirm(
						Lars.del,
						 Lars.main.tree.del_desktop_link_confirm, 
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
            	})
            }
        this.menu.showAt(e.getXY());
    },
    
    afterRender : function(){
        LarsTreePanelDeskOthers.superclass.afterRender.call(this);
		this.el.on({
			contextmenu:{fn:function(){return false;},stopEvent:true}
		});    
    },
    onReload : function(node){
			node.cascade(
				function(){
					this.expand(false, false, function(node){node.collapse(false, false)});
				}
			);
			var openTab = Ext.getCmp("main-tabs").getActiveTab(); 
			openTab.fireEvent("activate", openTab);
    }
    
});