/**
 * 
 * @author Steven Rosato
 */
$(function(){
	$("#keyword").autocomplete(Application.url + '/admin/therapists/search/', {
		minChars: 2,
		delay: 100,
		extraParams: {
			type : $('select option:selected').val()
		},
		parse: function(data) {
			return $.map(eval(data), function(row) {
				return {
					data: row,
				}
			});
		},
		formatItem: function(row, i, max, term) {
			return row.fName
				+ ' ' + row.name
				+ '<br /><span style="font-size:80%">' + row.email + '</span>';
		},
		highlight: function(value, term) {
			return value.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + term.replace(/([\^\$\(\)\[\]\{\}\*\.\+\?\|\\])/gi, "\\$1") + ")(?![^<>]*>)(?![^&;]+;)", "gi"), '<span style="color:red">$1</span>');
		},
	}).result( function(event, data, formatted) {
		if( data ) {
			window.location = data.url;
		}
	});
	
	$('select').change(function(){
		$('#keyword').setOptions({ extraParams: { type : $('select option:selected').val() } });
		$('#keyword').flushCache();
	});
});