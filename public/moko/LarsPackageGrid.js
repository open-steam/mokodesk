var larsObj;

PackageGrid = function(parentNode, groupColor) {
	this.groupColor = groupColor;
	// primary key for elements in the editor
	this.primaryKey = 'id';
	parentNode.parentElement = parentNode.id + 'Grid';

	this.stateAction = new Ext.Action({
				tooltip : Lars.main.grid.change_state,
				iconCls : this.getStateIcon(parentNode.attributes.state),
				menu : [{
							iconCls : "redIcon",
							group : 'lars-state',
							text : Lars.main.grid.state_0,
							handler : function() {
								this.changeState(0, this.node);
							},
							scope : this
						}, {
							iconCls : "orangeIcon",
							group : 'lars-state',
							text : Lars.main.grid.state_1,
							handler : function() {
								this.changeState(1, this.node)
							},
							scope : this
						}, {
							iconCls : "greenIcon",
							group : 'lars-state',
							text : Lars.main.grid.state_2,
							handler : function() {
								this.changeState(2, this.node)
							},
							scope : this
						}, {
							iconCls : "blueIcon",
							group : 'lars-state',
							text : Lars.main.grid.state_3,
							handler : function() {
								this.changeState(3, this.node)
							},
							scope : this
						}, {
							iconCls : "yellowIcon",
							group : 'lars-state',
							text : Lars.main.grid.state_4,
							handler : function() {
								this.changeState(4, this.node)
							},
							scope : this
						}],
				scope : this
			});

	if (parentNode.isLeaf()) {
		this.tobar = new Ext.Toolbar([this.stateAction, {
					iconCls : 'add-page',
					text : Lars.main.grid.add_document_title,
					tooltip : Lars.main.grid.add_document,
					handler : function() {
						this.win = new UploadWindow(this.node);
						this.win.show();
						this.win.setZIndex(90000);
					},
					scope : this
				}, {
					iconCls : 'add-page-white',
					text : Lars.main.grid.add_text_title,
					tooltip : Lars.main.grid.add_text,
					handler : function() {
						this.win = new LarsHtmlTextWindow(this.node);
						this.win.show();
						this.win.setZIndex(90000);
					},
					scope : this
				},{
					text : "Dokument hier einfügen", //TODO: Sprachen
					id : 'add23141494723340233',
					handler : function() {
						if (!fileRecordToCopy) {
							Ext.Msg.alert("Fehler", "Kein Dokument in der Zwischenablage vorhanden")
						}
						else {
							Ext.Msg.confirm(Lars.main.tree.copy_confirm_1, Lars.main.tree.copy_confirm_2_document +
							fileRecordToCopy.data.OBJ_DESC +
							Lars.main.tree.copy_confirm_3_in_package, function(btn){
								if (btn == 'yes') {
									Ext.Ajax.request({
										url: 'lars_json.php',
										params: {
											task: "copyFile",
											sourceId: fileRecordToCopy.id,
											targetId: parentNode.id
										},
										success: function(response, options){
											var responseData = Ext.util.JSON.decode(response.responseText);
											if (responseData.success) {
												Ext.ux.ToastLars.msg(Lars.msg.success_copy, responseData.name ? responseData.name : " ", 3);
											}
											else {
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
						}
					},
					iconCls : "paste"
				}, '->',{
					iconCls : 'icon-refresh',
					text : '',
					tooltip : Lars.refresh,
					handler : function() {
						this.getView().getRowClass = LarsGridConfig.applyRowClassWithout;
						Ext.getCmp(this.node.id + "Grid").store.load();
					},
					scope : this
				}]);
	} else {
		this.tobar = new Ext.Toolbar([{
					iconCls : 'add-page',
					text : Lars.main.grid.add_document_title,
					tooltip : Lars.main.grid.add_document,
					handler : function() {
						this.win = new UploadWindow(this.node);
						this.win.show();
						this.win.setZIndex(90000);
					},
					scope : this
				}, {
					iconCls : 'add-page-white',
					text : Lars.main.grid.add_text_title,
					tooltip : Lars.main.grid.add_text,
					handler : function() {
						this.win = new LarsHtmlTextWindow(this.node);
						this.win.show();
						this.win.setZIndex(90000);
					},
					scope : this
				}, '->',
				{
					iconCls : 'icon-refresh',
					text : '',
					tooltip : Lars.refresh,
					handler : function() {
						this.getView().getRowClass = LarsGridConfig.applyRowClassWithout;
						Ext.getCmp(this.node.id + "Grid").store.load();
					},
					scope : this
				}]);
	}

	this.actionPackage = new Ext.ux.grid.RowActions({
				header : '',
				actions : [{
							iconIndex : 'action1',
							qtipIndex : 'qtip1',
							hideIndex : 'hide1',
							iconCls : 'whiteIcon',
							tooltip : ''
						}, {
							iconIndex : 'action3',
							qtipIndex : 'qtip3',
							hideIndex : 'hide3',
							iconCls : 'whiteIcon',
							tooltip : ''
						}, {
							iconIndex : 'action4',
							qtipIndex : 'qtip4',
							hideIndex : 'hide4',
							iconCls : 'whiteIcon',
							tooltip : ''
						}]
			});

	this.actionPackage.on('action', this.onRowActionPackage, this);

	this.actionFileType = new Ext.ux.grid.RowActions({
				header : '',
				actions : [{
							iconIndex : 'action0',
							qtipIndex : 'qtip0',
							iconCls : 'file-dll'
						}, {
							iconCls : 'whiteIcon',
							qtipIndex : 'qtip2',
							hideIndex : 'hide2',
							iconIndex : 'action2'
						}, {
							iconCls : 'whiteIcon',
							qtipIndex : 'qtip5',
							hideIndex : 'hide5',
							iconIndex : 'action5'
						}]
			});

	this.expander = new Ext.grid.RowExpander({
				tpl : '<div id=\"desktop-grid-{id}\"></div>'
			});

	this.expander.on('expand', function(ct, record, body, rowIndex) {
				switch (record.data.action2) {
					case 'page-save' :
						var panel = new Ext.Panel({
									id : 'desktop-grid-row-' + record.data.id,
									html : record.data.LARS_CONTENT,
									autoHeight : true,
									renderTo : 'desktop-grid-' + record.data.id
								});
						record.panel = panel;
						break;
					case 'editPage' :
					case 'pdf':
						var panel = new Ext.ux.ManagedIframePanel({
									id : 'desktop-grid-row-' + record.data.id,
									defaultSrc : "tools/get.php?object="
											+ record.data.id,
									listeners : {
										domready : LarsViewer.LinkInterceptorIFrame
									},
									loadMask : true,
									renderTo : 'desktop-grid-' + record.data.id
								});
						panel.on("documentloaded", function() {
									var iFrame = panel.getFrame();
									iFrame
											.setHeight(iFrame.getBody().scrollHeight);
								}, this);
						record.iFrame = panel;
						break;
				}

			});
	// definition of the record structure
	this.larsObj = LarsGridConfig.larsObj;

	// store for the states of the Tasks and to show the names instead of the
	// numbers
	var dsState = LarsGridConfig.dsState;

	var dsType = LarsGridConfig.dsType;

	// Json Reader that creates a JavaScript array from JSON response
	var myReader = new Ext.data.JsonReader({
				root : 'ass',
				id : this.primaryKey
			}, this.larsObj);

	// store for all the tasks
	var store = new Ext.data.Store({
				proxy : new Ext.data.HttpProxy({
							url : 'lars_json.php', // url to server side
							// script
							method : 'POST'
						}),
				baseParams : {
					task : "getAssignment",
					id : parentNode.id
				},
				reader : myReader,
				sortInfo : {
					field : 'OBJ_DESC',
					direction : 'ASC'
				}
			});
	var fm = Ext.form;

	// Column Model is defined for the data in the store
	this.cm = new Ext.grid.ColumnModel([this.expander, {
				id : 'ID',
				header : "ID",
				dataIndex : 'id',
				width : 20,
				readOnly : true,
				sortable : true,
				hidden : true
			}, this.actionFileType, {
				header : Lars.main.grid.columns.desc,
				dataIndex : 'OBJ_DESC',
				sortable : true,
				width : 150,
				editor : new fm.TextField({
							allowBlank : false
						})
			}, {
				header : Lars.main.grid.columns.content,
				dataIndex : 'LARS_CONTENT',
				readOnly : true,
				hidden : true,
				width : 40
			}, {
				header : Lars.main.grid.columns.type,
				dataIndex : 'type',
				hidden : true,
				sortable : true,
				readOnly : true,
				width : 60
			}, {
				header : Lars.main.grid.columns.type,
				dataIndex : 'LARS_TYPE',
				sortable : true,
				fixed : true,
				width : 70,
				// create a dropdown based on store date dsState
				editor : new Ext.form.ComboBox({
					mode : 'local',
					editable : false,
					triggerAction : 'all',
					store : dsType,
					displayField : 'text',
					valueField : 'id',
					valueNotFoundText : Lars.main.grid.columns.valueNotFoundText
				}),
				renderer : // custom rendering specified inline
				function(data, metadata) {
					if (metadata) {
						metadata.css = LarsGridConfig.renderCell(data);
					}
					record = dsType.getById(data);
					if (record) {
						return record.data.text;
					} else {
						return '';
					}
				}
			}, {
				header : Lars.main.grid.columns.last_changed,
				dataIndex : 'OBJ_LAST_CHANGED',
				width : 70,
				fixed : true,
				readOnly : true,
				// hidden: true,
				sortable : true,
				renderer : function(value) {
					return value.dateFormat('d-m-Y');
				}
			}, {
				header : Lars.main.grid.columns.created,
				dataIndex : 'OBJ_CREATION_TIME',
				readOnly : true,
				width : 100,
				hidden : true,
				sortable : true,
				renderer : function(value) {
					return value.dateFormat('d-m-Y H:i');
				}
			}, this.actionPackage]);

	PackageGrid.superclass.constructor.call(this, {
				region : 'center',
				loadMask : {
					msg : Lars.main.grid.loadMask
				},
				id : parentNode.id + "Grid",
				boder : false,
				bodyBorder : false,
				node : parentNode,
				disableSelection : true,
				closable : true,
				autoScroll : true,
				hideHeaders : true,
				cm : this.cm,
				store : store,
				autoSizeColumns : true,
				autoExpandColumn : 'ID',
				frame : false,
				tbar : this.tobar,
				view : new Ext.grid.GridView({
							forceFit : true,
							enableRowBody : true,
							emptyText : Lars.main.grid.emptyText,
							deferEmptyText: true
						}),
				clicksToEdit : 1,
				stripeRows : true,
				plugins : [this.expander, this.actionPackage,
						this.actionFileType]
			});
	this.addEvents({
				schuelerEditTab : true
			});
	this.addEvents({
				refreshTab : true
			});

	this.on('rowcontextmenu', this.onContextClick, this);
	this.on('afteredit', this.handleEdit, this);
	this.on('beforeadd', this.handleEdit, this);


	this.on("render", function(g) {
				var dropOff = new Ext.dd.DropZone(g.getEl(), {
							ddGroup : 'TreeDD',
							onDragDrop : function(e, id) {
								alert("dragdrop");
							},

							notifyDrop : function(dd, e, data) {
								var t = e.getTarget('div.x-panel');
								var folderId = this.id;
								Ext.Ajax.request({
											scope : this,
											url : 'lars_json.php',
											params : {
												id : data.node.id,
												folderId : folderId,
												task : 'copyIntoPackage'
											},
											success : function(response,
													options) {
												var responseData = Ext.util.JSON
														.decode(response.responseText);// passed
												// back
												// from
												// server
												if (responseData.success) {
													Ext.getCmp(folderId).store
															.load();
												}
											}
										});
							},
							onContainerOver : function(source, e, data) {
								if (data.node.leaf != true) {
									return this.dropNotAllowed;
								} else {
									return this.dropAllowed;
								}
							}
						});
			});
};

Ext.extend(PackageGrid, Ext.grid.EditorGridPanel, {
	onContextClick : function(grid, index, e) {
		var row = grid.getView().getRow(index);
		var record = grid.store.getAt(index);
		var node1 = {};
		node1.id = record.data.id;
		node1.text = record.data.OBJ_NAME;
		node1.parentElement = grid.id;
		this.menu = new Ext.menu.Menu({
					id : 'grid-ctx'
				});
		if (record.data.action2 == "editPage")
			this.menu.add({
						iconCls : 'editPage',
						text : Lars.edit,
						tooltip : Lars.edit,
						handler : function() {
							Ext.getCmp('main-tabs').fireEvent(
									'schuelerEditTab', node1, this.groupColor);
						},
						scope : this
					});
		this.menu.add({
			iconCls : 'delete',
			text : Lars.del,
			tooltip : Lars.main.grid.del_tt,
			handler : function() {
				Ext.Msg.confirm(Lars.main.grid.del_confirm, node1.text + '',
						function(btn) {
							if (btn == 'yes') {
								Ext.Ajax.request({
									scope : this,
									url : 'lars_json.php',
									params : {
										id : node1.id,
										name : node1.text,
										task : 'deleteItem'
									},
									success : function(response, options) {
										var responseData = Ext.util.JSON
												.decode(response.responseText);
										if (responseData.success) {
											grid.store.remove(record);
										} else {
											Ext.ux.ToastLars
													.msg(
															Lars.msg.failure,
															Lars.msg.failure_nothing_changed,
															5);
										}
									}
								});
							}
						}, this);
			},
			scope : this
		});
		this.menu.add({
					iconCls : 'copy',
					text : Lars.copy_document,
					tooltip : Lars.copy_document_tt,
					handler : function() {
						fileRecordToCopy = record;
						packageNodeToCopy = false;
					},
					scope : this
				});
		if (fileRecordToCopy) {
			this.menu.add({
				text : Lars.main.tree.copy_document,
				id : 'add2314194723340233',
				handler : function() {
					Ext.Msg.confirm(Lars.main.tree.copy_confirm_1,
							Lars.main.tree.copy_confirm_2_document
									+ fileRecordToCopy.data.OBJ_DESC
									+ Lars.main.tree.copy_confirm_3_in_package,
							function(btn) {
								if (btn == 'yes') {
									Ext.Ajax.request({
										url : 'lars_json.php',
										params : {
											task : "copyFile",
											sourceId : fileRecordToCopy.id,
											targetId : node1.id
										},
										success : function(response, options) {
											var responseData = Ext.util.JSON
													.decode(response.responseText);
											if (responseData.success) {
												Ext.ux.ToastLars
														.msg(
																Lars.msg.success_copy,
																responseData.name
																		? responseData.name
																		: " ",
																3);
											} else {
												Ext.ux.ToastLars
														.msg(
																Lars.msg.failure_copy,
																responseData.name
																		? responseData.name
																		: " ",
																5);
											}
										},
										failure : function() {
											Ext.ux.ToastLars.msg(
													Lars.msg.failure_copy, "",
													3);
										},
										scope : this
									});
								}
							}, this);
				},
				iconCls : "paste"
			});
		}

		this.menu.on('hide', this.onContextHide, this);
		e.stopEvent();
		if (this.ctxRow) {
			Ext.fly(this.ctxRow).removeClass('x-node-ctx');
			this.ctxRow = null;
		}
		this.ctxRow = this.view.getRow(index);
		this.ctxRecord = this.store.getAt(index);
		Ext.fly(this.ctxRow).addClass('x-node-ctx');
		this.menu.showAt(e.getXY());
	},

	onContextHide : function() {
		if (this.ctxRow) {
			Ext.fly(this.ctxRow).removeClass('x-node-ctx');
			this.ctxRow = null;
		}
	},

	updateDB : function(oGrid_Event) {

		if (oGrid_Event.value instanceof Date) { // format the value for PHP
			// Script
			var fieldValue = oGrid_Event.value.format('U');
		} else {
			var fieldValue = oGrid_Event.value;
		}
		Ext.Ajax.request({
			scope : this,
			url : 'lars_json.php',
			params : {
				task : "update",
				key : this.primaryKey,
				keyValue : oGrid_Event.record.data.id,
				id : oGrid_Event.record.data.id,
				field : oGrid_Event.field,// the column name
				fieldValue : fieldValue,// the updated value
				originalValue : oGrid_Event.record.modified
			},
			failure : function(response, options) {
				Ext.MessageBox.alert(Lars.msg.failure,
						Lars.msg.failure_response);
				this.store.rejectChanges();// undo any changes
			},// end failure block
			success : function(response, options) {
				var responseData = Ext.util.JSON.decode(response.responseText);
				if (responseData.success == true) {
					Ext.ux.ToastLars.msg(Lars.msg.success_changed_data,
							responseData.name ? responseData.name : " ", 3);
					if (oGrid_Event.field == "OBJ_DESC") {
						oGrid_Event.record.data.OBJ_NAME = fieldValue;
					}
					this.store.commitChanges();
				} else {
					Ext.ux.ToastLars.msg(Lars.msg.failure,
							Lars.msg.failure_nothing_changed + '<br>'
									+ responseData.name, 4);
					this.store.rejectChanges();// undo any changes
				}
			}// end success block
		}		// end request config
		); // end request
	}, // end updateDB

	handleEdit : function(editEvent) {
		if (editEvent) {
			var gridField = editEvent.field;
			this.updateDB(editEvent);
		} else {
			Ext.Msg.alert(Lars.msg.failure_nothing_changed);
		}
	},

	onRowActionPackage : function(grid, rec, action, row, col) {
		var node2 = {
			id : rec.data.id,
			text : rec.data.OBJ_DESC,
			parentElement : this.node.parentElement,
			attributes : {
				iconCls : action,
				lars_ref : rec.data.LARS_CONTENT
			}
		};
		switch (action) {
			case 'cut' :
				copy3(rec.data.OBJ_PATH);
				break;
			case 'add-page' :
				this.win = new UploadWindow(this.node, 2, rec.data.OBJ_DESC
								+ Lars.main.grid.solution);
				this.win.show();
				this.win.setZIndex(90000);
				break;
			case 'icon-edit-record' :
				break;
			case 'comment-edit' :
			case 'comments' :
				this.win = new LarsCommentWindow(rec);
				this.win.show();
				this.win.setZIndex(90000);
				break;
			case 'file-link' :
				if (node2.attributes.lars_ref.match(window.location.host)){
					LarsViewer.QuestionTabOrBrowserOpen(node2);
				} else {
					LarsViewer.QuestionBrowserOpenNode(node2);                    
				}
				break;
			case 'page-save' :
				Ext.Msg.confirm(Lars.main.grid.open_link_1,
						Lars.main.grid.open_link_2, function(btn) {
							if (btn == 'yes') {
								window.open("tools/get.php?object=" + rec.data.id);
							}
						}, this);
				break;
			case 'editPage' :
				Ext.getCmp('main-tabs').fireEvent('schuelerEditTab', node2,
						this.groupColor);
				break;
			case 'tab-go' :
				Ext.getCmp('main-tabs').fireEvent('viewIFrameTab', node2,
						this.groupColor);
				break;
			case 'delete' :
				Ext.Msg.confirm(Lars.main.grid.del_confirm, rec.data.OBJ_DESC
								+ '', function(btn) {
							if (btn == 'yes') {
								Ext.Ajax.request({
									scope : this,
									url : 'lars_json.php',
									params : {
										id : rec.id,
										name : rec.data.OBJ_NAME,
										task : 'deleteItem'
									},
									success : function(response, options) {
										var responseData = Ext.util.JSON
												.decode(response.responseText);
										if (responseData.success) {
											grid.store.remove(rec);
										} else {
											Ext.ux.ToastLars
													.msg(
															Lars.msg.failure,
															Lars.main.grid.del_failure_msg,
															5);
										}
									}
								});
							}
						}, this);
				break;
		}
		if (action.match("file")) {
			switch (rec.data.action2) {
				case 'file-gif' :
				case 'file-tiff' :
				case 'file-jpeg' :
				case 'file-jpg' :
				case 'file-png' :
				case 'file-bmp' :
				case 'editPage' :
					this.expander.toggleRow(row);
					break;
				case 'page-save' :
					switch (action) {
						case 'file-gif' :
						case 'file-tiff' :
						case 'file-jpeg' :
						case 'file-jpg' :
						case 'file-png' :
						case 'file-bmp' :
							this.expander.toggleRow(row);
							break;
						default :
							node2.attributes = {lars_ref : "tools/get.php?object="+node2.id, iconCls: node2.attributes.iconCls};
							LarsViewer.QuestionBrowserOpenPDF(node2);
							break;
					}
					break;
			}
		}
	}, // eo onRowAction

	changeState : function(state, node) {
		if(state==4 && isTeacher != 1){
			Ext.Msg.alert('', Lars.main.grid.only_teacher);
		} else {
			Ext.Ajax.request({
						url : 'lars_json.php',
						params : {
							task : "changeState",
							id : node.id,
							state : state
						},
						success : function() {
							var treeSelf = Ext.getCmp('topics-tree');
							var treeOthers = Ext.getCmp('topics-tree-t');
							var treeResources = Ext.getCmp('resources-tree');
							if (treeSelf.getNodeById(node.id)) {
								treeSelf.root.reload(treeSelf.onManualReload);
							}
							if (treeOthers.getNodeById(node.id)) {
								treeOthers.root.reload(treeOthers.onManualReload);
							}
							this.stateAction.items[0].setIconClass(this
									.getStateIcon(state));
						},
						scope : this
					});
		}
	},
	getStateIcon : function(state) {
		switch (parseInt(state)) {
			case 0 :
				return 'redIcon';
				break;
			case 1 :
				return 'orangeIcon';
				break;
			case 2 :
				return 'greenIcon';
				break;
			case 3 :
				return 'blueIcon';
				break;
			case 4 :
				return 'yellowIcon';
				break;
			default :
				return 'whiteIcon';
				break;
		}
	},
	getStateText : function(state) {
		switch (parseInt(state)) {
			case 0 :
				return Lars.main.grid.state_0;
				break;
			case 1 :
				return Lars.main.grid.state_1;
				break;
			case 2 :
				return Lars.main.grid.state_2;
				break;
			case 3 :
				return Lars.main.grid.state_3;
				break;
			case 4 :
				return Lars.main.grid.state_4;
				break;
		}
	}
});
