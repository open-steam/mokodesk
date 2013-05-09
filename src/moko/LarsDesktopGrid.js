LarsDesktopGrid = function(region) {
	// primary key for elements in the editor
	this.primaryKey = 'id';
	
    this.tbar = [{
				text : Lars.main.retrospection, //TODO: Sprachen
				menu : [{
							text : Lars.main.hours,
							handler : function() {
								this.changeTime(24);
							},
							scope : this

						}, {
							text : Lars.main.week,
							handler : function() {
								this.changeTime(168);
							},
							scope : this
						}],
				scope : this
			}, '->', {
				iconCls : 'icon-refresh',
				text : '',
				tooltip : Lars.refresh,
				handler : function() {
					this.hideHeaders = false;
					this.view.renderHeaders();
					Ext.getCmp("lars-desktop-grid").store.load();
				},
				scope : this
			}, '-'];

	this.action = new Ext.ux.grid.RowActions({
		 header:''
		,actions:[{
			 iconIndex:'action1'
			,qtipIndex:'qtip1'
			,hideIndex:'hide1'
			,iconCls:'whiteIcon'
			,tooltip:''
		},{
			 iconIndex:'action3'
			,qtipIndex:'qtip3'
			,hideIndex:'hide3'
			,iconCls:'whiteIcon'
			,tooltip:''
		}]
	});

	this.action.on('action', this.onRowAction, this);

 	this.actionFileTypeDesktop = new Ext.ux.grid.RowActions({
		 header:''
		,actions:[{
			 iconIndex:'action0'
			,qtipIndex:'qtip0'
			,iconCls:'file-dll'
		},{
			 iconCls:'whiteIcon'
			,qtipIndex:'qtip2'
			,hideIndex:'hide2'
			,iconIndex:'action2'
		},{
			 iconCls:'whiteIcon'
			,qtipIndex:'qtip5'
			,hideIndex:'hide5'
			,iconIndex:'action5'
		}]
	});

	
	this.expander = new Ext.grid.RowExpander({
	  tpl: '<div id=\"desktop-grid-{id}\"></div>'
	});
	
	this.expander.on('expand', 
	    function(ct, record, body, rowIndex){
	      switch (record.data.action2){
				case 'page-save':
				      var panel = new Ext.Panel({
						id:'desktop-grid-row-'+record.data.id,
						html:record.data.LARS_CONTENT,
				        autoHeight: true,
				        renderTo: 'desktop-grid-'+record.data.id
				      });
					break;
				case 'editPage':
				      var panel = new Ext.ux.ManagedIframePanel({
						id:'desktop-grid-row-'+record.data.id,
						defaultSrc: "tools/get.php?object="+record.data.id,
				        loadMask: true,
						listeners: {domready:LarsViewer.LinkInterceptorIFrame},
				        renderTo: 'desktop-grid-'+record.data.id
				      });
				      panel.on("documentloaded", function(){
				      	var iFrame = panel.getFrame();
				      	iFrame.setHeight(iFrame.getBody().scrollHeight);
				      }, this);
		    		break;
			}

	    });	
	
	// definition of the record structure
    this.larsObj = LarsGridConfig.larsObj;
    
    // store for the states of the Tasks and to show the names instead of the numbers
    var dsState = LarsGridConfig.dsState;

    var dsType = LarsGridConfig.dsType;
        
	// Json Reader that creates a JavaScript array from JSON response 
	var myReader = new Ext.data.JsonReader(
		{
            root: 'newItems',
            id: this.primaryKey
        },
			this.larsObj
        );
        
	// store for all the tasks
    var store = new Ext.data.GroupingStore({
			proxy: new Ext.data.HttpProxy({
                url: 'lars_json.php', //url to server side script
                method: 'POST',
	            timeout: 120000
            }),   
            baseParams:{task: "getNewItems"},
            reader: myReader,
			groupField:'LARS_FOLDER',
			sortInfo:{field:'OBJ_DESC', direction:'ASC'}
            
    }); 
    store.on("loadexception", function(a, b, c){
		Ext.getDom("new-documents-count").innerHTML = "<b>(?)</b> ";
//		Ext.getDom("new-messages-count").innerHTML = "<b>(?)</b> ";
		Ext.getDom("new-messages-documents-count").innerHTML = "<b>(?)</b> ";
		Ext.getDom("new-documents-other-count").innerHTML = "<b>(?)</b> ";
		Ext.getDom("new-messages-other-count").innerHTML = "<b>(?)</b> ";
    	Ext.ux.ToastLars.msg(Lars.main.desktop_grid.timeout_msg_1, Lars.main.desktop_grid.timeout_msg_2, 5);
    }, this); 
/*
 * This resets the filter even when a project is
 * selected, but it is an alternative to callback
 * function
 */
//	store.on("load", function(){
//		var stateFilter =new Array();
//		stateFilter.lt = 5;
//		mainPanel.grid.filters.getFilter("GTD_STATE").setValue(stateFilter);
//	})
//	store.load({
//		callback : function(){ // to set the state filter and hide finished tasks
//				var stateFilter =new Array();
//				stateFilter.lt = 4;
////				mainPanel.grid.filters.getFilter("GTD_STATE").setValue(stateFilter);
//			}
//	});
	store.on("load", function(){ // Maybe there is a better solution to count!
		var desktopGrid = Ext.getCmp("lars-desktop-grid");
		count = 0;
		desktopGrid.store.filterBy(function(rec) {
                var tA = rec.data.OBJ_TYPE;
				var reA = new RegExp("LARS_MESSAGES", 'gi');
                var tB = rec.data.is_home;
				var reB = new RegExp("true", 'gi');
				if (!reA.test(tA) && reB.test(tB)){
					count++;
					return true;
				} else {
					return false;	
				}
			}, this);
		Ext.getDom("new-documents-count").innerHTML = " <b>"+count+"</b>";
		count = 0;
		desktopGrid.store.filterBy(function(rec) {
                var tA = rec.data.OBJ_TYPE;
				var reA = new RegExp("LARS_MESSAGES", 'gi');
                var tB = rec.data.is_home;
				var reB = new RegExp("true", 'gi');
				if (reA.test(tA) && !reB.test(tB)){
					count++;
					return true;
				} else {
					return false;	
				}
			}, this);
		Ext.getDom("new-messages-other-count").innerHTML = "<b>"+count+"</b> ";
		count = 0;
		desktopGrid.store.filterBy(function(rec) {
                var tA = rec.data.OBJ_TYPE;
				var reA = new RegExp("LARS_MESSAGES", 'gi');
                var tB = rec.data.is_home;
				var reB = new RegExp("true", 'gi');
				if (!reA.test(tA) && !reB.test(tB)){
					count++;
					return true;
				} else {
					return false;	
				}
			}, this);
		Ext.getDom("new-documents-other-count").innerHTML = "<b>"+count+"</b> ";
		count = 0;
		desktopGrid.store.filterBy(function(rec) {
                var tA = rec.data.OBJ_TYPE;
				var reA = new RegExp("LARS_MESSAGES", 'gi');
                var tB = rec.data.is_home;
				var reB = new RegExp("true", 'gi');
				if (reA.test(tA) && reB.test(tB)){
					count++;
					return true;
				} else {
					return false;	
				}
			}, this);
		Ext.getDom("new-messages-documents-count").innerHTML = "<b>"+count+"</b> ";
		Ext.getCmp("lars-desktop-tab-panel").setActiveTab('about-panel');

	}, this);

	var fm = Ext.form;
	
	// Column Model is defined for the data in the store
	this.cm = new Ext.grid.ColumnModel([
    	this.expander,
    	{
           id:'id',
           header: "ID",
           dataIndex: this.primaryKey,
           width: 20,
           readOnly: true,
           sortable: true,
           hidden: true
		
        }
        ,this.actionFileTypeDesktop
        ,{
           header: Lars.main.grid.columns.desc,
           dataIndex: 'OBJ_DESC',
		   sortable: true, 
           editor: new fm.TextField({
               allowBlank: false
           })
        },{
           header: Lars.main.grid.columns.content,
           dataIndex: 'LARS_CONTENT',
           readOnly: true,
           hidden: true,
           width: 40
        },{
           header: Lars.main.grid.columns.folder,
           dataIndex: 'LARS_FOLDER',
		   hidden: true,
           readOnly: true,
		   sortable: true, 
           width: 100
        },{
			header: Lars.main.grid.columns.type,
			dataIndex: 'LARS_TYPE',
			sortable: true,
			fixed: true,
			width: 80,
	        hidden: true,
			//create a dropdown based on store date dsState
			editor: new Ext.form.ComboBox({ 
				mode: 'local',
				editable: false,
				triggerAction: 'all',
				store: dsType,
				displayField: 'text',
				valueField: 'id',
				valueNotFoundText: Lars.main.grid.columns.valueNotFoundText
			}),
			renderer:  //custom rendering specified inline
				function(data, metadata) {
					if (metadata){
						metadata.css = LarsGridConfig.renderCell(data);	
					}
					record = dsType.getById(data);
					if(record) {
						return record.data.text;
					} else {
						return '';
					}
				}
        },{
           header: Lars.main.grid.columns.last_changed,
           dataIndex: 'OBJ_LAST_CHANGED',
           width: 100,
           fixed: true,
           readOnly: true,
           sortable: true,
           renderer: function(value){
           		return value.dateFormat('d-m-Y H:i');
           }
        },{
           header: Lars.main.grid.columns.created,
           dataIndex: 'OBJ_CREATION_TIME',
           readOnly: true,
           width: 100,
           hidden: true,
		   sortable: true, 
           renderer: function(value){
	           	return value.dateFormat('d-m-Y H:i');
           }
        },
        this.action
    ]);
        
        
// ########################################################################        
    LarsDesktopGrid.superclass.constructor.call(this,{
//        region : 'south',
        collapsible: true,
        tabTip: Lars.main.desktop_grid.tabTip,
        loadMask: {msg:Lars.main.desktop_grid.loadMask},
		id: "lars-desktop-grid",
        hideHeaders: true,
        frame: false,
		height: 200,
    	title: Lars.main.desktop_grid.title,
    	titleBeginning : Lars.main.desktop_grid.titleBeginning,
    	iconCls: 'table-go',
    	split: true,
		autoScroll: true,
        store: store,
        cm: this.cm,
        autoSizeColumns: true,
		autoExpandColumn:'ID',
		clicksToEdit:1,
		listeners: LarsViewer.LinkInterceptor,
		stripeRows:true,
		margins: '0 0 0 0',
		cmargins: '0 0 0 0',
        plugins: [this.expander,
        			this.action,this.actionFileTypeDesktop,        
		             new Ext.ux.grid.Search({
		             mode:'local'
		            ,align: 'right'
		            ,position: 'top'
		            ,iconCls:false
		            ,width: 150
		            ,dateFormat:'d-m-Y H:i'
		            ,minLength:2})
		        ],
        view: new Ext.grid.GroupingView({
        	emptyText: Lars.main.grid.emptyText,
        	deferEmptyText: true,
        	forceFit:true,
            groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "'+Lars.main.item_p+'" : "'+Lars.main.item_s+'"]})'
        })
		
		        
    	
    });
	this.addEvents({schuelerEditTab: true});
	this.addEvents({refreshTab: true});

    this.on('rowcontextmenu', this.onContextClick, this);
    this.on('afteredit', this.handleEdit, this); 
    this.on('beforeadd', this.handleEdit, this); 
	
};
        
