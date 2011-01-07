$.extend(majisti.ext, {
   editing: {
       Editor : new Class({
           Implements: [Options],
           key: null,
           editorOptions: null,
           initialContent: null,
           $container: null,
           lastResult: '',
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

               this.$container = $trigger.closest('.maj-editing-container');

               this.bindEdit($trigger, $editorForm);
               this.bindSave($trigger, $editorForm);
           },

           bindEdit: function($trigger, $editorForm) {
               var self = this;

               self.$container.find('.text-wrapper').hover(function() {
                   self.$container.find('.panel').show();
               }, function() {
                   self.$container.find('.panel').hide();
               });

               $trigger.click(function() {

                   self.activate($editorForm, self.editorOptions);
                   self.initialContent = self.getData();

                   if( self.options.livePreview ) {
                       self.bindLivePreview(self.$container.find('.text'));
                   }

                   self.$container.find('.text').addClass('being-edited');

                   $editorForm.show();

                   return false;
               });
           }.protect(),

           bindSave: function($trigger, $editorForm) {
               var self = this;
               $editorForm.submit(function() {
                   $(this).hide();

                   $message = self.$container.find('.message');
                   $message
                       .html('')
                       .addClass('loading')
                       .show();

                   $.ajax({
                       url:        majisti.app.url,
                       data:       $editorForm.serialize(),
                       type:       'post',
                       dataType:   'json',
                       success: function(data) {
                           $message
                               .removeClass('loading')
                               .removeClass(self.lastResult)
                               .addClass(data.result)
                               .html(data.message);

                           self.lastResult = data.result;

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
                       .removeClass('being-edited');
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
