LarsTreePanelLinks = function() {
    LarsTreePanelLinks.superclass.constructor.call(this, {
        id: 'links-tree',
        margins: '0 0 0 0',
		bodyBorder: false,
		shim: false,
		ddGroup: "TreeDD",
        animate: false,
		enableDrag: true,
        rootVisible: false,
        lines: true,
        autoScroll: true,
        containerScroll: true,
        root: new Ext.tree.AsyncTreeNode({
                text: '', 
                loader: new Ext.tree.TreeLoader({
                	dataUrl: 'lars_folder.php',
					baseParams: {task: "getResourcesLinks"}
					}),
				id:'links'
            }),
		tbar: [{
            iconCls:'link-add',
            text:'',
            tooltip: Lars.main.tree.add_link,
            handler: function(){ 
            			this.win = new LarsAddLinkWindow();
           				this.win.show();
           				this.win.setZIndex(90000);
					},
            scope: this
            			},{
            iconCls:'folder-add',
            text:'',
            tooltip: Lars.main.tree.add_folder,
            handler: function(){ 
            			this.win = new LarsAddFolderLinksWindow("root");
           				this.win.show();
           				this.win.setZIndex(90000);
					},
            scope: this
            			},'->',{
            iconCls:'icon-refresh',
            text:'',
            tooltip: Lars.refresh,
            handler: function(){
            	Ext.getCmp('links-tree').root.reload();
            }}]             
    }
    );
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
				if (node.isLeaf() && node.attributes.lars_ref.match(window.location.host)){
					LarsViewer.QuestionTabOrBrowserOpen(node);
				} else {
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
	
	
};

Ext.extend(LarsTreePanelLinks, Ext.tree.TreePanel, {
    onContextMenu : function(node, e){
        if (!node.isLeaf()){
	            this.menu = new Ext.menu.Menu({
	                id:'feeds-ctx',
	                items: [{
	                    text: Lars.main.tree.add_link_here,
			            id: 'add142365',
			            handler : function(){
	            			this.win = new LarsAddLinkWindow(node);
	           				this.win.show();
           					this.win.setZIndex(90000);
			            },
			            iconCls: 'link-add'
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
                    text: 'Löschen',
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
											Ext.getCmp('links-tree').root.reload();
										} else {
											Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.main.tree.del_failure_msg_folder, 5);
										}
									}
				            	});
							}
					}, this);
	            	
	            	},
		            iconCls: 'delete'
                }]
	            });
        }
        if (node.isLeaf()){
            this.menu = new Ext.menu.Menu({
                id:'feeds-ctx',
                items: [{
                    text: Lars.del,
		            id: 'add13243',
		            handler: function(){
	            	Ext.Msg.confirm(
						Lars.del,
						 Lars.main.tree.del_link, 
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
											Ext.getCmp('links-tree').root.reload();
										} else {
											Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.main.tree.del_failure_msg_link, 5);
										}
									}
				            	});
							}
					}, this);
	            	
	            	},
		            iconCls: 'delete'
                }]
            });
        }
        this.menu.add({
            text: Lars.rename,
            id: 'add2314561',
            handler : function(){
            	this.win = new LarsChangeDescWindow(node);
            	this.win.show();
   				this.win.setZIndex(90000);
            },
            iconCls: 'rename'
        })
        this.menu.showAt(e.getXY());
        
    },
   
    afterRender : function(){
        LarsTreePanelLinks.superclass.afterRender.call(this);
		this.el.on({
			contextmenu:{fn:function(){return false;},stopEvent:true}
		});
    }
    
});