Ext.onReady(function() {

	var listener = {
		click : {
			fn : function(node, a) {
				node.toggle();
			}
		},
		render : function() {
			this.root.on( {
				scope : this.el,
				load : this.el.unmask,
				loadexception : this.el.unmask
			});
		},
		contextmenu : function(node, e) {
			this.menu = new Ext.menu.Menu( {
				id : 'topics-tree-ctx',
				items : [ {
					text : "Berechtigung für diese Gruppe erteilen",
					id : 'add2312341',
					handler : function() {
						Ext.Ajax.request( {
							scope : this,
							url : 'moko_authorization.php',
							params : {
								task : "addAuthorization",
								id : node.id
							},
							failure : function(response, options) {
								var responseData = Ext.util.JSON.decode(response.responseText);
								Ext.MessageBox.alert(Lars.msg.warning, responseData.name);
							},
							success : function(response, options) {
								var responseData = Ext.util.JSON.decode(response.responseText);
								if (responseData.success == true) {
							        Ext.ux.ToastLars.msg("Erfolgreich hinzugefügt", 5);
								} else {
									Ext.MessageBox.alert(Lars.msg.warning, responseData.name);
								}
							}
						});
					}
				} ]
			});
			this.menu.showAt(e.getXY());
			this.menu.el.setZIndex(90010)
		},
		afterRender : function() {
			groupsTree.superclass.afterRender.call(this);
			this.el.on( {
				contextmenu : {
					fn : function() {
						return false;
					},
					stopEvent : true
				}
			});
		}
	};
	var groupsTree = new Ext.tree.TreePanel( {
		id : "groups-tree",
		region : 'center',
		rootVisible : false,
		autoScroll : true,
		listeners : listener,
		root : new Ext.tree.AsyncTreeNode( {
			text : "Root",
			loader : new Ext.tree.TreeLoader( {
				dataUrl : 'lars_json.php',
				baseParams : {
					task : "getGroupsTree"
				}
					}),
			id : 'source'
		}),
		tbar : [{
	        text:Lars.dialog.groups.add_user,
	        tooltip: Lars.dialog.groups.add_user_tt,
	        handler: function(){
				this.win = new MokoAddUserWindow();
				this.win.show();
				this.win.setZIndex(90001);
	        },
	        scope: this
	    	
	    }]

	});

	var centerPanel = new Ext.Panel( {
		id : 'center',
		layout : 'border',
		region : 'center',
		items : [ groupsTree ]
	});

	var viewport = new Ext.Viewport( {
		layout : 'border',
		items : [ centerPanel ]
	});

});