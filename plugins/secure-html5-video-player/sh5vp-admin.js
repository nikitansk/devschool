
function sh5vp_generateSeed() {
	var charAry = '0123456789QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm';
	var buf = '';
	var seedLength = Math.floor(Math.random() * 60) + 20;
	for (i = 0; i < seedLength; i++) {
		buf += charAry[ Math.floor(Math.random() * charAry.length) ];
	}
	var secure_html5_video_player_key_seed = document.getElementById('secure_html5_video_player_key_seed');
	secure_html5_video_player_key_seed.value = buf;
	return false;	
}


function sh5vp_setActiveTab(tab_link_obj) {
	jQuery('.sh5vp_tab').each(function(index, elem) {
		jQuery(this).removeClass('active');
	});
	tab_link_obj.addClass('active');
	
	var targetTab = tab_link_obj.attr('rel');
	var targetLink = tab_link_obj.attr('id');
	jQuery('#last_tab_sel').val(targetLink);
	
	jQuery('.sh5vp_content_tab').each(function() {
		var curr_tab_content = jQuery(this);
		if (targetTab == curr_tab_content.attr('id')) {
			curr_tab_content.addClass('active');
			curr_tab_content.fadeIn();
			jQuery('.sh5vp_content').height(curr_tab_content.height() + 22);
		}
		else {
			curr_tab_content.removeClass('active');
			curr_tab_content.fadeOut();
		}
	});
	return false;
}


function sh5vp_initTabs() {
	jQuery('.sh5vp_content_tab').each(function() {
		var curr_tab_content = jQuery(this);
		if (! curr_tab_content.hasClass('active')) {
			curr_tab_content.fadeOut(0);
		}
	});
	jQuery('.sh5vp_tabs .sh5vp_tab').each(function() {
		jQuery(this).click(function() {
			return sh5vp_setActiveTab(jQuery(this));
		});
	});
	sh5vp_selectSkin(jQuery('#secure_html5_video_player_skin').val());
}


function sh5vp_selectSkin(skin_name) {
	jQuery('.sh5vp_skin_select').removeClass('active');
	jQuery('#sh5vp_skin_select-'+skin_name).addClass('active');
	jQuery('#secure_html5_video_player_skin').val(skin_name);
}


function sh5vp_insertIntoPost() {
	var vShortcode = jQuery('#sh5vp-video-modal').attr('data-video-shortcode');
	var vFile = jQuery('#sh5vp-video').val();
	var ytCode = jQuery('#sh5vp-youtube_video_id').val();
	var vmCode = jQuery('#sh5vp-vimeo_video_id').val();
	var vWidth = jQuery('#sh5vp-width').val();
	var vHeight = jQuery('#sh5vp-height').val();
	var vPreload = jQuery('#sh5vp-preload').prop('checked');
	var vAutoplay = jQuery('#sh5vp-autoplay').prop('checked');
	var vLoop = jQuery('#sh5vp-loop').prop('checked');
	var vCode = '['+vShortcode;
	if (vFile) vCode += ' file=\'' + vFile + '\'';
	if (ytCode) vCode += ' youtube=\'' + ytCode + '\'';
	if (vmCode) vCode += ' vimeo=\'' + vmCode + '\'';
	if (vWidth) vCode += ' width=\'' + vWidth + '\'';
	if (vHeight) vCode += ' height=\'' + vHeight + '\'';
	if (vPreload) vCode += ' preload=\'yes\'';
	else vCode += ' preload=\'no\'';
	if (vAutoplay) vCode += ' autoplay=\'yes\'';
	else vCode += ' autoplay=\'no\'';
	if (vLoop) vCode += ' loop=\'yes\'';
	else vCode += ' loop=\'no\'';
	vCode += ']';
	send_to_editor(vCode);
}

function sh5vp_previewVideo(field_ids) {
	if (!field_ids) {
		field_ids = {
			video: 'sh5vp-video',
			youtube: 'sh5vp-youtube_video_id',
			vimeo: 'sh5vp-vimeo_video_id',
			width: 'sh5vp-width',
			height: 'sh5vp-height',
			preload: 'sh5vp-preload',
			autoplay: 'sh5vp-autoplay',
			loop: 'sh5vp-loop',
			modal: 'sh5vp-video-modal'
		};
	}
	var opts = {
		video: jQuery('#'+field_ids.video).val(),
		key: jQuery('#'+field_ids.video).find(':selected').attr('data-key'),
		youtube: jQuery('#'+field_ids.youtube).val(),
		vimeo: jQuery('#'+field_ids.vimeo).val(),
		width: jQuery('#'+field_ids.width).val(),
		height: jQuery('#'+field_ids.height).val(),
		preload: jQuery('#'+field_ids.preload).prop('checked'),
		autoplay: jQuery('#'+field_ids.autoplay).prop('checked'),
		loop: jQuery('#'+field_ids.loop).prop('checked')
	};
	if (!opts.width) {
		opts.width = jQuery('#'+field_ids.modal).attr('data-default-width');
	}
	if (!opts.height) {
		opts.height = jQuery('#'+field_ids.modal).attr('data-default-height');
	}
	sh5vp_videoModal(opts, field_ids);
}

