LarsDesktop = function(){
    LarsDesktop.superclass.constructor.call(this, {
        margins: '0 0 0 0',
        layout: 'border',
		hideMode  : !Ext.isIE?'nosize':'display',
        region: 'center',
        autoScroll: false,
    });
};
Ext.extend(LarsDesktop, Ext.Panel, {});
