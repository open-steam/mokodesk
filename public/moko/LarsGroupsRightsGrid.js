LarsGroupsRightsGrid = function() {
	this.primaryKey = 'id';

    this.tbar = [{
        iconCls:'group-add',
        text:Lars.dialog.groups.add_group,
        tooltip: Lars.dialog.groups.add_group_tt,
        handler: function(){
			this.win = new LarsGroupsTreePanelWindow();
			this.win.show();
			this.win.setZIndex(90001);
        },
        scope: this
    	
    },{
        iconCls:'user-add',
        text:Lars.dialog.groups.add_user,
        tooltip: Lars.dialog.groups.add_user_tt,
        handler: function(){
			this.win = new LarsGroupsAddUserWindow();
			this.win.show();
			this.win.setZIndex(90001);
        },
        scope: this
    	
    },'->',{
        iconCls:'icon-refresh',
        text:'',
        tooltip:Lars.refresh,
        handler: function(){
        	this.hideHeaders=false;
        	this.view.renderHeaders();
			Ext.getCmp("lars-rights-grid").store.load();
        },
        scope: this
    }];

	
    this.larsObj = Ext.data.Record.create([
			{name: "id"},
            {name: 'group', type: 'string'},
            {name: 'fav', type: 'string'},
            {name: 'text', type: 'string'},
            {name: 'ACCESS_READ', type: 'boolean'},
            {name: 'ACCESS_WRITE', type: 'boolean'}
    ]);
    
	// Json Reader that creates a JavaScript array from JSON response 
	var myReader = new Ext.data.JsonReader(
		{
            root: 'groups',
            id: this.primaryKey
        },
			this.larsObj
        );
        
    var store = new Ext.data.GroupingStore({
			proxy: new Ext.data.HttpProxy({
                url: 'lars_json.php',
                method: 'POST'
            }),   
            baseParams:{task: "getRightsGroups", id: "rights"},
            reader: myReader,
			sortInfo:{field:'text', direction:'ASC'},
			groupField:'group'
    });
    
    
	var fm = Ext.form;

    var checkColumn2 = new Ext.grid.CheckColumn({
       header: Lars.dialog.groups.allow_read,
       dataIndex: 'ACCESS_READ',
       width: 130
    });
    var checkColumn3 = new Ext.grid.CheckColumn({
       header: Lars.dialog.groups.allow_all,
       dataIndex: 'ACCESS_WRITE',
       width: 100
    });

	
	this.cm = new Ext.grid.ColumnModel([
    	{
           id:'id',
           header: "ID",
           dataIndex: this.primaryKey,
           width: 20,
           readOnly: true,
           sortable: true,
           hidden: true
		
		},{
           header: Lars.dialog.groups.groupname,
           width: 290,
           dataIndex: 'text',
		   sortable: true
		},
		checkColumn2,
		checkColumn3,
		{
           header: Lars.dialog.groups.favorite,
           width: 50,
           dataIndex: 'fav',
		   sortable: true
		},{
           header: Lars.dialog.groups.type,
           width: 50,
           hidden: true,
           dataIndex: 'group',
		   sortable: true
        }
    ]);
    
        
    LarsGroupsRightsGrid.superclass.constructor.call(this,{
		id: "lars-rights-grid",
        loadMask: {msg:Lars.msg.loading},
    	iconCls: 'table-go',
    	region: 'center',
		autoScroll: true,
        store: store,
        cm: this.cm,
        autoSizeColumns: true,
        frame:false,
		autoHeight : true,
        viewConfig: {forceFit: true},
        plugins:[
        	checkColumn2,
        	checkColumn3
        	],
		stripeRows:true,
        view: new Ext.grid.GroupingView({
            groupTextTpl: '{text}'
        })
    });
    this.on('afteredit', this.handleEdit, this); 
    this.on('rowcontextmenu', this.onContextClick, this);
	
};
        
Ext.extend(LarsGroupsRightsGrid, Ext.grid.EditorGridPanel, {

    updateDB : function (oGrid_Event) {
            
            Ext.Ajax.request( 
                {   
                	scope: this,
                    url: 'lars_json.php', 
					params: { 
                        task: "updateGroup",
                        key: this.primaryKey,
                        keyValue: oGrid_Event.record.data.id,
                        id: oGrid_Event.record.data.id,
                        field: oGrid_Event.field,//the column name
                        fieldValue: oGrid_Event.value,//the updated value
                        originalValue: oGrid_Event.record.modified
                    	},
                    failure:function(response,options){
                        Ext.MessageBox.alert(Lars.msg.warning,Lars.msg.failure_connection);
                        this.store.rejectChanges();//undo any changes
                    },      
                    success:function(response,options){
						var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
						if (responseData.success == true){
					        Ext.ux.ToastLars.msg(Lars.msg.success_changed_data, responseData.name ? responseData.name : " ", 3);
							this.store.commitChanges();
						}else{
							Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.msg.failure_nothing_changed+' <br>'+responseData.name, 4);
	                        this.store.rejectChanges();//undo any changes
						}
	                }//end success block                                      
                 }//end request config
            ); //end request  
        }, //end updateDB

		handleEdit: function (editEvent) {
			if (editEvent){
			var gridField = editEvent.field;
				this.updateDB(editEvent);
			}else{
				Ext.Msg.alert(Lars.msg.failure_nothing_changed);
			}
		},
		
	onContextClick: function(grid, index, e){
		var row = grid.getView().getRow(index);
	    var record = grid.store.getAt(index);
        this.menu = new Ext.menu.Menu({
                id:'grid-ctx'
            });
//TODO: "Ja" als Kondition nicht ausreichend!
		if (record.data.fav == "Ja"){
		this.menu.add({
		            iconCls:'delete',
		            text:Lars.del,
		            tooltip: Lars.dialog.groups.del_group_user_1,
		            handler: function(){
		            	Ext.Msg.confirm(
							Lars.dialog.groups.del_group_user_2,
							Lars.dialog.groups.del_group_user_3, 
							function(btn){
								if (btn == 'yes'){
					            	Ext.Ajax.request({
						        		scope: this,
										url: 'lars_json.php',
										params: {id: record.id,
									   			name: record.text,
									   			task: 'deleteBuddy'},
										success: function(response, options) {
											var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
											if (responseData.success){
												grid.store.remove(record);
											} else {
												Ext.ux.ToastLars.msg(Lars.msg.failure, "---", 5);
											}
										}
					            	});
								}
						}, this);
	            		},
            		scope: this}
        		);
		}

            this.menu.on('hide', this.onContextHide, this);
        if(this.ctxRow){
            Ext.fly(this.ctxRow).removeClass('x-node-ctx');
            this.ctxRow = null;
        }
        this.ctxRow = this.view.getRow(index);
        this.ctxRecord = this.store.getAt(index);
        Ext.fly(this.ctxRow).addClass('x-node-ctx');
        this.menu.showAt(e.getXY());
   		this.menu.el.setZIndex(90010)
    },

    onContextHide : function(){
        if(this.ctxRow){
            Ext.fly(this.ctxRow).removeClass('x-node-ctx');
            this.ctxRow = null;
        }
    },
    afterRender : function(){
        LarsGroupsRightsGrid.superclass.afterRender.call(this);
		this.el.on({
			contextmenu:{fn:function(){return false;},stopEvent:true}
		});
    }    
			
});
