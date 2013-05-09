LarsPackageDiscussion = function(node, config, color) {
	this.groupColor = color;
	this.primaryKey = 'id';
	node.parentElement = node.id+'discussion-grid';			
	
	this.expander = new Ext.grid.RowExpander({
        tpl : new Ext.Template(
            '<p> {LARS_CONTENT}</p>'
        ),
		lazyRender : true,
		enableCaching: false
    }); 

	var myReader = new Ext.data.JsonReader(
		{
            root: 'messages',
            id: this.primaryKey
        },
            [
            this.primaryKey,
            'OBJ_NAME', 
            'OBJ_DESC', 
            'OBJ_AUTHOR', 
            {name:'DOC_LAST_MODIFIED', type:'date', dateFormat:'U'}, 
            'LARS_CONTENT'
            ]
        );
        
    this.store = new Ext.data.Store({
			proxy: new Ext.data.HttpProxy({
                url: 'lars_json.php', //url to server side script
                method: 'POST'
            }),   
            baseParams:{task: "getDiscussion",
            			id: node.id,
            			start: 0,
            			limit: 10
            			},
            reader: myReader,
			sortInfo:{field:'DOC_LAST_MODIFIED', direction:'DESC'}
            
    });
    this.store.setDefaultSort('DOC_LAST_MODIFIED', "DESC");
	this.store.on("load", function(){
		if (this.store.data.length < 1 && this.collapsible){
			this.collapse();
		}
	}, this);


    if (!node.isLeaf()){
	    this.bbar = new Ext.PagingToolbar({
	            pageSize: 10,
	            store: this.store,
	            displayInfo: true
	        });
    }
	this.tbar = [{
            iconCls:'comments-add',
            text:Lars.main.discussion.write_new_message,
            tooltip: Lars.main.discussion.write_new_message_tt,
			handler: function(){
				node.parentElement = node.id+'discussion-grid';			
    			this.win = new LarsMessageWindow(node, "newMessage");
    			this.win.show();
   				this.win.setZIndex(90000);
			},
				
            scope: this
		}
		,'->','-'
		,{            				
            iconCls:'icon-refresh',
            text:'',
            tooltip: Lars.refresh,
            handler: function(){
            	this.store.load();
            },
            scope: this
        },'-'
        ];


    this.columns = [{
        id: 'Titel',
        header: Lars.main.discussion.columns.title,
        dataIndex: 'OBJ_DESC',
        sortable:true,
        width: 300,
        renderer: this.formatTitle
      },{
        header: Lars.main.discussion.columns.author,
        dataIndex: 'OBJ_AUTHOR',
        width: 100,
        hidden: true,
        sortable:true
      },{
        header: Lars.main.discussion.columns.content,
        dataIndex: 'LARS_CONTENT',
        width: 10,
        hidden: true,
        sortable:false
      },{
        id: 'last',
        header: Lars.main.discussion.columns.date,
        dataIndex: 'DOC_LAST_MODIFIED',
        width: 150,
        renderer:  this.formatDate,
        sortable:true
    }];

    LarsPackageDiscussion.superclass.constructor.call(this, {
        title: Lars.main.discussion.title,
        iconCls: "comments",
        region: config.region,
        collapsible: config.collapsible,
        hideHeaders: true,
        collapseMode: 'mini',
        header: false,
        height: 200,
        border: false,
        id: node.id+'discussion-grid',
        loadMask: {msg:Lars.main.discussion.loadMask},

        sm: new Ext.grid.RowSelectionModel({
            singleSelect:true
        }),

        viewConfig: {
            forceFit:true,
            enableRowBody:true,
            showPreview:true,
            showAll:true,
            getRowClass : this.applyRowClass
        }
    });



	this.addEvents({schuelerEditTab: true});
    this.on('rowcontextmenu', this.onContextClick, this);
    this.on('rowdblclick', this.showContentWindow, this);
};
        
