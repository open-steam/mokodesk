LarsTreePanelArchiv = function(node) {

    LarsTreePanelArchiv.superclass.constructor.call(this, {
			        id: "tree-"+node.id,
			        region: 'center',
			        rootVisible:false,
			        autoScroll:true,
			        border: false,
			        
					root: new Ext.tree.AsyncTreeNode({
			                text: 'Hauptverzeichnis', 
			                loader: new Ext.tree.TreeLoader({
                				loadMask: {msg: Lars.msg.loading_folder},
								dataUrl:'lars_json.php',
								baseParams: {	task: "getSchuelerTopics",
												folder: node.id}
							}),
							id:'source'
			        })      
			    });
    
	this.expand();
	this.addEvents({schuelerEditTab: true});
	this.on('contextmenu', this.onContextMenu, this);
	this.on({
		dblClick: { 
			fn: function(node, a){
				if (node.isLeaf()){
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


Ext.extend(LarsTreePanelArchiv, Ext.tree.TreePanel, {
    onContextMenu : function(node, e){
            this.menu = new Ext.menu.Menu({
				id: 'topics-tree-ctx',
				items: [{
					text: Lars.del,
					id: 'add1345752443',
					handler: function(){
						Ext.Msg.confirm(Lars.del, Lars.main.tree.del_confirm_object, function(btn){
							if (btn == 'yes') {
								Ext.Ajax.request({
									scope: this,
									url: 'lars_json.php',
									params: {
										id: node.id,
										name: node.attributes.origName,
										task: 'deleteItem'
									},
									success: function(response, options){
										var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
										if (responseData.success) {
											node.remove();
										}
										else {
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

            if (node.isLeaf()){
	            this.menu.add(
					{
						text: Lars.main.tree.show_package,
						id: 'add2311',
						handler: function(){
							Ext.getCmp('main-tabs').fireEvent('viewPackage', node);
						},
						iconCls: 'table-go'
					}, {
						text: Lars.main.tree.copy_package,
						id: 'add2334514561',
						handler: function(){
							packageNodeToCopy = node;
						},
						iconCls: 'copy'
					});
            }

			
        this.menu.showAt(e.getXY());
   		this.menu.el.setZIndex(90010)
    },
	
    afterRender : function(){
        LarsTreePanelArchiv.superclass.afterRender.call(this);
		this.el.on({
			contextmenu:{fn:function(){return false;},stopEvent:true}
		});
    }
});