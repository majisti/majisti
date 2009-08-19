/**
 * Small plugin to identify an element. If the element has no id, a
 * new one will be assigned and returned.
 *
 * Usage:
 *
 *  $('.div').identify();      // make sure all elements have ids
 *  $('.div').identify('foo'); // make sure all elements have ids, if not, create a new one with prefix 'foo'
 */

jQuery.fn.identify = function(prefix) {
    var i = 0;
    return this.each(function() {
        if($(this).attr('id')) return;
        var preId = prefix ? prefix : this.tagName.toLowerCase() + 'element';
        do { 
            var id = preId + '_' + (++i);
        } while($('#' + id).length > 0);            
        $(this).attr('id', id);
    });
};
