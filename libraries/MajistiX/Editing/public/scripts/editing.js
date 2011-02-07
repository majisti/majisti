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

           options: {
               messageDuration: 3000
           },

           /**
            * @desc Constructor
            * @param string key The editor's unique key
            * @param object The editor options
            * @param object The options
            */
           initialize: function(key, editorOptions, options) {
               this.key            = key;
               this.editorOptions  = editorOptions;

               this.initialContent = '';
               this.lastClass      = '';

               this.setOptions(options);
               this.init();
           },

           /**
            * @desc Inits the editor by binding its edit and save buttons.
            */
           init: function() {
               this.$container = $('#maj-editing-container-' + this.key);

               this.initDialogs();

               this.bindEdit();
               this.bindControls();
           },

            /**
             * @desc Inits the needed dialogs.
             */
           initDialogs: function() {
               /* cancel dialog */
               var self = this;
               this.$container.find('.dialog').dialog({
                   modal: true,
                   autoOpen: false,
                   buttons: {
                       //TODO: i18n
                       "Yes": function() {
                           self.closeEditor();
                           $(this).dialog("close");
                       },
                       "No": function() {
                           $(this).dialog("close");
                       }
                   }
               });
           },

           /**
            * @desc Binds the edit trigger.
            */
           bindEdit: function() {
               var self  = this;
               var $cont = this.$container;

               /* show editor's panel when text is hovered */
               $cont.find('.text-wrapper').hover(function() {
                   $cont.find('.text').addClass('editable');
                   $cont.find('.panel').show();
               }, function() {
                   $cont.find('.text').removeClass('editable');
                   $cont.find('.panel').hide();
               });

               /* activate the editor when clicking the edit button */
               $cont.find('.edit').click(function() {
                   var $editor = $cont.find('.editor');

                   self.activate($editor, self.editorOptions);
                   self.initialContent = self.getData();

                   var $text = $cont.find('.text');

                   /* 
                    * disables/enables the edit button according 
                    * if text is empty or not or modified
                    */
                   var dataValidator = function() {
                        var $save = self.$container.find('.save');
                        var data  = self.getData();

                        if( false === $save.attr('disabled') 
                            && ( 0 === data.length
                                 || data === self.initialContent) ) 
                        {
                            $save.attr('disabled', true);
                        }
                        else if( true === $save.attr('disabled') 
                            && 0 < data.length && data !== self.initialContent ) 
                        {
                            $save.attr('disabled', false);
                        }
                   };

                   self.bindTextChange([dataValidator]);

                   $text.addClass('being-edited');

                   $editor.show();

                   return false;
               });
           }.protect(),

           /**
            * @desc Binds the triggers
            */
           bindControls: function() {
               var self = this;

               /* save trigger */
               this.$container.find('.editor').submit(function() {

                   $(this).hide();
                   self.showLoading();

                   $.ajax({
                       url:        majisti.app.currentUrl,
                       data:       $(this).serialize(),
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
                   if( self.getData() === self.initialContent ) {
                       self.closeEditor();
                   } else {
                       /* must use id selector, class selector won't work */
                       $('#majistix-editing-cancel-dialog-' + self.key).dialog("open");
                   }
                   return false;
               });
           }.protect(),

           closeEditor: function() {
               this.$container.find('.editor').hide();

               this.setData(this.initialContent);

               /* text no longer being edited, replace last known state */
               this.$container.find('.text')
                   .html(this.initialContent)
                   .removeClass('being-edited');
           },

           /**
            * @desc Shows a message in the message container.
            *
            * @param string message The message
            * @param string css class(es) to use
            * @param useTimeout Removes message for options.messageDuration.
            */
           showMessage: function(message, cssClass, useTimeout) {
               useTimeout = undefined === useTimeout ? true : useTimeout;

               var $message = this.$container.find('.message');
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
            * @desc Binds a text change listener to the editor. The contract suggest
            * at least the following to be implemented: 
            * 
            * - a live preview that changes text when the user is typing.
            * 
            * The text chage implementation must call back the given callbacks.
            * 
            * @param array callbacks
            */
           bindTextChange: null
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
