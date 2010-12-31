$.extend(majisti.ext, {
   editing: {
       Editor : new Class({
           key: null,
           initialize: function(key) {
               this.key = key;
           },

           getKey: function() {
               return this.key;
           },

           activate: function(editorCallback) {
               $editForm   = $('#majistix_editing_form_edit_' + this.key);
               $editorForm = $('#majistix_editing_form_' + this.key);

               this.bindEdit($editForm, $editorForm, editorCallback);
               this.bindSave($editForm, $editorForm);
           },

           bindEdit: function($editForm, $editorForm, editorCallback) {
               $editForm.submit(function() {
                   $(this).hide();
                   $editorForm.show();
                   if( editorCallback ) {
                       editorCallback($editorForm.find('textarea'));
                   }
                   return false;
               });
           }.protect(),

           bindSave: function($editForm, $editorForm) {
               self = this;
               var key = this.key;
               $editorForm.submit(function() {
                   $(this).hide();

                   $.ajax({
                       url:        majisti.app.url,
                       data:       $editorForm.serialize(),
                       type:       'post',
                       dataType:   'json',
                       success: function(data) {
                           console.log('success');
                       }
                   });

                   $('#majistix-editing-content-text-' + key).html(
                       self.getData(key)
                   );

                   $editForm.show();
                   return false;
               });
           }.protect(),

           /**
            * @abstract
            * @param string key The key
            */
           getData: null
       }),

       editor: function(key) { //TODO: protect accidental double instanciation
           return new this.Editor(key);
       }
   }
});
