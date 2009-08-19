/**
 * Taken from Kent Fredric at
 * http://stackoverflow.com/questions/316278/timeout-jquery-effects
 * 
 * Added a minor fix.
 * 
 * @author Steven Rosato
 */
;(function($) {
  $.fn.idle = function(time)
  { 
      var o = $(this); 
      return o.queue(function()
      { 
         setTimeout(function()
         { 
            o.dequeue(); 
         }, time);
      });
  };
})(jQuery);
