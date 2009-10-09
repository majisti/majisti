$(function(){
	/* table sorting and pagination */
	$('table').attr('id', 'table-test')
	$('table').addClass('tablesorter').tablesorter( {cssHeader: 'tablesorter-header'} );
	$('table').tablesorterPager({container: $("#pager")}); 
	$(this.form).submit(function(){ return false; });
	
	therapistId = 1;
	$link = null;

	/* popup dialog window when clicking the 'delete' link */
	$('.delete').click(function(){
		therapistId = $(this).attr('rel');
		$link = $(this);
		$('#dialog').dialog('open');
	});
	
	/* dialog's config */
	$('#dialog').dialog({
		'autoOpen': false,
		'resizable': false,
		'draggable': false,
		'modal': true,
		'buttons': {
			'non' : function() {
				$(this).dialog('close');
			},
			'oui' : function() {
				$(this).dialog('close');
				$.post(Application.url + '/admin/therapists/delete/id/' + therapistId);
				
				$tr = $link.parent().parent();
				$tr.css('background-color', '#e5e800').fadeOut('slow');
			}
		}
	});
});
