LarsTreePanel = function(node) {
    this.node = {};
    if (node){
	    this.node.id = node.id;
    } else {
    	this.node.id = 'root';
    }
    LarsTreePanel.superclass.constructor.call(this, {
        id: 'tree',
        region: 'center',
        width: 225,
        minSize: 175,
        maxSize: 400,
        margins: '0 0 0 0',
        ddGroup : 'resources',
        border: false,
        frame: true,
        collapseMode:'mini',
	    enableDD:true,
        dropConfig : {
		    appendOnly:true
		},
        rootVisible: false,
        lines: true,
        split: true,
        autoScroll: true,
        containerScroll: true,
        root: new Ext.tree.TreeNode({
                text: Lars.main.tree.main_directory,
                expanded: true 
            })
    }
    );
	this.root.appendChild([
		new Ext.tree.AsyncTreeNode({
	        text: Lars.main.tree.root_directory, 
	        loader: new Ext.tree.TreeLoader({
	        	dataUrl: 'lars_folder.php',
				baseParams: {task: "getRoot"}
				}),
			id: "root"
    	}), new Ext.tree.AsyncTreeNode({
	        text: Lars.main.tree.home_directory, 
	        loader: new Ext.tree.TreeLoader({
	        	dataUrl: 'lars_folder.php',
				baseParams: {task: "getRoot"}
				}),
			id: "home"
    	})
	]);
	new Ext.tree.TreeSorter(this, {folderSort: true});    
	this.expand();
	this.on('contextmenu', this.onContextMenu, this);
    this.on('render', function() {
		this.root.on({
					scope:this.el
					,beforeload:this.el.mask.createDelegate(this.el, [Lars.msg.loading_data])
					,load:this.el.unmask
					,loadexception:this.el.unmask
				});
	});	
	this.on({
		click: { 
			fn: function(node, a){
				node.toggle();
				}
		}
	});
	
};

Ext.extend(LarsTreePanel, Ext.tree.TreePanel, {
    onContextMenu : function(node, e){
		if (!node.isLeaf()){
            this.menu = new Ext.menu.Menu({
                id:'feeds-ctx',
                items: [{
                    text: Lars.main.tree.add_to_resources,
		            id: 'add13452',
		            handler : function(){
                		this.win = new LarsResourcesAddNameWindow(node);
                		this.win.show();
           				this.win.setZIndex(90000);
		            },
		            iconCls: 'link-add'
                }]
            });
        	this.menu.showAt(e.getXY());
       		this.menu.el.setZIndex(90010)
		}
    },
    afterRender : function(){
        LarsTreePanel.superclass.afterRender.call(this);
		this.el.on({
			contextmenu:{fn:function(){return false;},stopEvent:true}
		});
    }
    
});