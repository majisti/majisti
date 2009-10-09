$(function(){
	$('.scrollTo').click(function(){
		$.scrollTo('#' + $(this).attr('rel'), 500);	
	});
});
