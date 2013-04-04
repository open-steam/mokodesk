LarsTreePanelDesk = function() {

    LarsTreePanelDesk.superclass.constructor.call(this, {
			        id: "topics-tree",
					iconCls: 'application-home',
			        rootVisible:false,
			        animate: false,
			        autoScroll:true,
			        title: Lars.main.tree_title,
					root: new Ext.tree.AsyncTreeNode({
			                text: Lars.main.tree.main_directory, 
			                loader: new Ext.tree.TreeLoader({
								dataUrl:'lars_json.php',
								baseParams: {task: "getSchuelerTopics"}
							}),
							id:'source'
			        })
					,tbar: [{
			            iconCls:'folder-add',
			            text:'',
			            tooltip: Lars.main.tree.add_folder,
			            handler: function(){ 
			            			this.win = new LarsAddFolderWindow();
			            			this.win.show();
           							this.win.setZIndex(90000);
								},
			            scope: this
			            			},{
			            iconCls:'folder-archiv',
			            text:'',
			            tooltip: Lars.main.tree.show_archive,
			            handler: function(){ 
	            			var nodeA = {text: "Archiv", id: larsArchivId};
							if (this.win = Ext.getCmp('custom-tree-win-'+nodeA.id)){
								this.win.show();
								this.win.setZIndex(90000);
							} else {
				            	this.win = new LarsTreePanelArchivWindow(nodeA);
				            	this.win.show();
		       					this.win.setZIndex(90000);								
							}
						},
			            scope: this
			            			},{
			            iconCls:'bin',
			            text:'',
			            tooltip: Lars.main.tree.show_bin,
			            handler: function(){ 
									if (this.win = Ext.getCmp('bin-explorer-win')){
										this.win.show();
										this.win.setZIndex(90000);
									} else {
						            	this.win = new LarsTreePanelBinWindow();
						            	this.win.show();
				       					this.win.setZIndex(90000);								
									}
								},
			            scope: this
			            			},{
			            iconCls:'key',
			            text:'',
			            tooltip: Lars.main.tree.set_rights,
			            handler: function(){ 
			            			this.win = new LarsGroupsRightsGridWindow();
			            			this.win.show();
           							this.win.setZIndex(90000);
           							this.win.items.items[0].store.load();
								},
			            scope: this
			            			},'->',{
			            iconCls:'icon-refresh',
			            text:'',
			            tooltip: Lars.refresh,
			            handler: function(){
			            	Ext.getCmp('topics-tree').root.reload();
			            },
			            scope: this
			        }] 
			              
			    });
	new Ext.tree.TreeSorter(this, {folderSort: true});    
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
    this.on('render', function() {
		this.root.on({
					scope:this.el
					,beforeload:this.el.mask.createDelegate(this.el, [Lars.msg.loading_data])
					,load:this.el.unmask
					,loadexception:this.el.unmask
				});
	});	

};


Ext.extend(LarsTreePanelDesk, Ext.tree.TreePanel, {
    onContextMenu : function(node, e){
            this.menu = new Ext.menu.Menu({
	                id:'topics-tree-ctx',
	                items: []
            });
            if (node.attributes.iconCls == "folder-archiv"){
            	this.menu.add({
	                    text: Lars.main.tree.show_archive,
			            id: 'add2311',
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
	                });
            }
            else if (node.isLeaf()){
            	this.menu.add({
	                    text: Lars.main.tree.show_package,
			            id: 'add2311',
			            handler : function(){
	                		Ext.getCmp('main-tabs').fireEvent('viewPackage', node);
			            },
			            iconCls: 'table-go'
	                },{
	                    text: Lars.main.tree.copy_package,
			            id: 'add2334514561',
			            handler : function(){
			            	packageNodeToCopy = node;
			            },
			            iconCls: 'copy'
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
											Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.main.tree.del_failure_msg, 5);
										}
									}
				            	});
							}
					}, this);
	            	
	            	},
		            iconCls: 'delete'
                });
	            if (parseInt(node.attributes.state) == 4){
	            	this.menu.add({
			            iconCls:'database-save',
			            text:Lars.main.tree.move_to_archive,
			            tooltip: Lars.main.tree.move_to_archive_tt,
			            id: 'add232453',
			            handler : function(){
	                		Ext.getCmp('main-tabs').archivePackage(node);
	                		this.root.reload();
			            },
			            scope: this
	            	})
	            }
            } else {
            	this.menu.add({
	            		text: Lars.main.tree.add_package,
	            		id: 'add34',
	            		handler: function(){
	            			this.win = new AssignmentWindow(node);
	            			this.win.show();
           					this.win.setZIndex(90000);
	                	},
	                	iconCls: 'folder-add'
	                },{
						text: Lars.main.tree.open_learn_process,
			            id: 'add23141',
			            handler : function(){
        					Ext.getCmp("topics-tree").el.mask(Lars.msg.loading, 'x-mask-loading');
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
									Ext.getCmp("topics-tree").el.unmask();
						        },
						        failure: function(){
						        	this.el.unmask();
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
											Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.main.tree.del_failure_msg_folder, 5);
										}
									}
				            	});
							}
					}, this);
	            	
	            	},
		            iconCls: 'delete'
                });
                if (packageNodeToCopy){
            	 if (packageNodeToCopy.attributes.iconCls.match("report")){
	            	this.menu.add({						
	            		text: Lars.main.tree.insert_here,
			            id: 'add23141972340233',
			            handler : function(){
			            	Ext.Msg.confirm(
								Lars.main.tree.copy_confirm_1,
								 Lars.main.tree.copy_confirm_2+' ('+packageNodeToCopy.attributes.text+') '+Lars.main.tree.copy_confirm_3+' '+node.attributes.text+' '+Lars.main.tree.copy_confirm_4, 
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
													newId = responseData.newId;
													newNode = new Ext.tree.TreeNode({
														id: newId, 
														text: packageNodeToCopy.text, 
														iconCls: packageNodeToCopy.attributes.iconCls,
														ownerTree: node.ownerTree,
														state: 0
														});
													newNode.attributes.state = 0;
													newNode.attributes.origName = responseData.origName;
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
              }
                
            }
        this.menu.showAt(e.getXY());
    },
    
    afterRender : function(){
        LarsTreePanelDesk.superclass.afterRender.call(this);
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