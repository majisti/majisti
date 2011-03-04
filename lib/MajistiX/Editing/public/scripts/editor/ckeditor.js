/**
 * @desc CkEditor concrete implementation.
 *
 * @author Steven Rosato
 */
majisti.ext.editing.Editor.implement({

    //private methods

    /**
     * @desc Returns the current ckEditor instance
     * @return CkEditor The editor
     */
    getEditor: function() {
        return CKEDITOR.instances[this.key];
    }.protect(),

    //implemented methods

    /*
     * (non js-doc)
     * @see Inherited implementation
     */
    bindTextChange: function(callbacks) {
        var editor = this.getEditor();
        var self   = this;
        var $text  = self.$container.find('.text');

        /*
         * bind live preview as a greedy interval watcher since
         * CkEditor does not provide any event for data change.
         * This greedy listener stops when not focussing on the ckeditor
         * instance, at least to save up some resource.
         */

        var intervalId = null;
        editor.on('focus', function() {
            intervalId = setInterval(function() {
                $text.html(editor.getData());

                for( var i = 0; i < callbacks.length; i++ ) {
                    callbacks[i]();
                }
            }, 100);
        });
        editor.on('blur', function() {
            clearInterval(intervalId);
        });
    },

    /*
     * (non js-doc)
     * @see Inherited implementation
     */
    activate: function($form, options) {
        $form.find('textarea').ckeditor(options);
    },

    /*
     * (non js-doc)
     * @see Inherited implementation
     */
    getData: function() {
       return this.getEditor().getData();
    },

    /*
     * (non js-doc)
     * @see Inherited implementation
     */
    setData: function(data) {
       this.getEditor().setData(data);
    }
});
