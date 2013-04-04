var version = "V0.986 19.10.2012";
var larsUpdater = {};
// UpdateIntervals
var larsUpdaterInterval = 10000;
var onlineStatusInterval = 30000;
var timeoutUpdateConnection = 180000;

LarsViewer = {};
eastCollapsed = {};
LarsGridConfig = {};
title = {};
larsDesktopId = {};
larsArchivId = {};
larsBinId = {};
loginInfo = {}
lastLogin = {};
var user = {};
var pass = {};
var larsVoiceChatAllowed = false;
var larsVoiceChatEnabled = true;
var packageNodeToCopy = false;
var fileRecordToCopy = false;
task = {};
var AScgiloc = 'http://www.imathas.com/imathas/filter/graph/svgimg.php';
var AMTcgiloc = "http://www.imathas.com/cgi-bin/mimetex.cgi";
var larsNews = '<font size="3">' 
		+ '<b>Version 0.986:<br></b>'
		+ '<ul><li>- Französisch.<br>'
		+ '<b>Version 0.983:<br></b>'
		+ '<ul><li>- Fehlerkorrekturen.<br>'
		+ '<b>Version 0.972:<br></b>'
		+ '<ul><li>- Neuer Login zum MokoDesk.<br>'
		+ '<b>Version 0.970:<br></b>'
		+ '<ul><li>- Englischer MokoDesk verfügbar.<br>'
		+ '<b>Version 0.969:<br></b>'
		+ '<ul><li>- Benachrichtigungen für "Eigene Mitteilungen" werden erst ausgeblendet, wenn mit dem Mauszeiger über die Nachricht bewegt wurde.<br>'
		+ '<li>- Benachrichtigungen werden nicht angezeigt, wenn die "Eigenen Mitteilungen" geöffnet sind.<br>'
		+ '<b>Version 0.967:<br></b>'
		+ '<li>- Überarbeitung der Benachrichtigung bei neuen Mitteilungen auf der eigenen Seite.<br>'
		+ '<b>Version 0.955:<br></b>'
		+ '<li>- Überarbeitung der Oberfläche. Neue Icons auf der Hauptseite.<br>'
		+ '<li>- Zwischen neuen Dokumenten und Nachrichten auf dem eigenen MokoDesk und anderen MokoDesks wird unterschieden<br>'
		+ '<li>- Es werden nicht mehr standardmäßig Dokumente aller eingebundenen Schreibtische angezeigt. Hierzu muss der eigebundene Schreibtisch in einem Dialog ausgewählt werden. (Zusätzliches Icon unter "Andere MokoDesks")<br>'
		+ '<li>- Weitere Änderungen sind demnächst in der Hilfe zu finden<br>'
		+ '</ul>';

