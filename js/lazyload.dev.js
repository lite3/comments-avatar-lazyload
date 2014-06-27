/**
 * jQuery LazyLoad
 * @version		2.4.3
 * @author		Lite3 <lite3@qq.com>
 * @link		http://www.litefeel.com/comments-avatar-lazyload/
 * @requires	jQuery
 */
jQuery(function ($){
	var commentlist = $('#comments');
	if(commentlist.length === 0) {
		commentlist = $('#commentlist');
	}
	if(commentlist.length === 0) {
		commentlist = $('.commentlist');
	}
	var commentlistParent = commentlist.parent();
	var elements = $('img.comments-avatar-lazyload');
	var checkShow = function() {
		var fold = $(window).height() + $(window).scrollTop();
		elements.each(function() {
			if(fold > $(this).offset().top) {
				$(this).trigger('appear');
				elements = elements.not(this);
			}
		});
		if(elements.length === 0) {
			$(window).unbind('scroll',checkShow);
			$(window).unbind('resize', checkShow);
			commentlistParent.unbind('click', checkShow);
		}
	};
	elements.each(function() {
			$(this).one('appear',function(){$(this).attr('src',$(this).attr('alt'));});
	});
	if(elements.length === 0) {
		return;
	}
	$(window).bind('scroll', checkShow);
	$(commentlist).bind('resize', checkShow);
	commentlistParent.bind('click', checkShow);
	checkShow();
});