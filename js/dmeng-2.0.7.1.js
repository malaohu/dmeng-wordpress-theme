/*! Lazy Load 1.9.3 - MIT license - Copyright 2010-2013 Mika Tuupola */
!function(a,b,c,d){var e=a(b);a.fn.lazyload=function(f){function g(){var b=0;i.each(function(){var c=a(this);if(!j.skip_invisible||c.is(":visible"))if(a.abovethetop(this,j)||a.leftofbegin(this,j));else if(a.belowthefold(this,j)||a.rightoffold(this,j)){if(++b>j.failure_limit)return!1}else c.trigger("appear"),b=0})}var h,i=this,j={threshold:0,failure_limit:0,event:"scroll",effect:"show",container:b,data_attribute:"original",skip_invisible:!0,appear:null,load:null,placeholder:"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"};return f&&(d!==f.failurelimit&&(f.failure_limit=f.failurelimit,delete f.failurelimit),d!==f.effectspeed&&(f.effect_speed=f.effectspeed,delete f.effectspeed),a.extend(j,f)),h=j.container===d||j.container===b?e:a(j.container),0===j.event.indexOf("scroll")&&h.bind(j.event,function(){return g()}),this.each(function(){var b=this,c=a(b);b.loaded=!1,(c.attr("src")===d||c.attr("src")===!1)&&c.is("img")&&c.attr("src",j.placeholder),c.one("appear",function(){if(!this.loaded){if(j.appear){var d=i.length;j.appear.call(b,d,j)}a("<img />").bind("load",function(){var d=c.attr("data-"+j.data_attribute);c.hide(),c.is("img")?c.attr("src",d):c.css("background-image","url('"+d+"')"),c[j.effect](j.effect_speed),b.loaded=!0;var e=a.grep(i,function(a){return!a.loaded});if(i=a(e),j.load){var f=i.length;j.load.call(b,f,j)}}).attr("src",c.attr("data-"+j.data_attribute))}}),0!==j.event.indexOf("scroll")&&c.bind(j.event,function(){b.loaded||c.trigger("appear")})}),e.bind("resize",function(){g()}),/(?:iphone|ipod|ipad).*os 5/gi.test(navigator.appVersion)&&e.bind("pageshow",function(b){b.originalEvent&&b.originalEvent.persisted&&i.each(function(){a(this).trigger("appear")})}),a(c).ready(function(){g()}),this},a.belowthefold=function(c,f){var g;return g=f.container===d||f.container===b?(b.innerHeight?b.innerHeight:e.height())+e.scrollTop():a(f.container).offset().top+a(f.container).height(),g<=a(c).offset().top-f.threshold},a.rightoffold=function(c,f){var g;return g=f.container===d||f.container===b?e.width()+e.scrollLeft():a(f.container).offset().left+a(f.container).width(),g<=a(c).offset().left-f.threshold},a.abovethetop=function(c,f){var g;return g=f.container===d||f.container===b?e.scrollTop():a(f.container).offset().top,g>=a(c).offset().top+f.threshold+a(c).height()},a.leftofbegin=function(c,f){var g;return g=f.container===d||f.container===b?e.scrollLeft():a(f.container).offset().left,g>=a(c).offset().left+f.threshold+a(c).width()},a.inviewport=function(b,c){return!(a.rightoffold(b,c)||a.leftofbegin(b,c)||a.belowthefold(b,c)||a.abovethetop(b,c))},a.extend(a.expr[":"],{"below-the-fold":function(b){return a.belowthefold(b,{threshold:0})},"above-the-top":function(b){return!a.belowthefold(b,{threshold:0})},"right-of-screen":function(b){return a.rightoffold(b,{threshold:0})},"left-of-screen":function(b){return!a.rightoffold(b,{threshold:0})},"in-viewport":function(b){return a.inviewport(b,{threshold:0})},"above-the-fold":function(b){return!a.belowthefold(b,{threshold:0})},"right-of-fold":function(b){return a.rightoffold(b,{threshold:0})},"left-of-fold":function(b){return!a.rightoffold(b,{threshold:0})}})}(jQuery,window,document);

