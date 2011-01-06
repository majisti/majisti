majisti.ext.editing.Editor.implement({

    //private methods

    getEditor: function() {
        return CKEDITOR.instances[this.key];
    }.protect(),

    //implemented methods

    bindLivePreview: function($text) {
        editor = this.getEditor();

        intervalId = null;
        editor.on('focus', function() {
            intervalId = setInterval(function() {
                $text.html(editor.getData());
            }, 100);
        });
        editor.on('blur', function() {
            clearInterval(intervalId);
        });
    },

    activate: function($form, options) {
        $form.find('textarea').ckeditor(options);
    },

    getData: function() {
       return this.getEditor().getData();
    },

    setData: function(data) {
       this.getEditor().setData(data);
    }
});