Ext.onReady(function() {
	Ext.QuickTips.init();
	var choice = 0;
	Ext.Ajax.timeout = 90000;
	if (choice) {
	} else {
		this.doLoginBid = function() {
			Ext.Ajax.request({
						url : 'lars_login.php',
						params : {
							version : version
						},
						success : function(response, options) {
							var responseData = Ext.util.JSON
									.decode(response.responseText);
							title = responseData.title;
							imageHeight = responseData.imageHeight
									? responseData.imageHeight
									: 120;
							eastCollapsed = responseData.eastCollapsed
									? responseData.eastCollapsed
									: 1;
							larsDesktopId = responseData.larsDesktop;
							larsArchivId = responseData.larsArchiv;
							larsBinId = responseData.larsBin;
							larsVoiceChatAllowed = responseData.vc;
							isTeacher = responseData.isTeacher;
							loginTime = responseData.loginTime;
							lastLoginUnix = responseData.lastLoginUnix;
							lastLogin = responseData.loginTimeLast;
							loginInfo = Lars.main.logged_in_since + loginTime +"<br>" + Lars.main.last_login + lastLogin;
							user = responseData.user;
							pass = responseData.pass; //used for voice chat
							if (responseData.success) {
								log_in()
							} else if (responseData.version) {
								Ext.Msg.show({
											title : Lars.msg.failure,
											msg : responseData.name,
											icon : Ext.MessageBox.ERROR,
											buttons : Ext.Msg.OK
										});
							}
						},
						failure : function(form, action) {
							if (action.failureType === 'server') {
								obj = Ext.util.JSON
										.decode(action.response.responseText);
								Ext.Msg.alert(Lars.msg.failure,
										obj.errors.reason);
							} else {
								Ext.Msg.alert(Lars.msg.failure,
										Lars.msg.failure_redirect);
							}
						},
						scope : this
					});
		};
		this.doLoginBid.defer(10, this);
		var checkBrowser = function() {
			if ((!Ext.isGecko2 && !Ext.isGecko3)
					|| (Ext.isSafari || Ext.isSafari2 || Ext.isSafari3)) {
		    	Ext.ux.ToastLars.msg('<font color="#DD0000">'+Lars.msg.attention, Lars.msg.attention_firefox, 7);
			}
		}
		checkBrowser.defer(1000, this);

		var win = new Ext.Window({
					id : 'login-window',
					width : 400,
					height : 134,
					closable : false,
					animate : true,
					title : Lars.dialog.login.title + version + ')</i>',
					keys : [{
								key : [10, 13],
								scope : this,
								fn : this.doLogin
							}],
					//items : login
				});
		setTimeout(function() {
					Ext.get('loading').remove();
					Ext.get('loading-mask').fadeOut({
								remove : true
							});
				}, 100);
		//win.show();
		//login.items.items[0].focus(false, 1250);
	}

	function log_in() {
		if (this.started) {
			return;
		}
		this.started = true;
		Ext.ux.ToastLars.msg(Lars.msg.success_login, Lars.msg.loading_data, 2);

		/*
		 * TreePanel
		 */
		var linksFolderTree = new LarsTreePanelFolderLinks();
		var linksTree = new LarsTreePanelLinks();
		var folderResources = new LarsResourcesPanel(linksFolderTree, 'center',
				'resource-panel', '8 8 4 0', Lars.main.tree.resources_title);
		var linkResources = new LarsResourcesPanel(linksTree, 'south',
				'links-panel', '0 8 8 0', Lars.main.tree.internet_links_title);
		var eastPanel = new Ext.Panel({
					id : 'main-east',
					region : 'east',
					layout : 'border',
					split : true,
					width : 204,
					minSize : 175,
					maxSize : 400,
					border : false,
					layoutConfig : {
						animate : true
					},
					margins : '0 0 0 0',
					cmargins : '8 0 8 0',
					collapsible : true,
					items : [folderResources, linkResources]
				});
		eastPanel.on('beforeexpand', LarsGridConfig.onCollapseExpand, this);
		eastPanel.on('beforecollapse', LarsGridConfig.onCollapseExpand, this);

		/*
		 * MainPanel
		 */
		var desktopNode = {
			id : larsDesktopId
		};
		var mainPanel = new MainPanel();

		var larsDesktopGrid = new LarsDesktopGrid('');
		var larsDesktop = new LarsDesktop();
		var larsDesktopDiscussion = new LarsDesktopDiscussion(desktopNode);
		var larsDesktopNotes = new LarsDesktopNotes();

		var larsDesktopNorth = new LarsDesktopNorth();
		larsDesktopNorth.add(larsDesktopNotes);

		var larsDesktopCenter = new Ext.TabPanel({
					id: "lars-desktop-tab-panel",
					frame : false,
					region : "center",
					activeTab : 1,
					margins : '0 0 0 0',
					frame : false,
					hideBorders: true,
					hideLabel: true,
					bodyBorder : false,
					border: false,
					resizeTabs : true,
					tabWidth : 150,
					minTabWidth : 90,
					enableTabScroll : true,
					viewConfig : {
						forceFit : true
					},
					listeners : LarsViewer.LinkInterceptor
				});
		larsDesktopCenter.add({
			xtype : 'panel',
			id: 'about-panel',
			title : Lars.main.about,
			iconCls : 'wand',
			layout : 'accordion',
			items : [{
						title : Lars.main.help,
						iconCls: 'help',
						xtype : 'iframepanel',
						id : 'moko_about',
						tbar : ['->', {
									iconCls : 'error',
									text : Lars.main.send_bug.text,
									tooltip : Lars.main.send_bug.text_tt,
									handler : function() {
										this.win = new LarsDesktopErrorWindow();
										this.win.show();
										this.win.setZIndex(90001);
									},
									scope : this
								}],
						edit : false,
						layout : "fit",
						autoScroll : true,
						defaultSrc : 'MokoDeskHelp/index.htm',
						listeners : {
							domready : LarsViewer.LinkInterceptorIFrame
						}
					},{
						collapsible : true,
						iconCls: 'about',
						xtype : 'panel',
						title : Lars.main.news,
						listeners : LarsViewer.LinkInterceptor,
						layout : 'fit',
						split : true,
						autoScroll : true,
						html : larsNews
					}]
		});
		larsDesktopCenter.add(larsDesktopDiscussion);
		larsDesktopCenter.add(larsDesktopGrid);

		larsDesktop.add(larsDesktopCenter);
		larsDesktop.add(larsDesktopNorth);
		larsDesktopWrapper = new Ext.Panel({
	        title: Lars.main.desktop_title,
	        id: 'lars-desktop',
	        margins: '0 0 0 0',
			layout:'border',
			hideMode  : !Ext.isIE?'nosize':'display',
//	        split: true,
	    	iconCls: 'application-home',
			closable: false,
			autoScroll: false,
//			items: []
		});
		larsDesktopWrapper.add(larsDesktop);
		if (larsVoiceChatAllowed && larsVoiceChatEnabled) {
			larsDesktopWrapper.add(larsVoiceChat);
		}
		larsDesktopGrid.getView().getRowClass = LarsGridConfig.applyRowClassWithout;
		mainPanel.add(larsDesktopWrapper);
		var innerPanel = new Ext.Panel({
					id : 'main-center',
					frame : true,
					header : true,
					layout : 'fit',
					region : 'center',
					margins : '8 5 8 0',
					autoScroll : false,
					items : mainPanel
				});
		var centerPanel = new Ext.Panel({
					id : 'center',
					header : false,
					layout : 'border',
					region : 'center',
					border : false,
					autoScroll : false,
					margins : '0 0 0 0',
					items : [innerPanel]
				});
		/*
		 * TopicsPanelLars
		 */
		var topicsPanelLars = new LarsTopicsPanel();
		var customImagePanel = new LarsCustomImagePanel(title, imageHeight);
		var westPanel = new Ext.Panel({
					id : 'main-west',
					region : 'west',
					layout : 'border',
					split : true,
					width : 204,
					minSize : 175,
					maxSize : 400,
					border : false,
					layoutConfig : {
						animate : true
					},
					margins : '0 0 0 0',
					cmargins : '8 5 8 0',
					collapsible : true,
					items : [customImagePanel, topicsPanelLars]
				});

		var viewport = new Ext.Viewport({
					layout : 'border',
					items : [eastPanel, centerPanel, westPanel]
				});
		if (eastCollapsed == "1") {
			eastPanel.collapse();
		}
		larsDesktopDiscussion.store.load({
					params : {
						start : 0,
						limit : 10
					}
				});
		larsDesktopGrid.store.load();

		larsUpdater = new LarsUpdater();

		function load_updates() {
			larsUpdater.store.load({
						params : {
							task : "getUpdates",
							id : Ext.util.JSON.encode(larsUpdater.updateIds)
						}
					});
		}
		larsUpdater.store.proxy.on("loadexception", function(a, b, response) {
					if (response.responseText) {
						var responseData = Ext.util.JSON
								.decode(response.responseText);// passed back
																// from server
						Ext.ux.ToastLars.msg(Lars.msg.failure_connection,
								responseData.name, 5);
					}
					load_updates.defer(larsUpdaterInterval, this, []);
				}, this);
		larsUpdater.store.on("load", function(a, b, c) {
					load_updates.defer(larsUpdaterInterval, this, []);
				}, this);

		load_updates.defer(10000, this, []);
		LarsUpdaterUsers.defer(30000, this, []);

		larsDesktopDiscussion.startAutoUpdate("d" + larsDesktopId);
	};
});

