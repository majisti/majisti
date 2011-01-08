/**
 * @desc Editing extension. Provides simple ajax editing capabilities to
 * a defined editor.
 *
 * @author Steven Rosato
 */
$.extend(majisti.ext, {
   editing: {
       /**
        * @desc Abstract Editor class that provides basic binding
        * for the editor and its panel. This class MUST be implemented
        * with a concrete strategy.
        *
        * @author Steven Rosato
        */
       Editor : new Class({

           Implements: [Options],

           key: null,
           editorOptions: null,
           initialContent: null,
           $container: null,
           lastClass: '',
           options: {
               livePreview: true,
               messageDuration: 3000
           },

           /**
            * @desc Constructor
            * @param string key The editor's unique key
            * @param object The editor options
            * @param object The options
            */
           initialize: function(key, editorOptions, options) {
               this.key           = key;
               this.editorOptions = editorOptions;

               this.setOptions(options);
               this.init();
           },

           /**
            * @desc Inits the editor by binding its edit and save buttons.
            */
           init: function() {
               $trigger    = $('.maj-editing-container a[rel=' + this.key + ']');
               $editorForm = $('#maj_editing_editor_' + this.key);

               this.$container = $trigger.closest('.maj-editing-container');

               this.bindEdit($trigger, $editorForm);
               this.bindSave($editorForm);
           },

           /**
            * @desc Binds the edit trigger.
            */
           bindEdit: function($trigger, $editorForm) {
               var self = this;

               /* show editor's panel when text is hovered */
               self.$container.find('.text-wrapper').hover(function() {
                   self.$container.find('.panel').show();
               }, function() {
                   self.$container.find('.panel').hide();
               });

               /* activate the editor when clicking the edit button */
               $trigger.click(function() {

                   self.activate($editorForm, self.editorOptions);
                   self.initialContent = self.getData();

                   $text = $trigger.parent().prev();

                   /* live preview */
                   if( self.options.livePreview ) {
                       self.bindLivePreview($text);
                   }

                   $text.addClass('being-edited');

                   $editorForm.show();

                   return false;
               });
           }.protect(),

           /**
            * @desc Binds the save trigger
            */
           bindSave: function($editorForm) {
               var self = this;

               /* save trigger */
               $editorForm.submit(function() {
                   $(this).hide();
                   self.showLoading();

                   $.ajax({
                       url:        majisti.app.url,
                       data:       $editorForm.serialize(),
                       type:       'post',
                       dataType:   'json',
                       success: function(data) {
                           self.showMessage(data.message, data.result);

                           /* toggle text as being currently edited */
                           self.$container.find('.text')
                               .html(self.getData())
                               .toggleClass('being-edited');
                       }
                   });

                   return false;
               }).bind('reset', function() { /* cancel trigger */
                   $(this).hide();
                   self.setData(self.initialContent);

                   /* text no longer being edited, replace last known state */
                   self.$container.find('.text')
                       .html(self.initialContent)
                       .removeClass('being-edited');
               });
           }.protect(),

           /**
            * @desc Shows a message in the message container.
            *
            * @param string message The message
            * @param string css class(es) to use
            * @param useTimeout Removes message for options.messageDuration.
            */
           showMessage: function(message, cssClass, useTimeout) {
               useTimeout = useTimeout || true;

               $message = this.$container.find('.message');
               $message
                   .removeClass(this.lastClass)
                   .addClass(cssClass)
                   .html(message)
                   .show();

                   this.lastClass = cssClass;

                   if( useTimeout ) {
                       setTimeout(function() {
                           $message.fadeOut();
                       }, this.options.messageDuration);
                   }
           },

           /**
            * @desc Shows the loading bar.
            */
           showLoading: function() {
               this.showMessage('', 'loading', false);
           },

           /**
            * @abstract
            * @desc Activates the editor, making it available and
            * ready yo use.
            */
           activate: null,

           /**
            * @abstract
            * @desc Returns the data contained within the editor
            * @return string Data the data.
            */
           getData: null,

           /**
            * @abstract
            * @desc Sets the data within the editor
            * @param string data The data
            */
           setData: null,

           /**
            * @abstract
            * @desc Bind a live preview to the text while content is
            * added to the editor. If live preview option is disabled,
            * this will never be called.
            *
            * @param jquery $text The text object
            */
           bindLivePreview: null
       }),

       /**
        *  @desc Creates an editor instance with the key,
        *  editor options, and options provided.
        *
        *  @param string key The editor's unique key
        *  @param object The editor options
        *  @param object The options
        */
       createEditor: function(key, editorOptions, options) {
           return new this.Editor(key, editorOptions, options);
       }
   }
});