Ext.extend(LarsDesktopGrid, Ext.grid.EditorGridPanel, {
	onContextClick: function(grid, index, e){
		var row = grid.getView().getRow(index);
	    var record = grid.store.getAt(index);
	    var node1 = {};
	    node1.id = record.data.id;
	    node1.text = record.data.OBJ_DESC;
            this.menu = new Ext.menu.Menu({
                id:'grid-ctx'
            });
            if (record.data.action2 == "editPage")
				this.menu.add({
			            iconCls:'editPage',
			            text:Lars.edit,
			            tooltip: Lars.main.grid.edit_tt,
				        handler : function(){
				    		Ext.getCmp('main-tabs').fireEvent('schuelerEditTab', node1);
				        },
			            scope: this
					});
			this.menu.add({
		            iconCls:'delete',
		            text:Lars.del,
		            tooltip: Lars.main.grid.del_tt,
		            handler: function(){
		            	Ext.Msg.confirm(
							Lars.main.grid.del_confirm,
							 node1.text + '', 
							function(btn){
								if (btn == 'yes'){
					            	Ext.Ajax.request({
						        		scope: this,
										url: 'lars_json.php',
										params: {id: node1.id,
									   			name: node1.text,
									   			task: 'deleteItem'},
										success: function(response, options) {
											var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
											if (responseData.success){
												grid.store.remove(record);
											} else {
												Ext.ux.ToastLars.msg(Lasr.msg.failure, Lars.main.grid.del_failure_msg, 5);
											}
										}
					            	});
								}
						}, this);
	            		},
            		scope: this}
        		);

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
	
    updateDB : function (oGrid_Event) {
            
			if (oGrid_Event.value instanceof Date)
			{
				var fieldValue = oGrid_Event.value.format('U');
			} else
			{
				var fieldValue = oGrid_Event.value;
			}	
					
			//submit to server
            Ext.Ajax.request( 
                {   
                	scope: this,
                    url: 'lars_json.php', 
					params: { 
                        task: "update",
                        key: this.primaryKey,
                        keyValue: oGrid_Event.record.data.id,
                        id: oGrid_Event.record.data.id,
                        field: oGrid_Event.field,//the column name
                        fieldValue: fieldValue,//the updated value
                        originalValue: oGrid_Event.record.modified
                    	},
                    failure:function(response,options){
                        Ext.MessageBox.alert(Lars.msg.warning, Lars.msg.failure_connection);
                        this.store.rejectChanges();//undo any changes
                    },//end failure block      
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
		onRowAction:function(grid, rec, action, row, col) {
			var node2 = {
				id: rec.data.id,
				text: rec.data.OBJ_DESC,
				attributes: {
					iconCls: action,
					lars_ref : rec.data.LARS_CONTENT
				}
				};
			switch(action) {
				case 'cut':
					copy3(rec.data.OBJ_PATH);
				break;
				case 'add-page':	
		    		this.win = new UploadWindow(this.node, 2, rec.data.OBJ_DESC+""+Lars.main.grid.solution_attached);
		    		this.win.show();
       				this.win.setZIndex(90000);
				break;
				case 'icon-edit-record':
				break;
				case 'comment-edit':
				case 'comments':	
		    		this.win = new LarsCommentWindow(rec);
		    		this.win.show();
       				this.win.setZIndex(90000);
				break;
				case 'file-link':
					if (node2.attributes.lars_ref.match(window.location.host)){
						LarsViewer.QuestionTabOrBrowserOpen(node2);
					} else {
						LarsViewer.QuestionBrowserOpenNode(node2);                    
					}
					break;
				case 'page-save':
					window.open("tools/get.php?object="+rec.data.id);
					break;
				case 'editPage':
		    		Ext.getCmp('main-tabs').fireEvent('schuelerEditTab', node2);
		    		break;
				case 'tab-go':
		    		Ext.getCmp('main-tabs').fireEvent('viewIFrameTab', node2);
		    		break;
				case 'delete':
	            	Ext.Msg.confirm(
						Lars.main.grid.del_confirm,
						 rec.data.OBJ_DESC + '', 
						function(btn){
							if (btn == 'yes'){
				            	Ext.Ajax.request({
					        		scope: this,
									url: 'lars_json.php',
									params: {id: rec.id,
								   			name: rec.data.OBJ_NAME,
								   			task: 'deleteItem'},
									success: function(response, options) {
										var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
										if (responseData.success){
											grid.store.remove(rec);
										} else {
											Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.main.grid.del_failure_msg, 5);
										}
									}
				            	});
							}
					}, this);
					break;
			}
			if (action.match("file")){
				switch(rec.data.action2) {
					case 'file-gif':
					case 'file-tiff':
					case 'file-jpeg':
					case 'file-jpg':
					case 'file-png':
					case 'file-bmp':
					case 'editPage':
						this.expander.toggleRow(row);
			    		break;
					case 'pdf' :
						node2.attributes = {lars_ref : "tools/get.php?object="+node2.id, iconCls: node2.attributes.iconCls};
						LarsViewer.QuestionBrowserOpenPDF(node2);
						break;
					case 'page-save':
						switch(action) {
							case 'file-gif':
							case 'file-tiff':
							case 'file-jpeg':
							case 'file-jpg':
							case 'file-png':
							case 'file-bmp':
							case 'file-pdf':
								this.expander.toggleRow(row);
								break;
							default:
					        	Ext.Msg.confirm(
									Lars.main.grid.download_1,
									Lars.main.grid.download_2,
									function(btn){
										if (btn == 'yes'){
											LarsGridConfig.downloadFile("tools/get.php?object="+rec.data.id);
										}
								}, this);
								break;
						}
						break;
				}
			}
		}, // eo onRowAction
		
		changeState: function(state, node){
	        Ext.Ajax.request({
	            url: 'lars_json.php',
	            params: {	
	        				task: "changeState",
	        				id: node.id,
	            			state: state
	            		},
	            success: function(){
	            	Ext.getCmp('topics-tree').root.reload();
				},
	            scope: this
	        });
		},
		changeTime : function(time) {
			this.store.load({
					params : {
						task : "getNewItems",
						time : time
					}
				});
		}
});
