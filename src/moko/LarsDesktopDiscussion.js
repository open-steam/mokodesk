LarsDesktopDiscussion = function(node, config) {
	this.primaryKey = 'id';
	node.parentElement = node.id+'discussion-grid';			
	
	this.expander = new Ext.grid.RowExpander({
        tpl : new Ext.Template(
            '<p> {LARS_CONTENT}</p>'
        ),
		lazyRender : true,
		enableCaching: false
    }); 

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
		,
		'->',{
            iconCls:'help',
            text:"",
            tooltip: Lars.main.help_and_news_tt,
			handler: function(){
				Ext.getCmp("lars-desktop-tab-panel").setActiveTab('about-panel');
			},
            scope: this
		}
        ];
    

	var myReader = new Ext.data.JsonReader(
		{
            root: 'messages',
            id: this.primaryKey,
	        totalProperty: 'totalCount',
	        remoteSort: true     
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
            			id: node.id
            			},
            reader: myReader,
			sortInfo:{field:'DOC_LAST_MODIFIED', direction:'DESC'}
            
    });

	this.store.on("load", function(){ 
	var count = 0;
			this.store.filterBy(function(rec) {
                var lastModified = rec.data.DOC_LAST_MODIFIED.getTime() / 1000;
				if (lastModified > lastLoginUnix){
					count++;
					return true;
				} else {
					return true;	
				}
			}, this);

			Ext.getDom("new-messages-count").innerHTML = "<b>"+count+"</b> ";
		}, this);

    this.store.on("add", function(b, c, d){
		// check whether the user is already on the discussion panel. Then there is no notification needed
        if (Ext.getCmp("lars-desktop-tab-panel").activeTab.id != larsDesktopId + 'discussion-grid' || Ext.getCmp("main-tabs").activeTab.id != "lars-desktop") {
            Ext.Ajax.request({
                url: 'lars_json.php',
                params: {
                    task: "getUserIcon",
                    name: c[0].data.OBJ_AUTHOR
                },
                success: function(response, options){
                    var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
                    imageUri = responseData.imageUri;
                    Ext.ux.ToastLarsDiscussion.msg(imageUri, '<font color="#578d00">' + c[0].data.OBJ_AUTHOR + '</font>: ' + c[0].data.OBJ_DESC, c[0].data.LARS_CONTENT, 10);
                },
                scope: this
            });
        }
    });
    this.store.setDefaultSort('DOC_LAST_MODIFIED', "DESC");

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
    this.bbar = new Ext.PagingToolbar({
            pageSize: 10,
            store: this.store,
            displayInfo: true
        });

    LarsDesktopDiscussion.superclass.constructor.call(this, {
        title: Lars.main.discussion.title,
        iconCls: "comments",
    	split: true,
		listeners: LarsViewer.LinkInterceptor,
//        region: 'center',
        frame: false,
        hideHeaders: true,
        height: "200",
        id: node.id+'discussion-grid',
        loadMask: {msg:Lars.main.discussion.loadMask},

        sm: new Ext.grid.RowSelectionModel({
            singleSelect:true
        }),
				view : new Ext.grid.GridView({
            forceFit:true,
            enableRowBody:true,
            showPreview:true,
            showAll:true,
            getRowClass : this.applyRowClass,
            emptyText: Lars.main.discussion.emptyText,
            deferEmptyText: true})
    });



	this.addEvents({schuelerEditTab: true});
    this.on('rowcontextmenu', this.onContextClick, this);
    this.on('rowdblclick', this.showContentWindow, this);
};
        
Ext.extend(LarsDesktopDiscussion, Ext.grid.GridPanel, {
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
//							 "ID: "+this.ctxRecord.id + '<br>'+
							 Lars.main.discussion.menu.del_confirm_2+' '+this.ctxRecord.data.OBJ_DESC + '', 
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
//        }
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

    // within this function "this" is actually the GridView
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
        return 'Heute ' + date.dateFormat('d-m-Y H:i');
    }
    d = d.add('d', -6);
    if (d.getTime() <= notime) {
        return date.dateFormat('d-m-Y H:i');
    }
    return date.dateFormat('d-m-Y H:i');
    },

    formatTitle: function(value, p, record) {
        return String.format(
                '<div class="comment"><b>{0}</b> <span class="author"> '+Lars.main.discussion.created_of+'{1}</span></div>',
                value, record.data.OBJ_AUTHOR, record.id, record.data.forumid
                );
    }
});
