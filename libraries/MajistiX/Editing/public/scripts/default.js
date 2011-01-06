$.extend(majisti.ext, {
   editing: {
       Editor : new Class({
           Implements: [Options],
           key: null,
           editorOptions: null,
           initialContent: null,
           $container: null,
           options: {
               livePreview: true
           },

           initialize: function(key, editorOptions, options) {
               this.key           = key;
               this.editorOptions = editorOptions;

               this.setOptions(options);
               this.init();
           },

           init: function() {
               $trigger    = $('.maj-editing-container a[rel=' + this.key + ']');
               $editorForm = $('#maj_editing_editor_' + this.key);

               this.$container = $trigger.parent().parent();

               this.bindEdit($trigger, $editorForm);
               this.bindSave($trigger, $editorForm);
           },

           bindEdit: function($trigger, $editorForm) {
               var self = this;
               $trigger.click(function() {
                   self.activate($editorForm, self.editorOptions);
                   self.initialContent = self.getData();

                   if( self.options.livePreview ) {
                       self.bindLivePreview($editorForm.prev());
                   }

                   self.$container.find('.text').toggleClass('being-edited');

                   $editorForm.show();

                   return false;
               });
           }.protect(),

           bindSave: function($trigger, $editorForm) {
               var self = this;
               $editorForm.submit(function() {
                   $(this).hide();

                   $.ajax({
                       url:        majisti.app.url,
                       data:       $editorForm.serialize(),
                       type:       'post',
                       dataType:   'json',
                       success: function(data) {
                           $message = self.$container.find('.message');
                           $message.html(data.message);

                           setTimeout(function() {
                               $message.fadeOut();
                           }, 3000);

                           self.$container.find('.text')
                               .html(self.getData())
                               .toggleClass('being-edited');
                       }
                   });

                   return false;
               }).bind('reset', function() {
                   $(this).hide();
                   self.setData(self.initialContent);
                   self.$container.find('.text')
                       .html(self.initialContent)
                       .toggleClass('being-edited');
               });
           }.protect(),

           /**
            * @desc
            * @abstract
            */
           activate: null,

           /**
            * @desc
            * @abstract
            * @return string Data the data.
            */
           getData: null,

           /**
            * @desc
            * @abstract
            * @param string data The data
            */
           setData: null,

           /**
            * @desc
            * @abstract
            * @param jquery $text The text object
            */
           bindLivePreview: null
       }),

       createEditor: function(key, editorOptions, options) {
           return new this.Editor(key, editorOptions, options);
       }
   }
});
