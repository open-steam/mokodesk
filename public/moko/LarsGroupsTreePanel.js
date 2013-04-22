LarsGroupsTreePanel = function() {

    LarsGroupsTreePanel.superclass.constructor.call(this, {
			        id: "groups-tree",
			        region: 'center',
			        rootVisible:false,
			        autoScroll:true,
					root: new Ext.tree.AsyncTreeNode({
			                text: Lars.dialog.groups.root_directory, 
			                loader: new Ext.tree.TreeLoader({
								dataUrl:'lars_json.php',
								baseParams: {task: "getGroupsTree"}
							}),
							id:'source'
			        })
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
	this.on('contextmenu', this.onContextMenu, this);
	
};


Ext.extend(LarsGroupsTreePanel, Ext.tree.TreePanel, {
    onContextMenu : function(node, e){
        this.menu = new Ext.menu.Menu({
            id:'topics-tree-ctx',
            items: [{
                text: Lars.dialog.groups.add_group_to_list,
	            id: 'add2312341',
	            handler : function(){
		            Ext.Ajax.request( 
		                {   
		                	scope: this,
		                    url: 'lars_json.php', 
							params: { 
		                        task: "addBuddy",
		                        id: node.id
		                    	},
		                    failure:function(response,options){
                        		Ext.MessageBox.alert(Lars.msg.warning,Lars.msg.failure_connection);
		                    },//end failure block      
		                    success:function(response,options){
								var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
								if (responseData.success == true){
					       			Ext.ux.ToastLars.msg(Lars.msg.success_changed_data, responseData.name ? responseData.name : " ", 3);
									Ext.getCmp("lars-rights-grid").store.load();
								}else{
									Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.msg.failure_nothing_changed+' <br>'+responseData.name, 4);
								}
			                }//end success block                                      
		                 }//end request config
		            ); //end request 
	            },
	            iconCls: 'group-add'
            }]
        });
        this.menu.showAt(e.getXY());
        this.menu.el.setZIndex(90010)
    },
    afterRender : function(){
        LarsTreePanelFolderLinks.superclass.afterRender.call(this);
		// prevent default browser context menu to appear 
		this.el.on({
			contextmenu:{fn:function(){return false;},stopEvent:true}
		});
    }
});