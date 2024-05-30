/**
* CG Chat Component  - Joomla 4.x/5.x Component
* Version			: 1.1.0
* Package			: CG Chat
* copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : Kide ShoutBox
*/

var cgchat = {
	debug: true,
	abort_time_extra: 6000,
	sids: [],
	mostrar_colores_iniciado: false, // color
	private: false, // private chat
	retardo_avisar: false,
	shift_pressed: false,
	shift_priv_pressed: false,
	popup: null,
	popup_smileys: null,
	scrolling: false,
	scrolling_privados: false,
	encendido: false,

	$: function(id) {
		return document.getElementById(id);
	},
	defined: function(value) {
		return typeof(value) != "undefined";
	},
	css: function(id, param, value) {
		if (!this.checkID(id)) return;
		if (this.defined(value)) this.$(id).style[param] = value;
		else return this.$(id).style[param];
	},
	attr: function(id, param, value) {
		if (!this.checkID(id)) return;
		if (this.defined(value)) this.$(id)[param] = value;
		else return this.$(id)[param];
	},
	val: function(id, v) {
		if (!this.checkID(id)) return;
		if (this.defined(v)) this.$(id).value = v;
		else return this.$(id).value;
	},
	html: function(id, value) {
		if (!this.checkID(id)) return;
		if (this.defined(value)) this.$(id).innerHTML = value;
		else return this.$(id).innerHTML;
	},
	show: function(id,s) {
		if (!this.checkID(id)) return;
		if (this.defined(s)) s = s ? "" : "none";
		else s = this.css(id,"display") == "none" ? "" : "none";
		this.css(id,"display",s);
	},
	toggle: function(id) {
		if (!this.checkID(id)) return;
        s = this.css(id,'display');
        if (this.defined(s) && (s != '')) this.css(id,"display",'');
        else this.css(id,"display",'none');
	},
	visible: function(id,s) {
		if (!this.checkID(id)) return;
		if (this.defined(s)) s = s ? "" : "hidden";
		else s = this.css(id,"visibility") == "hidden" ? "" : "hidden";
		this.css(id,"visibility",s);
	},
	foco: function(id) {
		if (!this.checkID(id)) return;
		this.$(id).focus();
	},
	checkID: function(id) {
		if (this.$(id)) return true;
		if (this.debug) {
			this.error("The element with id '"+id+"' doesn't exists.", 2);
		}
	},
	log: function(msg, f, l) {
		if (this.debug && console && console.log) {
			console.log("CG Chat error: "+msg+" at "+f+":"+l);
		}
	},
	error: function(msg, n, e) {
		try {
			var up = 0;
			if (!this.defined(e)) {
				up++;
				var e = new Error();
			}
			s = e.stack;
			if (s.indexOf("@") != -1) {
				var s = s.split("\n")[n+up].split("@")[1].split(":");
				var l = s[s.length-1];
				s[s.length-1] = '';
				s = s.join(':'); 
				var f = s.substr(0, s.length-1);
				this.log(msg, f, l);
			}
			else if(s.indexOf("at ") != -1) {
				var s = s.split("\n")[n+up].match(/\(([^\)]+)/)[1].split(":");
				var l = s[s.length-2];
				s[s.length-1] = '';
				s[s.length-2] = '';
				s = s.join(':'); 
				var f = s.substr(0, s.length-2);
				this.log(msg, f, l);
			}
		}
		catch(err) {}
	},
	form: function(param, v) {
		if (typeof(v) == "undefined") return document.forms.kideForm[param].value;
		else document.forms.kideForm[param].value = v;
	},
	onLoad: function(func, func2) {
		if (window.addEventListener) window.addEventListener("load", func, false);
		else if (window.attachEvent) window.attachEvent("onload", func);
		else if (cgchat.defined(func2)) (func2)();
		else (func)();
	},
	addHTMLInBody: function(html) {
		this.onLoad(function() {
			var div = document.createElement('div');
			div.setAttribute('class', 'CGCHAT_div');
			div.innerHTML = html;
			cgchat.insertAfter(div, document.body.lastChild);
		}, function(){
			document.write(html);
		}); 
	},
	iniciar: function() { // init
		if (!cgchat.encendido) {
			cgchat.encendido = true;
			cgchat.attr('encendido', 'src', cgchat.img_encendido[2]);
			cgchat.sessions();
			cgchat.recargar();
			cgchat.events.lanzar('onIniciar');
		}
	},
	open_popup: function() {
		if (this.popup) this.popup.close();
		this.popup = window.open(this.popup_url, '_blank', 'toolbar=0,location=0,menubar=0,directories=0,resizable=1,scrollbars=1,width=800,height=600');
	},
	open_popup_smileys: function(options) {
		if (this.popup_smileys) this.popup_smileys.close();
		var params = "menubar=0,resizable=1,location=0,status=0,scrollbars=1";
		if (options && options.length > 0) params += ','+options;
		this.popup_smileys = window.open(this.ajax_url+'&task=more_smileys&window=1', '_blank', params);
	},
	text: function(row) {
		return cgchat.defined(row.textContent) ? row.textContent : row.text;
	},
	recargar: function() { // reload
		cgchat.ajax("reload");
	},
	getUser: function(sid) {
		return this.defined(this.sids[sid]) ? this.sids[sid] : null;
	},
	getUserById: function(uid) {
		for(var i in this.sids) {
			if (this.sids[i].id == uid)
				return this.sids[i];
		}
	},
	sessions: function() {
		cgchat.ajax("sessions");
	},
	sonido: function() { // sound
		if (this.sound != -1) {
			if (this.sound == 1) {
				this.sound = 0;
				this.attr('sound', 'src', this.sound_off);
			}
			else {
				this.sound = 1;
				this.attr('sound', 'src', this.sound_on);
				this.play_msg_sound();
			}
			this.save_config("sound", this.sound);
		}
	},
	save_config: function(param, value) {
        var config = document.cookie.match(/cgchat_config=([^;]*)/);
        if (config && config[1]) {
            config = decodeURIComponent(config[1]);
            if (config.search(eval('/'+param+'=/')) > -1)
                config = config.replace(eval('/'+param+'=[^;]*/'), param+'='+value);
            else
                config += ';'+param+'='+value;
		}
		else
			config = param+'='+value;
       	issecure = "";
        if (window.location.protocol == "https:") issecure="secure;";
        document.cookie = 'cgchat_config='+encodeURIComponent(config)+'; path=/;samesite=lax;'+issecure;
	},
	ahora: function() {
		var ya = new Date();
		var m = ya.getMonth() + 1;
		ya = ya.getDate()+"-"+(m < 10 ? "0" : "")+m+" "+ya.getHours()+":"+(ya.getMinutes() < 10 ? "0" : "")+ya.getMinutes()+":"+(ya.getSeconds() < 10 ? "0" : "")+ya.getSeconds();
		return ya;
	},
	in_array: function(e, a) {
		for (var i=0; i<a.length; i++)
			if (a[i] == e) return true;
		return false;
	},
	insertAfter: function(newElement,targetElement) {
		var parent = targetElement.parentNode;
		if (parent.lastchild == targetElement) 
			parent.appendChild(newElement);
		else 
			parent.insertBefore(newElement, targetElement.nextSibling);
	},
	trim: function(a,e) {
		//http://phpjs.org/functions/trim:566
		var c,d=0,b=0;a+="";if(!e)c=" \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";else{e+="";c=e.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g,"$1")}d=a.length;for(b=0;b<d;b++)if(c.indexOf(a.charAt(b))===-1){a=a.substring(b);break}d=a.length;for(b=d-1;b>=0;b--)if(c.indexOf(a.charAt(b))===-1){a=a.substring(0,b+1);break}return c.indexOf(a.charAt(0))===-1?a:"";
	},
	htmlspecialchars_decode: function(b,a) {
		//http://phpjs.org/functions/htmlspecialchars_decode:427
		var f=0,c=0,e=false;if(typeof a==="undefined")a=2;b=b.toString().replace(/&lt;/g,"<").replace(/&gt;/g,">");var d={ENT_NOQUOTES:0,ENT_HTML_QUOTE_SINGLE:1,ENT_HTML_QUOTE_DOUBLE:2,ENT_COMPAT:2,ENT_QUOTES:3,ENT_IGNORE:4};if(a===0)e=true;if(typeof a!=="number"){a=[].concat(a);for(c=0;c<a.length;c++)if(d[a[c]]===0)e=true;else if(d[a[c]])f=f|d[a[c]];a=f}if(a&d.ENT_HTML_QUOTE_SINGLE)b=b.replace(/&#0*39;/g,"'");if(!e)b=b.replace(/&quot;/g,'"');b=b.replace(/&amp;/g,"&");return b;
	},
	check_shift: function(e, up, priv) {
		var code = this.getCode(e);
		if (up) {
			if (code == 16) { //shift
				if (priv)
					this.shift_priv_pressed = false;
				else
					this.shift_pressed = false;
			}
		}
		else if (code != 13) { //enter
			if (priv)
				this.shift_priv_pressed = code == 16;
			else
				this.shift_pressed = code == 16;
		}
	},
	getCode: function(e) {
		return e.keyCode ? e.keyCode : e.which ? e.which : e.charCode;
	},
	isEnter: function(e) {
		return this.getCode(e) == 13;
	},
	pressedEnter: function(e, priv) {
		if (this.isEnter(e)) {
			if ((!priv && this.shift_pressed) || (priv && this.shift_priv_pressed))
				return true;
			else if (priv) 
				this.ajax("privados_insertar");
			else
				this.sm();
			return false;
		} 
		else
			return true;
	},
	tiempo: function(t) {
		t = Number(t);
		if (t <= 0) {
			cgchat.show('CGCHAT_tiempo_p', false);
			return;
		}
		cgchat.show('CGCHAT_tiempo_p', true);
		t -= this.retardo;
		var time = new Date();
		time = time.getTime();
		t = Math.floor((time/1000) - t);
		if (t <= 0) t = 1;
		
		var out = "";
		var i;
		var salir = false;
		var datos = new Array();
		datos[0] = new Array();
		datos[0][0] = Math.floor(t/2592000);
		datos[0][1] = Math.floor((t - datos[0][0]*2592000)/86400); 
		datos[0][2] = Math.floor((t - datos[0][0]*2592000 - datos[0][1]*86400)/3600);
		datos[0][3] = Math.floor((t - datos[0][0]*2592000 - datos[0][1]*86400 - datos[0][2]*3600)/60);
		datos[0][4] = Math.floor(t - datos[0][0]*62592000 - datos[0][1]*86400 - datos[0][2]*3600 - datos[0][3]*60);
		datos[1] = [1, 3, 7, 10];
		
		for (i=0;i<=4 && !salir;i++) {
			if (datos[0][i]) {
				salir = true;
				out += datos[0][i]+" "+this.msg.lang[datos[0][i]!=1 ? i*2+1 : i*2];
				if (i < 4 && datos[0][i] <= datos[1][i] && datos[0][i+1]) 
					out += " "+datos[0][i+1]+" "+this.msg.lang[datos[0][i+1]!=1 ? (i+1)*2+1 : (i+1)*2];
			}
		}
		if (!out) out = '1 '+this.msg.lang[8];
		cgchat.html('CGCHAT_tiempoK', out); 
	},
	insertSmile: function(text) {
		cgchat.insertAtCursor(document.forms.kideForm.txt, text);
	},
	insertAtCursor: function(element,text) {
		var txtarea = element;
		var scrollPos = txtarea.scrollTop;
		var strPos = 0;
		var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ? 
			"ff" : (document.selection ? "ie" : false ) );
		if (br == "ie") { 
			txtarea.focus();
			var range = document.selection.createRange();
			range.moveStart ('character', -txtarea.value.length);
			strPos = range.text.length;
		}
		else if (br == "ff") strPos = txtarea.selectionStart;

		var front = (txtarea.value).substring(0,strPos);  
		var back = (txtarea.value).substring(strPos,txtarea.value.length); 
		txtarea.value=front+text+back;
		strPos = strPos + text.length;
		if (br == "ie") { 
			txtarea.focus();
			var range = document.selection.createRange();
			range.moveStart ('character', -txtarea.value.length);
			range.moveStart ('character', strPos);
			range.moveEnd ('character', 0);
			range.select();
		}
		else if (br == "ff") {
			txtarea.selectionStart = strPos;
			txtarea.selectionEnd = strPos;
			txtarea.focus();
		}
		txtarea.scrollTop = scrollPos;
	},
	filter_smilies: function(s) {
		s = " "+s+" ";
		for (var i = 0; i < this.smilies.length; i++) {
			s = s.replace(" "+this.smilies[i][0], '<img alt="' + this.smilies[i][0] + '" title="' + this.smilies[i][0] + '" src="' + this.smilies[i][1] + '" class="CGCHAT_icono" />');
			s = s.replace(" "+this.smilies[i][0].toLowerCase(), '<img alt="' + this.smilies[i][0] + '" title="' + this.smilies[i][0] + '" src="' + this.smilies[i][1] + '" class=CGCHAT_icono" />')
		}
		return s;
	},
	tohtml: function(s) {
		s = s.replace(/&/g, "&amp;");
		s = s.replace(/</g, "&lt;");
		s = s.replace(/>/g, "&gt;");
		s = s.replace(/'/g, "&#39;");
		s = s.replace(/"/g, "&quot;");
		return s;
	},
	sm: function() {
		cgchat.ajax("insertar");
		if (!cgchat.encendido)
			cgchat.iniciar();
	},
	retardo_input: function() { // Delay
		this.retardo_avisar = true;
		this.ajax("retardo");
	},
	mostrar_iconos: function() { // show icons
		if (this.$('CGCHAT_iconos')) {
			this.save_config('icons_hidden', this.css('CGCHAT_iconos', 'display') == 'none' ? 0 : 1);
			this.show('CGCHAT_iconos');
		}
	},
	play_msg_sound: function() {
		this.html('CGCHAT_msg_sound', '<audio autoplay style="height:0;width:0"><source src="'+this.sound_src+'" type="audio/mpeg"></audio>');
	},
	mostrar_opciones: function() { // show options
		if (!this.mostrar_colores_iniciado) {
			this.mostrar_colores_iniciado = true;
			this.show_colors();
		}
		this.show('CGCHAT_opciones');
	},
	save_options: function() {
		this.show('CGCHAT_opciones', false);
		if (this.color)
			this.save_config("color", this.color);
		this.save_config("hidden_session", this.attr('hidden_session', 'checked')?1:0);
		if (this.form("CGCHAT_template") != this.template) {
			this.save_config("template", this.form("CGCHAT_template"));
			location.reload();
		}
	},
	set_color: function(c) {
		if (this.can_write) {
			this.color = c;
			this.css('CGCHAT_txt', 'color', "#"+c);
			this.events.lanzar('onSetColor', c);
		}
	},
	borrar: function(id) { // remove
		if (id > 0) {
			this.show("CGCHAT_id_"+id, false);
			this.show("CGCHAT_mensaje", false)
			this.ajax("borrar", id);
		}
		else
            alert(this.msg.mensaje_borrar);
	},
    ask_private : function(auser) {
        this.ajax("ask_private", [auser,document.getElementById('CGCHAT_user_go_to_private').checked]);
    },
    close_private : function() {
        this.ajax("close_private");
    },
    accept_private : function(abool) {
        if (!abool) {
            this.ajax("close_private");
        } else {
            this.ajax("accept_private");
        }
    },
	getDocumentWidth: function() {
		return window.innerWidth ? window.innerWidth : document.documentElement.clientWidth;
	},
	getDocumentHeight: function() {
		return window.innerHeight ? window.innerHeight : document.documentElement.clientHeight;
	},
	banear: function(sid, tipo) { // banned
        this.ajax("ban", [sid,document.getElementById('CGCHAT_user_banear').checked]);
	},
	ajax: function(tipo, tmp) {
		var ajax = new XMLHttpRequest();
		if (tipo == "reload") {  // reload
            lastid = this.n;
            if (cgchat.private) lastid = this.p;
			url = this.ajax_url+"&task=reload&privs="+cgchat.private+"&id="+lastid+"&token="+this.token+'&format=json';
			setTimeout(function(){ajax.abort();}, cgchat.refresh_time+cgchat.abort_time_extra);
			Joomla.request({
				method : 'POST',
				url : url,
				onSuccess: function(data, xhr) {
					var result = JSON.parse(data);
                    if (result.privaterequest) {
                        user = cgchat.getUserById(result.privaterequest);
                        cgchat.show('CGCHAT_GOCHAT',true);
                        msg = cgchat.html('CGCHAT_GOCHAT');
                        msg = msg.replace('%s',user.name);
                        cgchat.html('CGCHAT_GOCHAT',msg);
                    }
					if (result.last_id) {
                        if (cgchat.private) {
                            cgchat.p = result['last_id'];
                        } else {
                            cgchat.n = result['last_id'];
                        }
						cgchat.last_time = result['last_time'];
						for (var i=0; i < result.messages.length; i++) {
							row = result.messages[i];
							cgchat.insertNewContent(row.uid,row.name,row.text,row.url,row.date,row.color,row.row,row.id,row.session,row.session==cgchat.session,row.hora,row.img);
						}
					}
					cgchat.ajustar_scroll();
					cgchat.events.lanzar('onAjaxReload', result);
					cgchat.tiempo(cgchat.last_time);
					setTimeout(cgchat.recargar, cgchat.refresh_time);

				},
				onError: function(message) {console.log(message.responseText)}
			})
			
		}
		else if (tipo == "insertar") { // insert
			var txt = this.val('CGCHAT_txt');
			cgchat.val('CGCHAT_txt', '');
			if (!cgchat.trim(txt)) return;
			this.visible('CGCHAT_img_ajax', true);
			urltxt = encodeURIComponent(txt);
			// JSON : replace @ by / , ~ by <br />, ' by   \\x27
			urltxt = urltxt.replaceAll('%0A',' ~ ').replaceAll('%3A',':').replaceAll('%2F','@').replaceAll(/'/g, '\\x27');
			urltxt = urltxt.replaceAll('%3E','').replaceAll('%3C',''); // cleanup other chars < > 
            color = '';
            if (this.color) color = "&color="+this.color;
			url = this.ajax_url+"&task=add&privs="+cgchat.private+"&txt="+urltxt+"&"+this.token+'=1&format=json'+color;
			Joomla.request({
				method : 'POST',
				url : url,
				onSuccess: function(data, xhr) {
					var result = JSON.parse(data);
					if (result.banned == 1) {
						location.reload();
						return;
					}
					if (result.comment) {
						var texto = result.comment;
						cgchat.insertNewContent(0,'System',texto,'',cgchat.ahora(),'',0,0,0,false,result.hora,'',cgchat.private); 
					}
					if (result.txt && result.txt.length) {
						var texto = result.txt;
						cgchat.insertNewContent(cgchat.userid,cgchat.name,texto.length?texto:txt,cgchat.url,cgchat.ahora(),cgchat.color,cgchat.row,result.id,cgchat.session,true,result.hora,result.img,cgchat.private);
                        if (cgchat.private) {
                            cgchat.p = result.id;
                        } else {
                            cgchat.n = result.id;
                        }
						cgchat.last_time = result.tiempo;
						cgchat.tiempo(cgchat.last_time);
						cgchat.ajustar_scroll();
					}
					cgchat.visible('CGCHAT_img_ajax', false);
					cgchat.events.lanzar('onAjaxInsertar', result); // to check
				},
				onError: function(message) {console.log(message.responseText)}
			})
		}
        else if (tipo == "ask_private") { // ask private messages authorization to one user 
			url = this.ajax_url+"&task=askprivate&user="+tmp[0]+"&private="+tmp[1]+"&"+this.token+"=1&format=json";
            cgchat.show('waiting_private',true);
			Joomla.request({
				method : 'POST',
				url : url,
				onSuccess: function(data, xhr) {
					var out = JSON.parse(data);
                    // cgchat.ajax('sessions');
				},
				onError: function(message) {console.log(message.responseText)}
			})
        }
        else if (tipo == "close_private") { // close private messages
			url = this.ajax_url+"&task=closeprivate&user="+cgchat.userid+"&"+this.token+"=1&format=json";
			Joomla.request({
				method : 'POST',
				url : url,
				onSuccess: function(data, xhr) {
					var out = JSON.parse(data);
                    cgchat.private = 0;
                    cgchat.show('CGCHAT_GOCHAT',false)
                    cgchat.ajax('sessions');
				},
				onError: function(message) {console.log(message.responseText)}
			})
        }
        else if (tipo == "accept_private") { // close private messages
			url = this.ajax_url+"&task=acceptprivate&user="+cgchat.userid+"&"+this.token+"=1&format=json";
			Joomla.request({
				method : 'POST',
				url : url,
				onSuccess: function(data, xhr) {
					var out = JSON.parse(data);
                    cgchat.private = out.private;
                    cgchat.show('CGCHAT_GOCHAT',false);
                    cgchat.ajax('sessions');
				},
				onError: function(message) {console.log(message.responseText)}
			})
        }
		else if (tipo == "borrar") { // remove
			url = this.ajax_url+"&task=borrar&id="+tmp+"&"+this.token+'=1&format=json';
			Joomla.request({
				method : 'POST',
				url : url,
				onSuccess: function(data, xhr) {},
				onError: function(message) {console.log(message.responseText)}
			})
		}
		else if (tipo == "sessions") {
			url = this.ajax_url+"&task=sessions&show_sessions="+cgchat.show_sessions+"&privs="+(cgchat.hidden_session?0:1)+"&"+this.token+"=1&format=json";
			Joomla.request({
				method : 'POST',
				url : url,
				onSuccess: function(data, xhr) {
					var result = JSON.parse(data);
					if (cgchat.show_sessions) {
						cgchat.sids = [];
						cgchat.html('CGCHAT_users', '');
						var alias, name;
                        stillprivate = 0;
						for (var i=result.length-1; i>=0; i--) {
							row = result[i];
							var sid = row.session;
                            if ((cgchat.userid > 0) && (row.userid == cgchat.userid ) && row.private ) {
                                cgchat.private = row.private;
                                stillprivate = true;
                            }
                            if ((row.userid > 0) && (row.userid == cgchat.userid)) { // myself
                                row.class += ' me';
                            }
                            if (row.userid == cgchat.private) {
                                row.class += ' private';
                            }
                            if (row.row == 4 ) { // banned
                                row.class += ' banned';
                            }
							cgchat.sids[sid] = {
									row: row.row,
									name: row.name,
									_class: row.class,
                                    title:  cgchat.rowtitles[row.row],
									session: row.session,
									profile: row.profile,
									private: row.private,
									id: row.userid,
									img: row.img
							};
                            if (row.session == cgchat.session ) {
                                if ((row.row == 4) && (row.banned)) {
                                    msg = cgchat.html('cgchat_banned');
                                    msg = msg.replace('%s',row.banned);
                                    cgchat.html('cgchat_banned',msg);
                                    cgchat.show('cgchat_banned',true);
                                    cgchat.show('CGCHAT_form',false);
                                } else {
                                    cgchat.show('cgchat_banned',false);
                                    cgchat.show('CGCHAT_form',true);
                                    cgchat.show('CGCHAT_user',false);
                                }
                            }
							cgchat.events.lanzar('onAjaxSession', cgchat.getUser(sid));
							cgchat.insert_session(cgchat.getUser(sid));
						}
                        if (!stillprivate) cgchat.private = 0;
					}
					setTimeout(cgchat.sessions, cgchat.refresh_time_session);
				},
				onError: function(message) {console.log(message.responseText)}
			})
			
		} else if (tipo == "retardo") { // Delay
			url = this.ajax_url+"&task=retardo"+"&"+this.token+"=1&format=json";
			Joomla.request({
				method : 'POST',
				url : url,
				onSuccess: function(data, xhr) {
					var result = JSON.parse(data);
					var out = result.time;
					if (out > 0) {
						var time = new Date();
						time = time.getTime();
						out = out - Math.floor((time/1000));
						cgchat.retardo = out;
						cgchat.save_config("retardo", cgchat.retardo);
						if (cgchat.retardo_avisar) {
							alert(cgchat.msg.retardo_frase.replace("%s", out));
						}
					}
				},
				onError: function(message) {console.log(message.responseText)}
			})
		} else if (tipo == "ban") { // banned
			url = this.ajax_url+"&task=ban"+"&"+"session="+tmp[0]+'&flag='+tmp[1]+'&'+this.token+"=1&format=json";
			Joomla.request({
				method : 'POST',
				url : url,
				onSuccess: function(data, xhr) {
					var out = JSON.parse(data);
                    cgchat.show('CGCHAT_user',false);
                    cgchat.ajax('sessions');
				},
				onError: function(message) {console.log(message.responseText)}
			})
		} else if (tipo == "kill") {
			url = this.ajax_url+"&task=kill"+"&"+"session="+cgchat.session+'&'+this.token+"=1&format=json";
			Joomla.request({
				method : 'POST',
				url : url,
				onSuccess: function(data, xhr) {
				},
				onError: function(message) {console.log(message.responseText)}
			})
		} else {
			this.events.lanzar('onAjax_'+tipo, [ajax, tmp]);
		}
	}
};

// events

cgchat.events = {
	list: [],
	add: function(name, func) {
		if (typeof(func) != 'function') return;
		if (!this.list[name])
			this.list[name] = [];
		this.list[name].push(func);
	},
	lanzar: function(name, params) { // start
		var stop = false;
		if (this.list[name]) {
			if (!params) params = [];
			for (var i=0; i<this.list[name].length;i++)
				stop = (this.list[name][i])(params) || stop;
		}
		return stop;
	}
};
// kill session when exiting
window.addEventListener('beforeunload', function (e) {
  // the absence of a returnValue property on the event will guarantee the browser unload happens
  delete e['returnValue'];
  cgchat.ajax("kill");
});