function sh5vp_previewIframeUrl(opts, field_ids) {
	//$opts - hash with keys: file, youtube, vimeo, width, height, preload, autoplay, loop, key
	var elem = jQuery('#'+field_ids.modal);
	var youtube_override_type = elem.attr('data-youtube_override_type');
	var plugin_dir = elem.attr('data-plugin_dir');
	if ('primary' == youtube_override_type || !opts.video) {
		if (opts.youtube) {
			var autoplay_youtube = '0';
			if (opts.autoplay) {
				autoplay_youtube = '1';
			}
			return "http://www.youtube.com/embed/"+opts.youtube+"?autoplay="+autoplay_youtube+"&origin="+location.origin;
		}
		else if (opts.vimeo) {
			var autoplay_vimeo = '0';
			if (opts.autoplay) {
				autoplay_vimeo = '1';
			}
			var loop_vimeo = '0';
			if (opts.loop) {
				loop_vimeo = '1';
			}
			return "http://player.vimeo.com/video/"+opts.vimeo+"?autoplay="+autoplay_vimeo+"&amp;loop="+loop_vimeo;
		}
	}
	return plugin_dir+"/getiframe.php?k=" + encodeURI(opts.key) 
		+ "&file=" + encodeURI(opts.video)
		+ "&youtube=" + encodeURI(opts.youtube)
		+ "&vimeo=" + encodeURI(opts.vimeo)
		+ "&preload=" + encodeURI(opts.preload)
		+ "&autoplay=" + encodeURI(opts.autoplay)
		+ "&loop=" + encodeURI(opts.loop);
}

function sh5vp_videoModal(opts, field_ids) {
	var elem = jQuery('#'+field_ids.modal);
	var titleElem = jQuery('#sh5vp-modal-title');
	var selVideoElem = jQuery('#'+field_ids.modal+' .sh5vp-preview-selected-video');
	var selYoutubeElem = jQuery('#'+field_ids.modal+' .sh5vp-preview-selected-youtube');
	var selVimeoElem = jQuery('#'+field_ids.modal+' .sh5vp-preview-selected-vimeo');
	
	if (opts.video) {
		selVideoElem.text(opts.video);
		selVideoElem.parents('tr').show();
	}
	else {
		selVideoElem.parents('tr').hide();
	}
	if (opts.youtube) {
		selYoutubeElem.text(opts.youtube);
		selYoutubeElem.parents('tr').show();
	}
	else {
		selYoutubeElem.parents('tr').hide();
	}
	if (opts.vimeo) {
		selVimeoElem.text(opts.vimeo);
		selVimeoElem.parents('tr').show();
	}
	else {
		selVimeoElem.parents('tr').hide();
	}
	
	var iframeElem = jQuery('#'+field_ids.modal+' .sh5vp-modal-iframe');
	var innerElem = jQuery('#'+field_ids.modal+' .sh5vp-modal-inner');
	iframeElem.attr('src', sh5vp_previewIframeUrl(opts, field_ids));

	if (opts.width && opts.height) {
		iframeElem.attr('width', opts.width);
		iframeElem.attr('height', opts.height);
		innerElem.css({'width': opts.width});
	}
	innerElem.css('top', (jQuery(window).height() - innerElem.outerHeight())/2);
	elem.css('visibility','visible').animate({'opacity':1}, {
		duration:'fast',
		complete:function() {
			jQuery(this).addClass('open');
		}
	});
}

function sh5vp_previewVideoClose() {
	var elem = jQuery(this).parents('.sh5vp-video-modal');	
	elem.find('.sh5vp-modal-iframe').attr('src', '');
	elem.animate({'opacity':0}, {
		duration:'fast',
		complete:function() {
			jQuery(this).removeClass('open').css({'visibility':'hidden'});
		}
	});
}

jQuery(window).resize(function() {
	jQuery('.sh5vp_content_tab').each(function() {
		var curr_tab_content = jQuery(this);
		if (curr_tab_content.hasClass('active')) {
			jQuery('.sh5vp_content').height(curr_tab_content.height() + 22);
		}
	});
	var innerElem = jQuery('.sh5vp-modal-inner');
	innerElem.css('top', (jQuery(window).height() - innerElem.outerHeight())/2);
});

jQuery(document).ready(function(e) {
	jQuery('.sh5vp-modal-close').each(function() {
		jQuery(this).click(sh5vp_previewVideoClose);
	}); 
	jQuery('.sh5vp-modal-bg').each(function() {
		jQuery(this).click(sh5vp_previewVideoClose);
	});
});