/*
 * Ende des Seitenaufbaus
 * Beginn Config, ... 
 */
// This is a custom event handler passed to some panels so link open in a new
// windw
LarsViewer.LinkInterceptor = {
	render : function(p) {
		p.body.on({
					'mousedown' : function(e, t) { // try to intercept the easy
													// way
						t.target = '_blank';
					},
					'click' : function(e, t) { // if they tab + enter a link,
												// need to do it old fashioned
												// way
						if (String(t.target).toLowerCase() != '_blank') {
							e.stopEvent();
							window.open(t.href);
						}
					},
					delegate : 'a'
				});
	}
};
LarsViewer.LinkInterceptorIFrame = function(frame) {
	frame.getDoc().on({
				'mousedown' : function(e, t) { // try to intercept the easy way
					if (t.href.match(window.location.host)) {
						frame.setSrc(t.href);
					} else {
						t.target = '_blank';
					}
				},
				'click' : function(e, t) { // if they tab + enter a link, need
											// to do it old fashioned way
					if (String(t.target).toLowerCase() != '_blank') {
						e.stopEvent();
						if (t.href.match(window.location.host)) {
							frame.setSrc(t.href);
						} else {
							window.open(t.href);
						}
					}
				},
				delegate : 'a'
			});
}
LarsViewer.getKeyMap = function(fn, scope) {
	// var enterKeyMap = new Ext.KeyMap(scope.form, {
	var enterKeyMap = {
		key : Ext.EventObject.ENTER,
		fn : fn,
		scope : scope
	};
	// });
	return enterKeyMap;
}
LarsViewer.onLoadException = function(a, b, c) {
	Ext.ux.ToastLars.msg(Lars.msg.failure_response, '', 5);
};

