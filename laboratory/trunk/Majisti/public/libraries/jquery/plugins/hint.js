/**
* @author Remy Sharp
* @url http://remysharp.com/2007/01/25/jquery-tutorial-text-box-hints/
* 
* added blockSubmit
* @author Steven Rosato
*/

jQuery.fn.hint = function (blurClass, blockSubmit) {
  if (!blurClass) { 
    blurClass = 'blur';
  }
  if (!blockSubmit) {
      blockSubmit = false;
  }

  return this.each(function () {
    // get jQuery version of 'this'
    var $input = jQuery(this),

    // capture the rest of the variable to allow for reuse
      title = $input.attr('title'),
      $form = jQuery(this.form),
      $win = jQuery(window);

    function remove() {
      if ($input.val() === title && $input.hasClass(blurClass)) {
        $input.val('').removeClass(blurClass);
      }
    }
    
    // only apply logic if the element has the attribute
    if (title) { 
      // on blur, set value to title attr if text is blank
      $input.blur(function () {
        if (this.value === '') {
          $input.val(title).addClass(blurClass);
          jQuery('input[type=submit]', $form).attr('disabled', 'disabled');
        }
      }).focus(function(){
          remove();
        jQuery('input[type=submit]', $form).removeAttr('disabled');
      }).blur(); // now change all inputs to title

      //block submit if the input value is the title's value
        $form.submit(function(event) {
          if( $input.val() === title && blockSubmit ) {
            event.preventDefault();
          } else { // clear the pre-defined text when form is submitted
            $form.submit(remove);
        }
      });
      $win.unload(remove); // handles Firefox's autocomplete
    }
  });
};