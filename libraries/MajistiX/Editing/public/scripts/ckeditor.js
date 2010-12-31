majisti.ext.editing.Editor = new Class({
    Extends: majisti.ext.editing.Editor,

    getData: function(key) {
       return CKEDITOR.instances[key].getData();
    }
});
