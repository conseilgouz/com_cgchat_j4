/**
* CG Chat Component  - Joomla 4.x/5.x Component
* Version			: 1.0.0
* Package			: CG Chat
* copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : Kide ShoutBox
*/

// general

cgchat.mensaje = function(name, uid, id, url, ti, session, row, img) {
	this.html('CGCHAT_mensaje_username', name);
	this.attr('CGCHAT_mensaje_username', 'className', "CGCHAT_"+cgchat.rows[row]);
	this.html('CGCHAT_tiempo_msg', ti);
	this.attr('CGCHAT_mensaje_img', 'src', img ? img : this.img_blank);
	if (url) {
		this.attr('CGCHAT_mensaje_profil', 'href', url);
		this.show("CGCHAT_mensaje_profil_span", true);
		this.attr('CGCHAT_mensaje_img_enlace', 'href', url);
		this.attr('CGCHAT_mensaje_img_enlace', 'target', '_blank');
		this.css('CGCHAT_mensaje_img', 'cursos', 'pointer');
	}
	else {
		this.show("CGCHAT_mensaje_profil_span", false);
		this.attr('CGCHAT_mensaje_img_enlace', 'href', 'javascript:void(0)');
		this.attr('CGCHAT_mensaje_img_enlace', 'target', '');
		this.css('CGCHAT_mensaje_img', 'cursor', 'default');
	}
	if ((this.row == 1 || session == this.session) && id > 0) {
		this.show('CGCHAT_mensaje_borrar_span', true);
		this.attr('CGCHAT_mensaje_borrar', 'href', 'javascript:cgchat.borrar('+id+')');
	}
	else
		this.show('CGCHAT_mensaje_borrar_span', false);
	this.attr('CGCHAT_mensaje_ocultar', 'href', 'javascript:cgchat.show("CGCHAT_id_'+id+'")');
	if (this.row == 1) {
		if (session != '0' && row != 1) {
			this.show('CGCHAT_mensaje_banear_span', true);
			this.attr('CGCHAT_mensaje_banear', 'onclick', function() { cgchat.banear(session, 'mensaje'); });
		}
    } else {
        this.show('CGCHAT_mensaje_banear_span', false);
	}
	this.show("CGCHAT_mensaje", true);
};
cgchat.mostrar_user = function(uid, name, row, session, url, img) {
	this.html('CGCHAT_user_name', name);
	this.attr('CGCHAT_user_name', 'className', "CGCHAT_"+this.rows[row]);
	this.attr('CGCHAT_user_img', 'src', img ? img : this.img_blank);
    if ((uid > 0) &&(row < 3) && (uid != this.userid)) {// connected user
        this.show("CGCHAT_user_to_private", true); // allow private messages
        this.attr('CGCHAT_user_go_to_private', 'href', 'javascript:cgchat.ask_private('+uid+')');
    } else {
        this.show("CGCHAT_user_to_private", false); // no private messages
        this.attr('CGCHAT_user_go_to_private', 'href', 'javascript:void(0)');
    }
	if (url) {
		this.attr('CGCHAT_user_profil', 'href', url);
		this.show("CGCHAT_user_profil_mostrar", true);
		this.attr('CGCHAT_user_img_enlace', 'href', url);
		this.attr('CGCHAT_user_img_enlace', 'target', '_blank');
		this.css('CGCHAT_user_img', 'cursor', 'pointer');
	}
	else {
		this.show("CGCHAT_user_profil_mostrar", false);
		this.attr('CGCHAT_user_img_enlace', 'href', 'javascript:void(0)');
		this.attr('CGCHAT_user_img_enlace', 'target', '');
		this.css('CGCHAT_mensaje_img', 'cursor', 'default');
	}
    if (this.row == 1) {
        if (session != '0' && row != 1) {
            this.show('CGCHAT_user_banear_span', true);
            this.attr('CGCHAT_user_banear', 'onclick', function() { cgchat.banear(session, 'user'); }); 
        } else {
            this.show('CGCHAT_user_banear_span', false);
        }
	} else {
        this.show('CGCHAT_user_banear_span', false);
    }
	this.show("CGCHAT_user", true);
};
cgchat.insertNewContent = function(uid,name,text,url,ti,color,row,id,session,yo,hora,img) {
	if (text.replace(/ /g, "") != "") {
		var c = color.length>0 ? 'style="color:#'+color+'" class="CGCHAT_msg"' : 'class="CGCHAT_dc_'+this.rows[row]+' CGCHAT_msg"';
		var div = this.$('CGCHAT_msgs');
		var nodo = document.createElement('div');
		var insertO = this.$("CGCHAT_output");
		var s_hora;
		nodo.setAttribute('id', 'CGCHAT_id_'+id);
		nodo.setAttribute('class', 'CGCHAT_msg_top');
		if (this.show_hour)
			s_hora = '<span title="'+ti+'" class="CGCHAT_msg_hour">'+hora+'</span> ';
		else
			s_hora = '';
		var tmp = '';
		if (img && cgchat.show_avatar) {
			var style = cgchat.avatar_maxheight ? 'style="max-height:'+cgchat.avatar_maxheight+'" ' : '';
			tmp = '<img '+style+'src="'+img+'" class="CGCHAT_icono" alt="" /> ';
		}
		nodo.innerHTML = s_hora+tmp+'<span style="cursor: pointer" class="CGCHAT_'+this.rows[row]+'" onclick="cgchat.mensaje(\''+name+'\', '+uid+', '+id+', \''+url+'\', \''+ti+'\', \''+session+'\', '+row+', \''+img+'\')">'+name+'</span>: <span '+c+'>'+this.filter_smilies(text)+'</span>';

		if (this.order == 'bottom') {
			this.insertAfter(nodo, insertO.lastChild);
		}
		else
			insertO.insertBefore(nodo, insertO.firstChild);
		if (!yo && this.sound == 1) 
			this.play_msg_sound();
		this.ajustar_scroll();
	}
};
cgchat.insert_session = function(user) {
	var div = document.createElement('div');
	div.setAttribute('style', 'cursor:pointer');
	div.setAttribute('class', user._class);
	div.onclick = function() { cgchat.mostrar_user(user.id, user.name, user.row, user.session, user.profile, user.img) };
	div.innerHTML = user.name;
	this.$('CGCHAT_users').insertBefore(div, this.$('CGCHAT_users').firstChild);
};
cgchat.change_name_keyup = function(e, t) {
	if (this.isEnter(e)) {
		this.change_name(t);
		this.foco('CGCHAT_txt');
		return false;
	}
	return true;
};
cgchat.show_colors = function() {
	if (!cgchat.html('CGCHAT_opciones_colores')) {
		var colors = ['000000','000033','000066','000099','0000CC','0000FF','003300','003333','003366','003399','0033CC','0033FF','006600','006633','006666','006699','0066CC','0066FF','009900','009933','009966','009999','0099CC','0099FF','00CC00','00CC33','00CC66','00CC99','00CCCC','00CCFF','00FF00','00FF33','00FF66','00FF99','00FFCC','00FFFF','330000','330033','330066','330099','3300CC','3300FF','333300','333333','333366','333399','3333CC','3333FF','336600','336633','336666','336699','3366CC','3366FF','339900','339933','339966','339999','3399CC','3399FF','33CC00','33CC33','33CC66','33CC99','33CCCC','33CCFF','33FF00','33FF33','33FF66','33FF99','33FFCC','33FFFF','660000','660033','660066','660099','6600CC','6600FF','663300','663333','663366','663399','6633CC','6633FF','666600','666633','666666','666699','6666CC','6666FF','669900','669933','669966','669999','6699CC','6699FF','66CC00','66CC33','66CC66','66CC99','66CCCC','66CCFF','66FF00','66FF33','66FF66','66FF99','66FFCC','66FFFF','990000','990033','990066','990099','9900CC','9900FF','993300','993333','993366','993399','9933CC','9933FF','996600','996633','996666','996699','9966CC','9966FF','999900','999933','999966','999999','9999CC','9999FF','99CC00','99CC33','99CC66','99CC99','99CCCC','99CCFF','99FF00','99FF33','99FF66','99FF99','99FFCC','99FFFF','CC0000','CC0033','CC0066','CC0099','CC00CC','CC00FF','CC3300','CC3333','CC3366','CC3399','CC33CC','CC33FF','CC6600','CC6633','CC6666','CC6699','CC66CC','CC66FF','CC9900','CC9933','CC9966','CC9999','CC99CC','CC99FF','CCCC00','CCCC33','CCCC66','CCCC99','CCCCCC','CCCCFF','CCFF00','CCFF33','CCFF66','CCFF99','CCFFCC','CCFFFF','FF0000','FF0033','FF0066','FF0099','FF00CC','FF00FF','FF3300','FF3333','FF3366','FF3399','FF33CC','FF33FF','FF6600','FF6633','FF6666','FF6699','FF66CC','FF66FF','FF9900','FF9933','FF9966','FF9999','FF99CC','FF99FF','FFCC00','FFCC33','FFCC66','FFCC99','FFCCCC','FFCCFF','FFFF00','FFFF33','FFFF66','FFFF99','FFFFCC','FFFFFF'];
		var out = '';
		var c;
		for (var i=0; i<colors.length;i++) {
			c = colors[i];
			out += '<a href="javascript:cgchat.set_color(\''+c+'\')"><img class="CGCHAT_r" src="'+this.img_blank+'" style="background-color:#'+c+'" /></a>';
		}
		this.html('CGCHAT_opciones_colores', out)
	}
};
cgchat.ajustar_scroll = function() {
	if (cgchat.scrolling) return;
	if (cgchat.order == 'bottom')
		cgchat.attr('CGCHAT_msgs', 'scrollTop', cgchat.attr('CGCHAT_msgs', 'scrollHeight'));
	else
		cgchat.attr('CGCHAT_msgs', 'scrollTop', 0);
};