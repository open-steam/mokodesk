Ext.ux.ToastLarsDiscussion = function(){
    var msgCt;

    function createBox(u, t, s){
        return ['<div class="msg">',
                '<div class="x-box-tl"><div class="x-box-tr"><div class="x-box-tc"></div></div></div>',
                '<div class="x-box-ml"><div class="x-box-mr"><div class="x-box-mc-discussion">', u, '<h3>', t, '</h3>', s, '</div></div></div>',
                '<div class="x-box-bl"><div class="x-box-br"><div class="x-box-bc"></div></div></div>',
                '</div>'].join('');
				
    }
    return {
        msg : function(imageUri, title, format, seconds){
            var sec = seconds || 2;
            if(!msgCt){
                msgCt = Ext.DomHelper.insertFirst(document.body, {id:'msg-div',style:'position:absolute;z-index:100000'}, true);
            }
            var s = String.format.apply(String, Array.prototype.slice.call(arguments, 2));
            var m = Ext.DomHelper.append(msgCt, {html:createBox(imageUri, title, s)}, true);
            msgCt.alignTo(document, 'br-br', [-50,-5]);
            m.slideIn('b').pause(sec);
			m.on("mouseover", function(){
				m.ghost("", {remove:true});
			}, m.dom);
        }
	}
}();