/* * JS for DMENG 2.0  * author@steven.chan.chihyu * website@www.dmeng.net *  */
(function($){

var dmengRefreshIcon = '<span class="glyphicon glyphicon-refresh rotate"></span>';

//~ @function
function dmeng_lazyload(){
	$(".entry-thumbnail img,img.avatar,img.look").lazyload({
		effect : "show"
	});
	$("#sidebar img.avatar,#sidebar img.look").lazyload({ 
		effect : "show"
	});
}

//~ @function set cookie
function dmengSetCookie(c_name,value,expire,path){
	var exdate=new Date();
	exdate.setTime(exdate.getTime()+expire*1000);
	document.cookie=c_name+ "=" +escape(value)+((expire==null) ? "" : ";expires="+exdate.toGMTString())+((path==null) ? "" : ";path="+path);
}
//~ @function get cookie
function dmengGetCookie(c_name){
	if (document.cookie.length>0){
		c_start=document.cookie.indexOf(c_name + "=");
		if (c_start!=-1){ 
			c_start=c_start + c_name.length+1;
			c_end=document.cookie.indexOf(";",c_start);
			if (c_end==-1) c_end=document.cookie.length;
			return unescape(document.cookie.substring(c_start,c_end));
		}
	}
	return ""
}
//~ @function get query arg
function dmengGetQueryString(name){
     var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
     var r = window.location.search.substr(1).match(reg);
     if(r!=null)return  unescape(r[2]); return null;
}
//~ @function scroll to hash
function dmengGoLocationHash(h){
	var t = parseInt($('#masthead .navbar').outerHeight(true));
	if ( h!=null && h!="" && $(h).length > 0 ) $('html,body').animate({scrollTop:$(h).offset().top-t}, 'normal');
}
//~ @action scroll to hash ( when the hash change )
$(window).on('hashchange', function() {
	dmengGoLocationHash(window.location.hash);
});
//~ @function scroll to hash
function dmengFixedSidebarLastChild(rs){
	e = $('#sidebar aside').last();
	if( e.length<=0 || $('#content').height() <= $('#sidebar').height() ) return;
	var st = $(window).scrollTop();
	if(!e.attr('data-offset-top') || $.trim(e.css('position'))=='static' || rs) e.attr('data-offset-top',e.offset().top);
	var ot = e.attr('data-offset-top');
	var et = parseInt(ot) - st;
	var t = parseInt($('#masthead .navbar').outerHeight(true));
	var w = $(window).width();
	var h = $(window).height();
	var dh = $(document).outerHeight(true);
	e.css('width',$('#sidebar').width());

	if( w>991 ){
		if( et<=t ) e.css({'position':'fixed','top':t});
		var c = $('#colophon');
		if( ( st + c.outerHeight(true) + e.outerHeight() + t ) >= dh ) e.css({'position':'absolute','top':c.offset().top - e.outerHeight(true) - $('#masthead').outerHeight(true) });
	}
	if( w<992 || et>t ) e.css({'position':'static','top':0});
}
//~ @function set wp nonce cookie
function set_dmeng_nonce(){
	$.ajax({
		type: 'POST', url: ajaxurl, data: { 'action' : 'dmeng_create_nonce' },
		success: function(response) {
			dmengSetCookie('dmeng_check_nonce',$.trim(response),3600,'/');
		},
		error: function(){
			set_dmeng_nonce();
		}
	});
}
//~ @var get wp nonce cookie
var wpnonce = dmengGetCookie('dmeng_check_nonce');
//~ @action set wp nonce cookie ( if wp nonce is null or empty )
if (wpnonce==null || wpnonce=="") set_dmeng_nonce();
//~ @function update traffic
function update_dmeng_traffic(t,p){
	$.ajax({
		type: 'POST', 
		url: ajaxurl, 
		data: {
			'action' : 'dmeng_tracker_ajax',
			'type' : t,
			'pid' : p,
			'wp_nonce' : dmengGetCookie('dmeng_check_nonce')
		},
		success: function(response) {
			//~ @action reset wp nonce ( if response invalid ) and try again
			if($.trim(response)==='NonceIsInvalid'){
				set_dmeng_nonce();
				update_dmeng_traffic(t,p);
			}
		},
		error: function(){
			//~ @action try again ( if error )
			update_dmeng_traffic(t,p);
		}
	});
}
//~ vote+1
function dmeng_vote_count_plus(v,e){
	if(v=='up'){
		$(e).parent().attr("data-votes-up",parseInt($(e).parent().attr("data-votes-up"))+1);
		$(e).children('.votes').html(parseInt($(e).children('.votes').html())+1);
	}
	if(v=='down'){
		$(e).parent().attr("data-votes-down",parseInt($(e).parent().attr("data-votes-down"))+1);
		$(e).siblings('a').children('.votes').html(parseInt($(e).siblings('a').children('.votes').html())-1);
	}
}
//~ vote highlight
function dmeng_vote_highlight(e){
	var v = $(e).hasClass('up') ? 'up' : 'down';
	dmeng_vote_count_plus(v,e);
	$(e).addClass("highlight");
	$(e).parent().addClass("disabled");
}
//~ @function vote
function dmeng_vote(t,i,v,e){
	$.ajax({
		type: 'POST', 
		url: ajaxurl, 
		data: {
			'action' : 'dmeng_vote_ajax',
			'type' : t,
			'id' : i,
			'vote' : v,
			'wp_nonce' : dmengGetCookie('dmeng_check_nonce')
		},
		success: function(response) {
			//~ @action reset wp nonce ( if response invalid ) and try again
			if($.trim(response)==='NonceIsInvalid'){
				set_dmeng_nonce();
				dmeng_vote(t,i,v,e);
			}else if($.trim(response)==='ok'){
				dmeng_vote_highlight(e);
				dmengSetCookie('dmeng_vote_'+t+'_'+i,v,3600*24,'/');
			}
		},
		error: function(){
			//~ @action try again ( if error )
			dmeng_vote(t,i,v,e);
		}
	});
}
//~ @function set comment sticky
function dmeng_set_comment_sticky(c,s){
	$.ajax({
		type: 'POST', 
		url: ajaxurl, 
		data: {
			'action' : 'dmeng_comment_sticky',
			'post_id' : $('article#content').attr('data-post-id'),
			'comment_id' : c,
			'sticky' : s,
			'wp_nonce' : dmengGetCookie('dmeng_check_nonce')
		},
		success: function(response) {
			//~ @action reset wp nonce ( if response invalid ) and try again
			if($.trim(response)==='NonceIsInvalid'){
				set_dmeng_nonce();
				dmeng_set_comment_sticky(c,s);
			}
		},
		error: function(){
			//~ @action try again ( if error )
			dmeng_set_comment_sticky(c,s);
		}
	});
}
//~ @function comment sticky remove
function dmeng_comment_sticky_remove(e,i,t){
		e.remove();
		$('.comment-sticky.'+i).removeClass('active');
		if(t.next().length<=0) t.addClass('hide');
}
//~ @function comment sticky
function dmeng_comment_sticky(e){
	var l = $(e).parents("li.comment");
	var lid = l.attr('data-comment-id');
	var ss = $('#sticky-comments');
	var s = ss.children('#sticky-comment-'+lid);
	var t = $('.sticky-title');
	if(s.length>0){
		dmeng_comment_sticky_remove(s,lid,t);
		dmeng_set_comment_sticky(lid,0);
		return;
	}
	dmeng_set_comment_sticky(lid,ss.children().length);
	$(e).addClass('active');
	if(t.hasClass('hide')) t.removeClass('hide');
	var c = l.clone(true);
	c.attr('id','sticky-'+c.attr('id')).removeClass().addClass('comment sticky-comment list-group-item').children('.vote-group').remove();
	c.find('#comment-author, .top-level').remove();
	c.find('#comment-meta').prepend(c.find('cite').unbind()).children('.comment-sticky').bind({ click: function(){ dmeng_comment_sticky_remove(s,lid,t) } });
	var r = c.find('#comment-meta').children('.comment-reply-link');
	r.attr('onclick',r.attr('onclick').replace("comment", "sticky-comment"));
	t.after(c);
	dmengGoLocationHash("#sticky-comments");
}
//~ @action sticky
$(document).on("click",".comment-sticky",function(){
	dmeng_comment_sticky($(this));
});
//~ @function get comments list
function dmeng_get_comments(c,m,e){
	$(e).html('<div id="loading">'+dmengRefreshIcon+'</div>');
	$.ajax({
		type: 'POST', 
		url: ajaxurl, 
		data: {
			'action' : 'dmeng_get_comments',
			'post_id' : $('article#content').attr('data-post-id'),
			'cpage' : c,
			'max_page' : m,
			'wp_nonce' : dmengGetCookie('dmeng_check_nonce')
		},
		success: function(response) {
			//~ @action reset wp nonce ( if response invalid ) and try again
			if($.trim(response)==='NonceIsInvalid'){
				set_dmeng_nonce();
				dmeng_get_comments(c,m,e);
			}
			if(response.search("data-comment-id")>0){
				$(e).html(response);
				dmeng_lazyload();
			}
		},
		error: function(){
			//~ @action try again ( if error )
			dmeng_get_comments(c,m,e);
		}
	});
}
//~ @action post comments
$(document).on("submit","#commentform",function(){
		$('#commentsubmit').addClass('disabled').append(dmengRefreshIcon);
		$('#comment-error-alert').fadeOut().html('');
		var ajaxurl = $('#commentform').attr('action');
		var author = $("input[name=author]").val();
		var email = $("input[name=email]").val();
		var url = $("input[name=url]").val();
		var post_id = $("input[name=comment_post_ID]").val();
		var parent_id = $("input[name=comment_parent]").val();
		var comment_content = $("textarea[name=comment]").val();
		var nonce = $("input[name=_wp_unfiltered_html_comment_disabled]").val();
		$.ajax({
			type: 'POST', 
			url: ajaxurl, 
			data: {
				'comment_post_ID' : post_id,
				'comment_parent' : parent_id,
				'comment' : comment_content,
				'_wp_unfiltered_html_comment_disabled' : nonce ? nonce : '',
				'author' : author ? author : '',
				'email' : email ? email : '',
				'url' : url ? url : ''
			},
			success: function(response) {
				$('.entry-content').html($(response).find(".entry-content").html());
				$('#comments').html($(response).find("#comments").html());
				dmeng_lazyload();
			},
			error: function(response){
				var e = response.responseText;
				if(e.search("#error-page")>0){
					$('#comment-error-alert').html($.trim(e.match(/<body[^>]*>((.|[\n\r])*)<\/body>/im)[1])).fadeIn();
					$('#commentsubmit').removeClass('disabled').children('.glyphicon-refresh').fadeOut();
				}
			}
		});
		return false;
});
$(document).on("click","#comment-action .look-toggle",function(){
	$("#comment-action .look").toggle();
	return false;
});
//~ @action comment look
$(document).on("click","#comment-action .look",function(){
	var c = $("textarea[name=comment]");
	c.focus();
	c.val( c.val() + $(this).children('img').attr('alt') );
	$("#comment-action .look").hide();
	return false;
});
//~ @action pagination comments
$(document).on("click","#pagination-comments a",function(){
	dmeng_get_comments(parseInt($(this).html()),parseInt($(this).parent().attr('data-max-page')),"#thread-comments");
	dmengGoLocationHash('#thread-comments');
	return false;
});
//~ @action votes
$(document).on("click",".vote-group a",function(){
	var parent = $(this).parent();
	if(parent.hasClass("disabled")) return;
	var vid = parent.attr('data-vote-id');
	var type = $.trim(parent.attr('data-vote-type'))=='post' ? 'post' : 'comment';
	var vote = $(this).hasClass('up') ? 'up' : 'down';
	dmeng_vote(type,vid,vote,this);
});
//~ @action show votes
$(document).on("mouseenter",".vote-group",function(){
	$(this).attr('data-original-title',function(){
		return '\u2605 '+$(this).attr('data-votes-up')+' \u2606 '+$(this).attr('data-votes-down');
	});
	$(this).tooltip('show');
});
//~ @action show dropdown
$(document).on("mouseenter",".dropdown",function(){
	if( $(window).width() > 767 ) $(this).addClass('open'); 
	$(this).children('.dropdown-toggle').attr('href', 'javascript:;');
}).on("mouseleave",".dropdown",function(){
	if( $(window).width() > 767 ) $(this).removeClass('open'); 
});
//~ @action article index caret
$(document).on("click",".article_index h5",function(){
	$(this).parent('.article_index').children('ul').toggle();
});
//~ @action dmeng friend url
$(document).on("click","#dmeng_friend_url",function(){
	$(this).select();
});
//~ @function exchange
function dmeng_exchange(e){
	
	$(e).siblings('button').hide();
	$(e).addClass('disabled').append(dmengRefreshIcon);

	$.ajax({
		type: 'POST', 
		url: ajaxurl, 
		data: {
			'action' : 'dmeng_exchange_ajax',
			'post_id' : $('article#content').attr('data-post-id'),
			'wp_nonce' : dmengGetCookie('dmeng_check_nonce')
		},
		success: function(response) {
			//~ @action reset wp nonce ( if response invalid ) and try again
			if($.trim(response)==='NonceIsInvalid'){
				set_dmeng_nonce();
				dmeng_exchange(e);
			}
			var m = '';
			if(response.error) var m = response.error;
			if(response.success) var m = response.success;
			$(e).hide();
			$(e).parent().prepend('<p class="text-success">'+m+'</p>');
			if(response.success) location.reload();
		},
		error: function(){
			//~ @action try again ( if error )
			dmeng_exchange(e);
		}
	});
}
//~ @action exchange gift
$(document).on("click",".btn-exchange",function(){
	if( isUserLoggedIn==0 ){
		window.location.href = loginUrl;
		return;
	}
	$('.gift-exchange-modal').modal('show');
}).on("click",".btn-exchange-submit",function(){
	if( isUserLoggedIn==0 ){
		window.location.href = loginUrl;
		return;
	}
	dmeng_exchange($(this));
});
$(document).on("click","a",function(){
	if( $(window).width()<768 ) return;
	if( $(this).attr('target')=='_blank' ) return;
	if( $(this).parent('#pagination-comments').length>0 ) return;
	var url = $(this).attr('href');
	var u = document.createElement('a');
			u.href = url,
			ru = u.protocol+u.hostname+u.pathname+u.search,
			l = location,
			rl = l.protocol+l.hostname+l.pathname+l.search;
	if(ru==rl) return;
	var urlreg=/^((https|http|ftp|rtsp|mms)?:\/\/)+[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/;
	if(urlreg.test(url)){
		n = $('#masthead .navbar');
		if( n.children('.progress').length<=0 ){
			n.prepend('<div class="progress"><div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar"></div></div>');
		}
	}
});
function dmeng_do_post(formid, posturl, postdata, contentid){
	$(formid).find('[type="submit"]').addClass('disabled').append(dmengRefreshIcon);
	$.ajax({
		type: 'POST', 
		url: posturl,
		data: postdata,
		success: function(response) {
			$(contentid).html($(response).find(contentid).html());
			dmeng_lazyload();
		},
		error: function(){
			dmeng_do_post(formid, posturl, postdata, contentid);
		}
	});
}
$(document).on("submit","#pmform",function(){
	var formid = '#pmform';
	var p = $(formid);
	dmeng_do_post(
			formid, 
			location.href, 
			{
			'pmNonce' : p.find('[name="pmNonce"]').val(),
			'pm' : p.find('[name="pm"]').val()
			},
			'#content'
	);
	return false;
});
$(document).on("submit","#creditform",function(){
	var formid = '#creditform';
	var p = $(formid);
	dmeng_do_post(
			formid, 
			location.href, 
			{
			'creditNonce' : p.find('[name="creditNonce"]').val(),
			'creditChange' : p.find('[name="creditChange"]:checked').val(),
			'creditNum' : p.find('[name="creditNum"]').val(),
			'creditDesc' : p.find('[name="creditDesc"]').val()
			},
			'#content'
	);
	return false;
});
function dmeng_set_navbar_fixed_top(rs){
	var m = $('#masthead'),
			h = $('#masthead .header-content'),
			n = $('#masthead .navbar'),
			ht = h.outerHeight(true),
			st = $(window).scrollTop();

	m.css('height', n.outerHeight()+ht);

	if(st>=ht &&  $(window).width()>=768){
		n.removeClass('navbar-static-top').addClass('navbar-fixed-top');
	}
	if(st<ht){
		n.removeClass('navbar-fixed-top').addClass('navbar-static-top');
	}
}
$(document).on("click",".user-profile .friend",function(){
	var u = prompt(dmengFriend.title, dmengFriend.url);
});
//~ @action set font size
$(document).on("click","#set-font-small",function(){
	$(this).addClass("disabled");
	$(this).siblings("#set-font-big").removeClass("disabled");
	$("#content .entry-content").css({"font-size":"14px","line-height":"24px"});
}).on("click","#set-font-big",function(){
	$(this).addClass("disabled");
	$(this).siblings("#set-font-small").removeClass("disabled");
	$("#content .entry-content").css({"font-size":"16px","line-height":"26px"});
});
//~ @action float button
$(document).on("click","#goTop",function(){
	$('html,body').animate({scrollTop: '0px'}, 800);
}).on("click","#goBottom",function(){
	$('html,body').animate({scrollTop:$('#colophon').offset().top}, 800);
}).on("click","#goComments",function(){
	$('html,body').animate({scrollTop:$('#comments').offset().top}, 800);
}).on("click","#refresh",function(){
	location.reload();
});
//~ @jquery ready
$(document).ready(function(){
	//~ @action scroll to hash
	dmengGoLocationHash(window.location.hash);
	//~ each vote
	$(".vote-group").each(function(){
		if($(this).hasClass("disabled")) return;
		var i = $(this).attr('data-vote-id');
		var t = $.trim($(this).attr('data-vote-type'))=='post' ? 'post' : 'comment';
		var c = dmengGetCookie('dmeng_vote_'+t+'_'+i);
		if(c!=null && (c=='up'||c=='down')){
			$(this).addClass("disabled");
			$(this).children("."+c).addClass("highlight");
		}
	});
	// navbar fixed
	dmeng_set_navbar_fixed_top();
	// fixed sidebar last child
	dmengFixedSidebarLastChild(0);

	$('.header-profile [data-toggle="tooltip"]').tooltip({placement: 'auto',trigger: 'manual'}).tooltip('show');
	
	//~ @action set friend cookie ( credit )
	if(dmengGetQueryString('fid')) dmengSetCookie('dmeng_friend',dmengGetQueryString('fid'),86400,'/');
	
	//~ @action update traffic
	if(!(typeof(dmengTracker) == "undefined")) update_dmeng_traffic(dmengTracker.type,dmengTracker.pid);

	dmeng_lazyload();

});
$(window).resize(function(){
	// navbar fixed
	dmeng_set_navbar_fixed_top(1);
	// fixed sidebar last child
	dmengFixedSidebarLastChild(1);
});
$(window).scroll(function(){
	// navbar fixed
	dmeng_set_navbar_fixed_top();
	// fixed sidebar last child
	dmengFixedSidebarLastChild(0);
});
})(jQuery);
//~ comment reply 
var addComment = {
	moveForm : function(comment, parent, respond, post) {
		var c = '#'+comment;
		var l = jQuery('#commentform .help-block a');
		if(jQuery(c).next("form").length>0){
			jQuery(c).find('.comment-reply-link').removeClass('highlight');
			jQuery('#comment_parent').val('0');
			jQuery('#'+respond).append(jQuery('#commentform'));
			if(l.length>0) l.attr('href',l.attr('href').replace(/%23[^/]+$/,'%23'+respond));
		}else{
			jQuery(c).find('.comment-reply-link').addClass('highlight');
			jQuery('#comment_parent').val(parent);
			jQuery(c).after(jQuery('#commentform'));
			if(l.length>0) l.attr('href',l.attr('href').replace(/%23[^/]+$/,'%23'+comment));
		}
		return false;
	}
}