Ext.extend(LarsPackageDiscussion, Ext.grid.GridPanel, {
	onContextClick: function(grid, index, e){
		var row = grid.getView().getRow(index);
	    var record = grid.store.getAt(index);
	    var node1 = {};
	    node1.id = record.data.id;
	    node1.text = record.data.OBJ_DESC;
	    node1.parentElement = grid.id;
	        this.menu = new Ext.menu.Menu({
                id:'grid-discussion-ctx',
                items: [{
		            iconCls:'editPage',
		            text:Lars.main.discussion.menu.edit,
		            tooltip: Lars.main.discussion.menu.edit_tt,
			        handler : function(){
						this.win = new LarsMessageWindow(record, "editMessage");
		    			this.win.show();
		   				this.win.setZIndex(90000);
			        },
		            scope: this
				},{
		            iconCls:'delete',
		            text:Lars.main.discussion.menu.del,
		            tooltip: Lars.main.discussion.menu.del_tt,
		            handler: function(){
		            	Ext.Msg.confirm(
							Lars.main.discussion.menu.del_confirm_1,
							 Lars.main.discussion.menu.del_confirm_2+' '+ this.ctxRecord.data.OBJ_DESC + '', 
							function(btn){
								if (btn == 'yes'){
					            	Ext.Ajax.request({
						        		scope: this,
										url: 'lars_json.php',
										params: {id: this.ctxRecord.id,
									   			name: this.ctxRecord.data.OBJ_NAME,
									   			task: 'deleteItem'},
										success: function(response, options) {
											var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
											if (responseData.success){
												this.store.remove(this.store.getById(this.ctxRecord.id))
												Ext.ux.ToastLars.msg(Lars.main.discussion.menu.del_success_msg, "", 3);
											} else {
												Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.main.discussion.menu.del_failure_msg, 5);
											}
										}
					            	});
								}
						}, this);
		            	
		            	},
		            scope: this
        }]
            });
            this.menu.on('hide', this.onContextHide, this);
        e.stopEvent();
        if(this.ctxRow){
            Ext.fly(this.ctxRow).removeClass('x-node-ctx');
            this.ctxRow = null;
        }
        this.ctxRow = this.view.getRow(index);
        this.ctxRecord = this.store.getAt(index);
        Ext.fly(this.ctxRow).addClass('x-node-ctx');
        this.menu.showAt(e.getXY());
    },

    onContextHide : function(){
        if(this.ctxRow){
            Ext.fly(this.ctxRow).removeClass('x-node-ctx');
            this.ctxRow = null;
        }
    },

    togglePreview : function(show){
        this.view.showPreview = show;
        this.view.refresh();
    },

    applyRowClass: function(record, rowIndex, p, ds) {
        if (this.showPreview) {
            var xf = Ext.util.Format;
            if (this.showAll){
            	p.body = '<font size="2"><p>' + record.data.LARS_CONTENT + '</p>';
            } else {
            	p.body = '<p>' + xf.ellipsis(xf.stripTags(record.data.LARS_CONTENT), 300) + '</p>';	
            }
            return 'x-grid3-row-expanded';
        }
        return 'x-grid3-row-collapsed';
    },
    
    formatDate : function(date) {
    if (!date) {
        return '';
    }
    var now = new Date();
    var d = now.clearTime(true);
    var notime = date.clearTime(true).getTime();
    if (notime == d.getTime()) {
        return Lars.today+' ' + date.dateFormat('d-m-Y H:i');
    }
    d = d.add('d', -6);
    if (d.getTime() <= notime) {
        return date.dateFormat('d-m-Y H:i');
    }
    return date.dateFormat('d-m-Y H:i');
    },

    formatTitle: function(value, p, record) {
        return String.format(
                '<div class="comment"><b>{0}</b> <span class="author">'+Lars.main.discussion.created_of+' {1}</span></div>',
                value, record.data.OBJ_AUTHOR, record.id, record.data.forumid
                );
    }
});
