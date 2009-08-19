/*
 * jQuery UI ImageRegion
 *
 * Copyright (c) 2009 Yanick Rochon (yanick.rochon@gmail.com)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 * 
 * @version 0.1
 *
 * Depends:
 *    ui.core.js
 * ui.draggable.js
 * ui.selectable.js
 */

(function(JQuery) {

JQuery.widget("ui.imageregion", {
    _init: function() {
   
        var self = this;
        container = this.element;
        draggableWrapper = $('<div class="regionWrapper"></div>').appendTo(container);
        
        this.element.addClass('ui-imageregion')
            .selectable({
                filter:'.region-selectable', 
                distance:4,
                stop: function(e,ui) {
                    enableRegions(true,$(e.target).find('.ui-selected'));
                }
            })
            .mousedown(function(e) { 
                if (!e.ctrlKey && !e.shiftKey) 
                    enableRegions(false,$(this).find('.region')); 
            })
        ;

        $.each(this.options.regions, function(id,region) {
            addRegion(id,JQuery.extend({},self.options.regionDefaults, region));
        });

    }
});

var container;
var regionCount = 0;
// create a region from the region object, add it to the container, then return it
function addRegion(id, region) {
    regionCount++;
    var element = $('<div class="region"></div>')
        .attr('title', id)
        .appendTo(container)
        .css({top:region.top, left:region.left, width:region.width, height:region.height})
        .html(region.content)
        .mousedown(function(e) { 
            if ($(this).hasClass('ui-selected')) {
                if (e.ctrlKey) {
                    enableRegions(false,$(this));
                    e.stopImmediatePropagation();
                }
            } else {
                if (!e.ctrlKey) {
                    enableRegions(false,$(container).find('.ui-selected').not(this));
                }
                if ($(this).hasClass('region-selectable')) enableRegions(true,$(this));
            }
            e.stopPropagation();
        })
        .mouseup(function(e) {
            if(!e.ctrlKey && !this.dragged) {
                enableRegions(false,$(container).find('.ui-selected').not(this));
            }
            e.target.dragged = false;
        })
    ;
  
    if (region.selectable) {
        element.addClass('region-selectable');
    }
    if (region.draggable) {
        element.draggable({
            containment:'parent',
            drag: function(e,ui) {
                e.target.dragged = true;
            }
        });
    }
    if (region.resizable) {
        element.resizable({
            containment:'parent',
            delay: 20,
            minHeight:region.minHeight,
            minWidth:region.minWidth,
            stop: function(e,ui) {
                e.target.dragged = true;
                prepareDraggables();
            }
        })
        ;
    }
  
    return enableRegions(false,element);
};

function enableRegions(state,regions) {
    regions[(state?'add':'remove')+'Class']('ui-selected')
        .draggable(state?'enable':'disable')
        .resizable(state?'enable':'disable')
        .css('z-index', state ? regionCount + 10 : 0);
    ;
    prepareDraggables();
};

var draggableWrapper;
function prepareDraggables() {
    var selected = container.find('.ui-selected.ui-draggable');
  
    if (selected.size()>1) {
        // get boundaries...
        var offset = selected.offset();
        var box = [offset.left,offset.top,offset.left+selected.width(),offset.top+selected.height()];
          
        selected.each(function(i,el) {
            el = $(el);
            var offset = el.offset();
            if (offset.left<box[0]) {
                box[0] = offset.left;
            } else {
                box[2] = offset.left+el.width();
            }
            if (offset.top<box[1]) {
                box[1] = offset.top;
            } else {
                box[3] = offset.top+el.height();
            }
        });

        $('#status').text("box = " + box);

        offset = container.offset();
        var parentBox = [offset.top,offset.left,offset.top+container.width()];
        
    } else {
        $('#status').text("box reset");
    
        selected.draggable('option','containment', 'parent');
    }
  
};


/**
 * Options:
 *
 *   regions     {regionName: {top:int, left:int, width:int, height:int, locked:boolean}, ...} 
 */
JQuery.extend(JQuery.ui.imageregion, {
    //getter: "value",
    defaults: {
        regionDefaults: {
            content: '',
            top: 0,
            left: 0,
            width: 32,
            height: 32,
            minWidth: 32, 
            minHeight: 32,
            resizable: true,
            draggable: true,
            selectable: true
        },
        regions: {}
    }
});
    
})(jQuery);