LarsViewer.QuestionTabOrBrowserOpen = function(node) {
	Ext.Msg.show({
				title : node.attributes.lars_ref,
				msg : Lars.dialog.link.open_both + "<br><i>"
						+ node.attributes.lars_ref + '</i>',
				buttons : {
					ok : Lars.dialog.link.button_app,
					no : Lars.dialog.link.button_browser,
					cancel : Ext.MessageBox.buttonText.cancel
				},
				icon : Ext.MessageBox.QUESTION,
				animEl : 'elId',
				fn : function(btn) {
					if (btn == 'ok') {
						Ext.getCmp('main-tabs').fireEvent('viewIFrameTabAll',
								node);
					} else if (btn == 'no') {
						window.open(node.attributes.lars_ref);
					}
				}
			})
};

LarsViewer.QuestionBrowserOpen = function(e) {
	Ext.Msg.confirm(e.currentTarget.href, Lars.dialog.link.open_browser,
			function(btn) {
				if (btn == 'yes') {
					window.open(e.currentTarget.href);
				}
			}, this)
};
LarsViewer.QuestionBrowserOpenNode = function(node) {
	Ext.Msg.confirm(node.attributes.lars_ref, Lars.dialog.link.open_browser,
			function(btn) {
				if (btn == 'yes') {
					window.open(node.attributes.lars_ref);
				}
			}, this)
};
LarsViewer.QuestionBrowserOpenPDF = function(node) {
	Ext.Msg.show({
				title : node.attributes.lars_ref,
				msg : Lars.dialog.link.open_both + "<br><i>"
						+ node.attributes.lars_ref + '</i>',
				buttons : {
					ok : Lars.dialog.link.button_app,
					no : Lars.dialog.link.button_browser,
					cancel : Ext.MessageBox.buttonText.cancel
				},
				icon : Ext.MessageBox.QUESTION,
				animEl : 'elId',
				fn : function(btn) {
					if (btn == 'ok') {
						Ext.getCmp('main-tabs').fireEvent('viewIFrameTabAll',
								node);
					} else if (btn == 'no') {
						window.open(node.attributes.lars_ref);
					}
				}
			})

LarsGridConfig = {
	larsObj : Ext.data.Record.create([{
				name : "id"
			}, {
				name : 'OBJ_NAME',
				type : 'string'
			}, {
				name : 'LARS_CONTENT',
				type : 'string'
			}, {
				name : 'LARS_COMMENT',
				type : 'string'
			}, {
				name : 'OBJ_CREATION_TIME',
				type : 'date',
				dateFormat : 'U'
			}, {
				name : 'OBJ_LAST_CHANGED',
				type : 'date',
				dateFormat : 'U'
			}, {
				name : 'DOC_LAST_MODIFIED',
				type : 'date',
				dateFormat : 'U'
			}, {
				name : 'OBJ_DESC',
				type : 'string'
			}, {
				name : 'OBJ_AUTHOR',
				type : 'string'
			}, {
				name : 'type',
				type : 'string'
			}, {
				name : 'LARS_TYPE',
				type : 'string'
			},
			{
				name : 'OBJ_TYPE',
				type : 'string'
			}, {
				name : 'is_home',
				type : 'boolean'
			}, {
				name : 'OBJ_PATH',
				type : 'string'
			}, {
				name : 'LARS_FOLDER',
				type : 'string'
			}, {
				name : 'action0',
				type : 'string'
			}, {
				name : 'action1',
				type : 'string'
			}, {
				name : 'action2',
				type : 'string'
			}, {
				name : 'action3',
				type : 'string'
			}, {
				name : 'action4',
				type : 'string'
			}, {
				name : 'action5',
				type : 'string'
			}, {
				name : 'qtip0',
				type : 'string'
			}, {
				name : 'qtip1',
				type : 'string'
			}, {
				name : 'qtip2',
				type : 'string'
			}, {
				name : 'qtip3',
				type : 'string'
			}, {
				name : 'qtip4',
				type : 'string'
			}, {
				name : 'qtip5',
				type : 'string'
			}, {
				name : 'hide1',
				type : 'boolean'
			}, {
				name : 'hide2',
				type : 'boolean'
			}, {
				name : 'hide3',
				type : 'boolean'
			}, {
				name : 'hide4',
				type : 'boolean'
			}, {
				name : 'hide5',
				type : 'boolean'
			}, {
				name : 'container',
				type : 'string'
			}]),

	dsType : new Ext.data.SimpleStore({
				id : 0,
				fields : ['id', 'text'],
				data : [[0, '-'], [1, 'Aufgabe'], [2, 'Lösung'],
						[3, 'Info']]
			}),
	expander : new Ext.grid.RowExpander({
				tpl : new Ext.Template('<p><b>Inhalt:</b><br>{LARS_CONTENT}<br></p>'),
				lazyRender : true,
				enableCaching : false
			}),
	applyRowClass : function(record, rowIndex, p, ds) {
		switch (record.data.LARS_TYPE) {
			case '0' :
				return "x-grid3-row-collapsed"
				break
			case '1' :
				return "pinkrow x-grid3-row-expanded"
				break
			case '2' :
				return "greenrow x-grid3-row-collapsed"
				break
			case '3' :
				return "x-grid3-row-expanded"
				break
			default :
				return "x-grid3-row-collapsed"
				break
		}
	},
	applyRowClassWithout : function(record, rowIndex, p, ds) {
		switch (record.data.LARS_TYPE) {
			case '0' :
				return "x-grid3-row-collapsed"
				break
			case '1' :
				return "pinkrow x-grid3-row-collapsed"
				break
			case '2' :
				return "greenrow x-grid3-row-collapsed"
				break
			case '3' :
				return "x-grid3-row-collapsed"
				break
			default :
				return "x-grid3-row-collapsed"
				break
		}
	},
	renderType : function(data, cell, record, rowIndex, columnIndex, store) {
		switch (data) {
			case "Text" :
				cell.css = "type-text";
				return;
			case "Bild" :
				cell.css = "type-picture";
				return;
			case "Download" :
				cell.css = "type-download";
				return;
			case "Link" :
				cell.css = "type-link";
				return;
		}
	},
	renderCell : function(data) {
		switch (data) {
			case 0 :
				break
			case 1 :
				return "pinkrow"
				break
			case 2 :
				return "greenrow"
				break
			default :
		}
	},
	downloadFile : function(path) {

		var id = Ext.id();
		var frame = document.createElement('iframe');
		frame.id = id;
		frame.name = id;
		frame.className = 'x-hidden';
		frame.src = path;
		document.body.appendChild(frame);
	},
	changeAttribute : function(id, attribute, value, orignalValue) {
		Ext.Ajax.request({
			scope : this,
			url : 'lars_json.php',
			params : {
				task : "update",
				key : 'id',
				keyValue : id,
				id : id,
				field : attribute,
				fieldValue : value,
				originalValue : orignalValue
			},
			failure : function(response, options) {
				Ext.MessageBox.alert(Lars.msg.failure_connection, '');
			},// end failure block
			success : function(response, options) {
				var responseData = Ext.util.JSON.decode(response.responseText);
				if (responseData.success == true) {
					Ext.ux.ToastLars.msg(Lars.msg.success_changed_data,
							responseData.name ? responseData.name : " ", 3);
				} else {
					Ext.ux.ToastLars.msg(LArs.msg.failure,
							Lars.msg.failure_nothing_changed + '<br>'
									+ responseData.name, 4);
				}
			}// end success block
		}		// end request config
		); // end request

	},
	onCollapseExpand : function(p) {
		Ext.Ajax.request({
			scope : this,
			url : 'lars_json.php',
			params : {
				task : "update",
				key : 123,
				keyValue : 123,
				id : 123,
				field : "LARS_EAST_COLLAPSED",// the column name
				fieldValue : p.collapsed ? 0 : 1,// the updated value
				originalValue : p.collapsed ? 1 : 0
			},
			failure : function(response, options) {
			},// end failure block
			success : function(response, options) {
				var responseData = Ext.util.JSON.decode(response.responseText);
			}// end success block
		}		// end request config
		); // end request
	} // end updateDB
}
