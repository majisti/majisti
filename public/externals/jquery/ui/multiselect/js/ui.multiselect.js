/*
 * jQuery UI Multiselect
 *
 * Copyright (c) 2008 Michael Aufreiter (quasipartikel.at)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 * 
 * http://www.quasipartikel.at/multiselect/
 *
 * Modified by : Yanick Rochon (yanick.rochon@gmail.com)
 *
 * @version 1.7-1pre
 *
 * Depends:
 *    ui.core.js
 *    ui.sortable.js
 */

(function(JQuery) {

JQuery.widget("ui.multiselect", {
  _init: function() {
    
        // hide this.element
        this.element.hide();
        this.id = this.element.attr("id");
        this.container = JQuery('<div class="ui-multiselect ui-helper-clearfix"></div>').insertAfter(this.element);
        this.selectedList = JQuery('<ul class="selected"></ul>').bind('selectstart', function(){return false;}).appendTo(this.container);
        this.availableList = JQuery('<ul class="available"></ul>').bind('selectstart', function(){return false;}).appendTo(JQuery('<div class="available"></div>').appendTo(this.container));

        var that = this;

        // set dimensions
        this.container.width(this.element.width()+1);
        this.selectedList.width(this.element.width()*0.6);
        this.availableList.parent().width(this.element.width()*0.4);

        this.selectedList.height(this.element.height());
        
        if ( !this.options.animated ) {
            this.options.show = 'show';
            this.options.hide = 'hide';
        }
        
        if ( this.options.searchUrl ) {
            this.input = JQuery('<input/>').insertBefore(this.availableList).addClass('waiting').val(this.options.searchCaption);
            this.registerSearchEvents(this.input);
            
            this.inputOverlay = JQuery('<div class="ui-widget-overlay"></div>').appendTo(this.availableList.parent()).hide();
            
            this.availableList.height(this.element.height() - this.input.height());
        } else {
            this.availableList.height(this.element.height());
        }
        
        // init lists...
        this.populateLists(this.element.find('option'));
        
        if (this.options.sortable) {
            // make current selection sortable
            JQuery(this.selectedList).sortable({
              containment: 'parent',
              update: function(event, ui) {
                // apply the new sort order to the original selectbox
                that.selectedList.find('li').each(function() {
                  if (this.optionLink) JQuery(this.optionLink).remove().appendTo(that.element);
                });
              }
            });
        }
  },
    destroy: function() {
        this.element.show();
        this.container.remove();

        JQuery.widget.prototype.destroy.apply(this, arguments);
    },
  populateLists: function(options) {
    //this.selectedList.empty();
        
    this.availableList.empty();
        this.selectedList.children('*').each(function() { this.itemLink = null; });  // cleanup
        
    var that = this;
    var items = JQuery(options.map(function(i) {
      var item = that._getOptionNode(this).appendTo(that.availableList);
            that.applyItemState(item);        
            
            if (this.selected) {
                that._setSelected(item, true);
            } else {
                item.show();
            }
            
            return item[0];
    }));
        
        this.registerAddEvents(items.find('a.action'));
        this.registerHoverEvents(items);
  },
    _getOptionNode: function(option) {
        var node = JQuery('<li class="ui-state-default"> \
            <span class="ui-icon"/> \
            '+JQuery(option).text()+'\
            <a href="#" class="action"><span class="ui-corner-all ui-icon"/></a> \
            </li>').hide();
        node[0].optionLink = option;
        return node;
    },
    _setSelected: function(item, state) {
        try {
            item[0].optionLink.selected = state;
        } catch (e) {
            /* @HACK: ignore - IE6 complaints for norhing as the attribute was indeed properly set! (yr - 2009-04-28) */
        }

        if ( state ) {
            var selectedItem = this._getOptionNode(item[0].optionLink).appendTo(this.selectedList)[this.options.show](this.options.animated);
            selectedItem[0].itemLink = item[this.options.hide](this.options.animated);
            this.applyItemState(selectedItem);
            this.registerHoverEvents(selectedItem);
            this.registerRemoveEvents(selectedItem.find('a.action'));
        } else {
            if (item[0].itemLink) {
                item[0].itemLink[this.options.show](this.options.animated);
            } else {
                item[0].itemLink = this._getOptionNode(item[0].optionLink).appendTo(this.availableList)[this.options.show](this.options.animated);
                this.registerHoverEvents(item[0].itemLink);
                this.registerAddEvents(item[0].itemLink.find('a.action'));
            }
            this.applyItemState(item[0].itemLink);
            item[this.options.hide](this.options.animated, function() {JQuery(this).remove();});
        }
    },    
    applyItemState: function(item) {
        if (item[0].optionLink.selected) {
            item.removeClass('ui-priority-secondary');
            if (this.options.sortable)
                item.find('span:first').addClass('ui-icon-arrowthick-2-n-s').removeClass('ui-helper-hidden').addClass('ui-icon');
            else
                item.find('span:first').removeClass('ui-icon-arrowthick-2-n-s').addClass('ui-helper-hidden').removeClass('ui-icon');
            item.find('a.action span').addClass('ui-icon-minus').removeClass('ui-icon-plus');
        } else {
            item.addClass('ui-priority-secondary');
            item.find('span:first').removeClass('ui-icon-arrowthick-2-n-s').addClass('ui-helper-hidden').removeClass('ui-icon');
            item.find('a.action span').addClass('ui-icon-plus').removeClass('ui-icon-minus');
        }
    },
    registerSearchEvents: function(elements) {
        var that = this;
        var defaultValue = JQuery.trim(elements.val());
        var timer;
    
        elements.focus(function() {
            if (JQuery.trim(JQuery(this).val())==defaultValue) {
                JQuery(this).val('').removeClass('waiting');
            }
        });
        elements.blur(function() {
            if (JQuery.trim(JQuery(this).val())=='') {
                JQuery(this).addClass('waiting').val(defaultValue);
            }
        });
        elements.keydown(function(e) {
            switch (e.which) {
                case 16:   // shift
                case 17:   // control
                case 18:   // alt
                    break;

                case 13:   // enter
                    if (timer) clearTimeout(timer);
                    that.searchNow();
                    return false;

                //case 46:   // del
                //case 8:    // backspace
                default:
                    if (timer) clearTimeout(timer);
            }
        });
        elements.keypress(function(e) {
            switch (e.which) {
                case 13:   // enter
                    return false;
            }
        });
        elements.keyup(function(e) {
            //alert( e.which );
            switch (e.which) {
                case 16:   // shift
                case 17:   // control
                case 18:   // alt
                    break;
                    
                case 13:
                    break;
                
                //case 46:   // del
                //case 8:    // backspace
                    //if (JQuery.trim(JQuery(this).val()).length < 2) {
                    //    break;
                    //}
                default:
                    timer = setTimeout(function() { that.searchNow(); }, that.options.searchDelay);
            }
        });
    },
    searchNow: function() {
        var input = this.input.attr('disabled', true);
        var that = this;
        this.inputOverlay.show();
        JQuery.get(this.options.searchUrl,
            { q: escape(this.input.val()) },
            function(data){  // Ajax - complete
                var newOptions = [];
                data = data.split("\n");
                var len = data.length;
                
                for (var i=0; i<len; i++) {
                    if (JQuery.trim(data[i])!='') {
                        var d = data[i].split('=');
                        var opts = that.element.find('option[value="'+d[0]+'"]');
                        if (opts.size() == 0) {
                            // create new option element
                            newOptions.push( JQuery('<option/>').attr('value',d[0]).attr('label',d[1]).text(d[1]).appendTo(that.element)[0] );
                        } else {
                            opts.each(function(o) {
                                if (!this.selected) newOptions.push(this);
                            });
                        }
                    }
                }
                
                that.debug = true;
                that.populateLists(JQuery(newOptions));
                that.inputOverlay.hide();
                input.attr('disabled', false).blur().focus();
            }
        );
    },
    registerHoverEvents: function(elements) {
        // extract this
        elements.removeClass('ui-state-hover');
        
        elements.mouseover(function() {
            JQuery(this).addClass('ui-state-hover');
        });
        
        elements.mouseout(function() {
            JQuery(this).removeClass('ui-state-hover');
        });
    },
  registerAddEvents: function(elements) {
    var that = this;
    elements.click(function() {
            var item = that._setSelected(JQuery(this).parent(), true);
            return false;
    });

  },
  registerRemoveEvents: function(elements) {
    var that = this;
    elements.click(function() {
            that._setSelected(JQuery(this).parent(), false);
            return false;
    });
  }
});
        
JQuery.extend(JQuery.ui.multiselect, {
    getter: "value",
    defaults: {
        animated: 'fast',
        show: 'slideDown',
        hide: 'slideUp',
        searchUrl: null,
        searchCaption: 'Search...',
        searchDelay: 400,
        sortable: false
    }
});
    
})(jQuery);
