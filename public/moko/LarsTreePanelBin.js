LarsTreePanelBin = function() {
    LarsTreePanelBin.superclass.constructor.call(this, {
        id: 'bin-tree',
        region: "center",
        margins: '0 0 0 0',
		bodyBorder: false,
		shim: false,
        rootVisible: false,
        lines: true,
        autoScroll: true,
        containerScroll: true,
        root: new Ext.tree.AsyncTreeNode({
                text: Lars.main.bin.trash, 
                loader: new Ext.tree.TreeLoader({
                	dataUrl: 'lars_folder.php',
					baseParams: {task: "getResources"}
					}),
				id: larsBinId
				
            }),
		tbar: [{
            iconCls:'bin-empty',
            text:'',
            tooltip: Lars.main.bin.empty_trash,
            handler: function(){
	        	Ext.Msg.confirm(
					Lars.main.bin.trash,
					 Lars.main.bin.empty_trash_confirm, 
					function(btn){
						if (btn == 'yes'){
			            	Ext.Ajax.request({
				        		scope: this,
								url: 'lars_json.php',
								params: {task: 'emptyTrash'},
								success: function(response, options) {
									var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
									if (responseData.success){
										Ext.ux.ToastLars.msg(Lars.msg.success, "", 2);
						            	Ext.getCmp('bin-tree').root.reload();
									} else {
										Ext.ux.ToastLars.msg(Lars.msg.failure, "", 2);
									}
								}
			            	});
						}
				}, this);
        	},
	            scope: this
            			},'->',{
            iconCls:'icon-refresh',
            text:'',
            tooltip: Lars.refresh,
            handler: function(){
            	Ext.getCmp('bin-tree').root.reload();
            }}]             
            
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
				if (node.isLeaf()){
                    window.open(node.attributes.lars_ref);
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

Ext.extend(LarsTreePanelBin, Ext.tree.TreePanel, {
    onContextMenu : function(node, e){
        this.menu = new Ext.menu.Menu({
            id:'feeds-ctx',
            items: [{
                    text: Lars.main.bin.delete_irrevocable,
		            id: 'add132443',
		            handler: function(){
	            	Ext.Msg.confirm(
						Lars.del,
						 Lars.main.bin.delete_confirm, 
						function(btn){
							if (btn == 'yes'){
				            	Ext.Ajax.request({
					        		scope: this,
									url: 'lars_json.php',
									params: {id: node.id,
								   			name: node.attributes.origName,
								   			task: 'deleteItem',
							   				irrevocable: "1"},
									success: function(response, options) {
										var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
										if (responseData.success){
											node.parentNode.reload();
										} else {
											Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.main.bin.del_failure_msg, 5);
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
		            iconCls:'copy',
		            text:Lars.copy_document,
		            tooltip: Lars.copy_document_tt,
			            handler : function(a,b, c, d){
			            	node.data = {OBJ_DESC : node.text};
			            	fileRecordToCopy = node; 
			            	packageNodeToCopy = false;
			            },
            		scope: this
        		});
        } else if (node.attributes.iconCls != "folder-link"){
            this.menu.add(
				{
                    text: Lars.main.tree.copy_folder,
		            id: 'add2334d54514561',
		            handler : function(){
		            	fileRecordToCopy = false; 
		            	packageNodeToCopy = node;
		            },
		            iconCls: 'copy'
            });
            }
        this.menu.showAt(e.getXY());
        this.menu.el.setZIndex(90010)
    },
   
    afterRender : function(){
        LarsTreePanelBin.superclass.afterRender.call(this);
		this.el.on({
			contextmenu:{fn:function(){return false;},stopEvent:true}
		});
    }
    
});