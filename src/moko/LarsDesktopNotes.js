LarsDesktopNotes = function() {
	this.content = new Ext.form.HtmlEditor({
				id : 'desktop-calendar-form',
				name : Lars.main.notes,
				value : '',
				tbar: '',
				toolbar: '',
				enableAlignments : false,
				enableColors : false,
		        hideLabel: true,
				enableFormat:false,
				enableFont : false,
				enableFontSize : false,
				enableLinks : false,
				enableLists : false,
				enableSourceEdit : false
			});
	this.getContent();

	this.form = new Ext.FormPanel({
				labelAlign : 'top',
				buttonAlign : 'center',
				layout : 'fit',
				items : [this.content],
				buttons : [{
							align : 'center',
							text : Lars.button_save,
							cls: "x-btn button",
							ctCls: "x-btn button",
							handler : this.onAdd,
							scope : this
						}],
				border : false,
				bodyStyle : 'background:transparent;padding:4px 4px 0 0;'
			});

	LarsDesktopNotes.superclass.constructor.call(this, {
				collapsible : true,
				width : 200,
				split : true,
				region : "east",
				layout : 'fit',
				id : 'lars-desktop-calendar',
				margins: '0 0 0 0',
				viewConfig : {
					forceFit : true
				},
				autoScroll : true,
				buttonAlign : "center",
				items : this.form
			});
};
Ext.extend(LarsDesktopNotes, Ext.Panel, {
			onAdd : function() {
				var content = this.content.getValue();
				Ext.Ajax.request({
					scope : this,
					url : 'lars_json.php',
					params : {
						task : "saveAppointments",
						content : content
					},
					failure : function(response, options) {
						Ext.MessageBox.alert(Lars.msg.warning,
								Lars.msg.failure_connection);
					},// end failure block
					success : function(response, options) {
						var responseData = Ext.util.JSON
								.decode(response.responseText);// passed back
						// from server
						if (responseData.success == true) {
							Ext.ux.ToastLars
									.msg(Lars.msg.success_changed_data,
											responseData.name
													? responseData.name
													: " ", 3);
						} else {
							Ext.ux.ToastLars.msg(Lars.msg.failure,
									Lars.msg.failure_nothing_changed + ' <br>'
											+ responseData.name, 4);
						}
					}// end success block
				}		// end request config
				); // end request
			},
			getContent : function() {
				Ext.Ajax.request({
							scope : this,
							url : 'lars_json.php',
							params : {
								task : "getAppointments"
							},
							failure : function(response, options) {
								Ext.MessageBox.alert(Lars.msg.warning,
										Lars.msg.failure_connection);
							},// end failure block
							success : function(response, options) {
								var responseData = Ext.util.JSON
										.decode(response.responseText);
								if (responseData.success == true) {
									this.content.setValue(responseData.content);
								} else {
									this.content
											.setValue(Lars.msg.failure_connection);
								}
							}
						});
			}

		});