/**
 * tools.tabs 1.0.4 - Tabs done right.
 * 
 * Copyright (c) 2009 Tero Piirainen
 * http://flowplayer.org/tools/tabs.html
 *
 * Dual licensed under MIT and GPL 2+ licenses
 * http://www.opensource.org/licenses
 *
 * Launch  : November 2008
 * Date: ${date}
 * Revision: ${revision} 
 */  
(function($) {
        
    // static constructs
    $.tools = $.tools || {};
    
    $.tools.tabs = {
        version: '1.0.4',
        
        conf: {
            tabs: 'a',
            current: 'current',
            onBeforeClick: null,
            onClick: null, 
            effect: 'default',
            initialIndex: 0,            
            event: 'click',
            api:false,
            rotate: false
        },
        
        addEffect: function(name, fn) {
            effects[name] = fn;
        }
    };      
    
    
    var effects = {
        
        // simple "toggle" effect
        'default': function(i, done) { 
            this.getPanes().hide().eq(i).show();
            done.call();
        }, 
        
        /*
            configuration:
                - fadeOutSpeed (positive value does "crossfading")
                - fadeInSpeed
        */
        fade: function(i, done) {
            var conf = this.getConf(), 
                 speed = conf.fadeOutSpeed,
                 panes = this.getPanes();
            
            if (speed) {
                panes.fadeOut(speed);   
            } else {
                panes.hide();   
            }

            panes.eq(i).fadeIn(conf.fadeInSpeed, done); 
        },
        
        // for basic accordions
        slide: function(i, done) {          
            this.getPanes().slideUp(200);
            this.getPanes().eq(i).slideDown(400, done);          
        }, 

        // simple AJAX effect
        ajax: function(i, done)  {          
            this.getPanes().eq(0).load(this.getTabs().eq(i).attr("href"), done);    
        }
        
    };      
    
    var w;
    
    // this is how you add effects
    $.tools.tabs.addEffect("horizontal", function(i, done) {
    
        // store original width of a pane into memory
        if (!w) { w = this.getPanes().eq(0).width(); }
        
        // set current pane's width to zero
        this.getCurrentPane().animate({width: 0}, function() { $(this).hide(); });
        
        // grow opened pane to it's original width
        this.getPanes().eq(i).animate({width: w}, function() { 
            $(this).show();
            done.call();
        });
        
    }); 
     

    function Tabs(tabs, panes, conf) { 
        
        var self = this, $self = $(this), current;

        // bind all callbacks from configuration
        $.each(conf, function(name, fn) {
            if ($.isFunction(fn)) { $self.bind(name, fn); }
        });
        
        
        // public methods
        $.extend(this, {                
            click: function(i, e) {
                
                var pane = self.getCurrentPane();               
                var tab = tabs.eq(i);                                                
                
                if (typeof i == 'string' && i.replace("#", "")) {
                    tab = tabs.filter("[href*=" + i.replace("#", "") + "]");
                    i = Math.max(tabs.index(tab), 0);
                }
                                
                if (conf.rotate) {
                    var last = tabs.length -1; 
                    if (i < 0) { return self.click(last, e); }
                    if (i > last) { return self.click(0, e); }                      
                }
                
                if (!tab.length) { 
                    if (current >= 0) { return self; }
                    i = conf.initialIndex;
                    tab = tabs.eq(i);
                }               
                
                // current tab is being clicked
                if (i === current) { return self; }
                
                // possibility to cancel click action               
                e = e || $.Event();
                e.type = "onBeforeClick";
                $self.trigger(e, [i]);              
                if (e.isDefaultPrevented()) { return; }
                
                // call the effect
                effects[conf.effect].call(self, i, function() {

                    // onClick callback
                    e.type = "onClick";
                    $self.trigger(e, [i]);                  
                });         
                
                // onStart
                e.type = "onStart";
                $self.trigger(e, [i]);              
                if (e.isDefaultPrevented()) { return; } 
                
                // default behaviour
                current = i;
                tabs.removeClass(conf.current); 
                tab.addClass(conf.current);             
                
                return self;
            },
            
            getConf: function() {
                return conf;    
            },

            getTabs: function() {
                return tabs;    
            },
            
            getPanes: function() {
                return panes;   
            },
            
            getCurrentPane: function() {
                return panes.eq(current);   
            },
            
            getCurrentTab: function() {
                return tabs.eq(current);    
            },
            
            getIndex: function() {
                return current; 
            }, 
            
            next: function() {
                return self.click(current + 1);
            },
            
            prev: function() {
                return self.click(current - 1); 
            }, 
            
            bind: function(name, fn) {
                $self.bind(name, fn);
                return self;    
            },  
            
            onBeforeClick: function(fn) {
                return this.bind("onBeforeClick", fn);
            },
            
            onClick: function(fn) {
                return this.bind("onClick", fn);
            },
            
            unbind: function(name) {
                $self.unbind(name);
                return self;    
            }           
        
        });
        
        
        // setup click actions for each tab
        tabs.each(function(i) { 
            $(this).bind(conf.event, function(e) {
                self.click(i, e);
                return false;
            });         
        });

        // if no pane is visible --> click on the first tab
        if (location.hash) {
            self.click(location.hash);
        } else {
            if (conf.initialIndex === 0 || conf.initialIndex > 0) {
                self.click(conf.initialIndex);
            }
        }       
        
        // cross tab anchor link
        panes.find("a[href^=#]").click(function(e) {
            self.click($(this).attr("href"), e);        
        }); 
    }
    
    
    // jQuery plugin implementation
    $.fn.tabs = function(query, conf) {
        
        // return existing instance
        var el = this.eq(typeof conf == 'number' ? conf : 0).data("tabs");
        if (el) { return el; }

        if ($.isFunction(conf)) {
            conf = {onBeforeClick: conf};
        }
        
        // setup options
        var globals = $.extend({}, $.tools.tabs.conf), len = this.length;
        conf = $.extend(globals, conf);     

        
        // install tabs for each items in jQuery        
        this.each(function(i) {             
            var root = $(this); 
            
            // find tabs
            var els = root.find(conf.tabs);
            
            if (!els.length) {
                els = root.children();  
            }
            
            // find panes
            var panes = query.jquery ? query : root.children(query);
            
            if (!panes.length) {
                panes = len == 1 ? $(query) : root.parent().find(query);
            }           
            
            el = new Tabs(els, panes, conf);
            root.data("tabs", el);
            
        });     
        
        return conf.api ? el: this;     
    };      
        
}) (jQuery); 

/**
 * jQuery TOOLS plugin :: tabs.slideshow 1.0.2
 * 
 * Copyright (c) 2009 Tero Piirainen
 * http://flowplayer.org/tools/tabs.html#slideshow
 *
 * Dual licensed under MIT and GPL 2+ licenses
 * http://www.opensource.org/licenses
 *
 * Launch  : September 2009
 * Date: ${date}
 * Revision: ${revision} 
 */
(function($) {
    
    var t = $.tools.tabs; 
    t.plugins = t.plugins || {}; 
    t.plugins.slideshow = { 
        version: '1.0.2',
        
        // CALLBACKS: onBeforePlay, onPlay, onBeforePause, onPause,  
        conf: {
            next: '.forward',
            prev: '.backward',
            disabledClass: 'disabled',
            autoplay: false,
            autopause: true,
            interval: 3000, 
            clickable: true,
            api: false
        }
    };


    // jQuery plugin implementation
    $.prototype.slideshow = function(conf) {
    
        var globals = $.extend({}, t.plugins.slideshow.conf),
             len = this.length, 
             ret;
             
        conf = $.extend(globals, conf);  
        
        this.each(function() {
            
            var tabs = $(this), api = tabs.tabs(), $api = $(api), ret = api; 
            
            // bind all callbacks from configuration
            $.each(conf, function(name, fn) {
                if ($.isFunction(fn)) { api.bind(name, fn); }
            });
        
            
            function find(query) {
                return len == 1 ? $(query) : tabs.parent().find(query); 
            }   
            
            var nextButton = find(conf.next).click(function() {
                api.next();     
            });
            
            var prevButton = find(conf.prev).click(function() {
                api.prev();     
            });
            
            // interval stuff
            var timer, hoverTimer, startTimer, stopped = false;
    

            // extend the Tabs API with slideshow methods           
            $.extend(api, {
                    
                play: function() {
        
                    // do not start additional timer if already exists
                    if (timer) { return; }
                    
                    // onBeforePlay
                    var e = $.Event("onBeforePlay");
                    $api.trigger(e);
                    
                    if (e.isDefaultPrevented()) { return api; }             
                    
                    stopped = false;
                    
                    // construct new timer
                    timer = setInterval(api.next, conf.interval);
    
                    // onPlay
                    $api.trigger("onPlay");             
                    
                    api.next();
                },
            
                pause: function() {
                    
                    if (!timer) { return api; }
                    
                    // onBeforePause
                    var e = $.Event("onBeforePause");
                    $api.trigger(e);                    
                    if (e.isDefaultPrevented()) { return api; }     
                    
                    timer = clearInterval(timer);
                    startTimer = clearInterval(startTimer);
                    
                    // onPause
                    $api.trigger("onPause");        
                },
                
                // when stopped - mouseover won't restart 
                stop: function() {                  
                    api.pause();
                    stopped = true; 
                },
                
                onBeforePlay: function(fn) {
                    return api.bind("onBeforePlay", fn);
                },
                
                onPlay: function(fn) {
                    return api.bind("onPlay", fn);
                },

                onBeforePause: function(fn) {
                    return api.bind("onBeforePause", fn);
                },
                
                onPause: function(fn) {
                    return api.bind("onPause", fn);
                }
                
            });
    
            
        
            /* when mouse enters, slideshow stops */
            if (conf.autopause) {
                var els = api.getTabs().add(nextButton).add(prevButton).add(api.getPanes());
                
                els.hover(function() {                  
                    api.pause();                    
                    hoverTimer = clearInterval(hoverTimer);
                    
                }, function() {
                    if (!stopped) {                     
                        hoverTimer = setTimeout(api.play, conf.interval);                       
                    }
                });
            } 
            
            if (conf.autoplay) {
                startTimer = setTimeout(api.play, conf.interval);               
            } else {
                api.stop(); 
            }
            
            if (conf.clickable) {
                api.getPanes().click(function()  {
                    api.next();
                });
            } 
            
            // manage disabling of next/prev buttons
            if (!api.getConf().rotate) {
                
                var cls = conf.disabledClass;
                
                if (!api.getIndex()) {
                    prevButton.addClass(cls);
                }
                api.onBeforeClick(function(e, i)  {
                    if (!i) {
                        prevButton.addClass(cls);
                    } else {
                        prevButton.removeClass(cls);    
                    
                        if (i == api.getTabs().length -1) {
                            nextButton.addClass(cls);
                        } else {
                            nextButton.removeClass(cls);    
                        }
                    }
                });
            }
            
        });
        
        return conf.api ? ret : this;
    };
    
})(jQuery); 

/**
 * jQuery TOOLS plugin :: tabs.history 1.0.2
 * 
 * Copyright (c) 2009 Tero Piirainen
 * http://flowplayer.org/tools/tabs.html#history
 *
 * Dual licensed under MIT and GPL 2+ licenses
 * http://www.opensource.org/licenses
 *
 * Launch  : September 2009
 * Date: ${date}
 * Revision: ${revision} 
 */
(function($) {
    
    var t = $.tools.tabs; 
    t.plugins = t.plugins || {};
    
    t.plugins.history = { 
        version: '1.0.2',       
        conf: {
            api: false
        }       
    };
        
    var hash, iframe;       

    function setIframe(h) {
        if (h) {
            var doc = iframe.contentWindow.document;
            doc.open().close(); 
            doc.location.hash = h;
        }
    }
    
    // jQuery plugin implementation
    $.fn.onHash = function(fn) {
            
        var el = this;
        
        // IE
        if ($.browser.msie && $.browser.version < '8') {
            
            // create iframe that is constantly checked for hash changes
            if (!iframe) {
                iframe = $("<iframe/>").attr("src", "javascript:false;").hide().get(0);
                $("body").append(iframe);
                                
                setInterval(function() {
                    var idoc = iframe.contentWindow.document, 
                         h = idoc.location.hash;
                
                    if (hash !== h) {                       
                        $.event.trigger("hash", h);
                        hash = h;
                    }
                }, 100);
                
                setIframe(location.hash || '#');
            }
            
            // when link is clicked the iframe hash updated
            el.bind("click.hash", function(e) {
                setIframe($(this).attr("href"));
            }); 

            
        // other browsers scans for location.hash changes directly withou iframe hack
        } else { 
            setInterval(function() {
                var h = location.hash;
                var els = el.filter("[href$=" + h + "]");
                
                if (!els.length) { 
                    h = h.replace("#", "");
                    els = el.filter("[href$=" + h + "]");
                }
                
                if (els.length && h !== hash) {
                    hash = h;
                    $.event.trigger("hash", h);
                }                       
            }, 100);
        }
         
        // bind a history listener
        $(window).bind("hash", fn);
        
        // return jQuery
        return this;        
    };  
    

    $.fn.history = function(conf) {
    
        var globals = $.extend({}, t.plugins.history.conf), ret;
        conf = $.extend(globals, conf);
        
        this.each(function() {
            
            var api = $(this).tabs(), 
                 tabs = api.getTabs();
                 
            if (api) { ret = api; }
            
            // enable history support
            tabs.onHash(function(evt, hash) {
                if (!hash || hash == '#') { hash = api.getConf().initialIndex; }
                api.click(hash);        
            });   
            
            // tab clicks perform their original action
            tabs.click(function(e) {
                location.hash = $(this).attr("href").replace("#", "");  
            }); 

        });
        
        return conf.api ? ret : this;
        
    };
        
})(jQuery); 

/**
 * tools.tooltip 1.1.3 - Tooltips done right.
 * 
 * Copyright (c) 2009 Tero Piirainen
 * http://flowplayer.org/tools/tooltip.html
 *
 * Dual licensed under MIT and GPL 2+ licenses
 * http://www.opensource.org/licenses
 *
 * Launch  : November 2008
 * Date: ${date}
 * Revision: ${revision} 
 */
(function($) { 

    var instances = [];
    
    // static constructs
    $.tools = $.tools || {};
    
    $.tools.tooltip = {
        version: '1.1.3',
        
        conf: { 
            
            // default effect variables
            effect: 'toggle',           
            fadeOutSpeed: "fast",
            tip: null,
            
            predelay: 0,
            delay: 30,
            opacity: 1,         
            lazy: undefined,
            
            // 'top', 'bottom', 'right', 'left', 'center'
            position: ['top', 'center'], 
            offset: [0, 0],         
            cancelDefault: true,
            relative: false,
            oneInstance: true,
            
            
            // type to event mapping 
            events: {
                def:            "mouseover,mouseout",
                input:      "focus,blur",
                widget:     "focus mouseover,blur mouseout",
                tooltip:        "mouseover,mouseout"
            },          
            
            api: false
        },
        
        addEffect: function(name, loadFn, hideFn) {
            effects[name] = [loadFn, hideFn];   
        } 
    };
    
    
    var effects = { 
        toggle: [ 
            function(done) { 
                var conf = this.getConf(), tip = this.getTip(), o = conf.opacity;
                if (o < 1) { tip.css({opacity: o}); }
                tip.show();
                done.call();
            },
            
            function(done) { 
                this.getTip().hide();
                done.call();
            } 
        ],
        
        fade: [
            function(done) { this.getTip().fadeIn(this.getConf().fadeInSpeed, done); },  
            function(done) { this.getTip().fadeOut(this.getConf().fadeOutSpeed, done); } 
        ]       
    };   

    function Tooltip(trigger, conf) {

        var self = this, $self = $(this);
        
        trigger.data("tooltip", self);
        
        // find the tip
        var tip = trigger.next();
        
        if (conf.tip) {
            
            tip = $(conf.tip);
            
            // multiple tip elements
            if (tip.length > 1) {
                
                // find sibling
                tip = trigger.nextAll(conf.tip).eq(0);  
                
                // find sibling from the parent element
                if (!tip.length) {
                    tip = trigger.parent().nextAll(conf.tip).eq(0);
                }
            } 
        }               
        
        /* calculate tip position relative to the trigger */    
        function getPosition(e) {   
            
            // get origin top/left position 
            var top = conf.relative ? trigger.position().top : trigger.offset().top, 
                 left = conf.relative ? trigger.position().left : trigger.offset().left,
                 pos = conf.position[0];

            top  -= tip.outerHeight() - conf.offset[0];
            left += trigger.outerWidth() + conf.offset[1];
            
            // adjust Y     
            var height = tip.outerHeight() + trigger.outerHeight();
            if (pos == 'center')    { top += height / 2; }
            if (pos == 'bottom')    { top += height; }
            
            // adjust X
            pos = conf.position[1];     
            var width = tip.outerWidth() + trigger.outerWidth();
            if (pos == 'center')    { left -= width / 2; }
            if (pos == 'left')      { left -= width; }   
            
            return {top: top, left: left};
        }       

        
        // event management
        var isInput = trigger.is(":input"), 
             isWidget = isInput && trigger.is(":checkbox, :radio, select, :button"),            
             type = trigger.attr("type"),
             evt = conf.events[type] || conf.events[isInput ? (isWidget ? 'widget' : 'input') : 'def']; 
        
        evt = evt.split(/,\s*/); 
        if (evt.length != 2) { throw "Tooltip: bad events configuration for " + type; }
                
        trigger.bind(evt[0], function(e) {
            
            // close all instances
            if (conf.oneInstance) {
                $.each(instances, function()  {
                    this.hide();        
                });
            }
                
            // see if the tip was launched by this trigger
            var t = tip.data("trigger");            
            if (t && t[0] != this) { tip.hide().stop(true, true); }         
            
            e.target = this;
            self.show(e); 
            
            // tooltip close events
            evt = conf.events.tooltip.split(/,\s*/);
            tip.bind(evt[0], function() { self.show(e); });
            if (evt[1]) { tip.bind(evt[1], function() { self.hide(e); }); }
            
        });
        
        trigger.bind(evt[1], function(e) {
            self.hide(e); 
        });
        
        // ensure that the tip really shows up. IE cannot catch up with this.
        if (!$.browser.msie && !isInput && !conf.predelay) {
            trigger.mousemove(function()  {                 
                if (!self.isShown()) {
                    trigger.triggerHandler("mouseover");    
                }
            });
        }

        // avoid "black box" bug in IE with PNG background images
        if (conf.opacity < 1) {
            tip.css("opacity", conf.opacity);       
        }
        
        var pretimer = 0, title = trigger.attr("title");
        
        if (title && conf.cancelDefault) { 
            trigger.removeAttr("title");
            trigger.data("title", title);           
        }                       
        
        $.extend(self, {
                
            show: function(e) {
                
                if (e) { trigger = $(e.target); }               

                clearTimeout(tip.data("timer"));                    

                if (tip.is(":animated") || tip.is(":visible")) { return self; }
                
                function show() {
                    
                    // remember the trigger element for this tip
                    tip.data("trigger", trigger);
                    
                    // get position
                    var pos = getPosition(e);                   
                    
                    // title attribute                  
                    if (conf.tip && title) {
                        tip.html(trigger.data("title"));
                    }               
                    
                    // onBeforeShow
                    e = e || $.Event();
                    e.type = "onBeforeShow";
                    $self.trigger(e, [pos]);                
                    if (e.isDefaultPrevented()) { return self; }
            
                    
                    // onBeforeShow may have altered the configuration
                    pos = getPosition(e);
                    
                    // set position
                    tip.css({position:'absolute', top: pos.top, left: pos.left});                   
                    
                    // invoke effect
                    var eff = effects[conf.effect];
                    if (!eff) { throw "Nonexistent effect \"" + conf.effect + "\""; }
                    
                    eff[0].call(self, function() {
                        e.type = "onShow";
                        $self.trigger(e);           
                    });                 
                    
                }
                
                if (conf.predelay) {
                    clearTimeout(pretimer);
                    pretimer = setTimeout(show, conf.predelay); 
                    
                } else {
                    show(); 
                }
                
                return self;
            },
            
            hide: function(e) {

                clearTimeout(tip.data("timer"));
                clearTimeout(pretimer);
                
                if (!tip.is(":visible")) { return; }
                
                function hide() {
                    
                    // onBeforeHide
                    e = e || $.Event();
                    e.type = "onBeforeHide";
                    $self.trigger(e);               
                    if (e.isDefaultPrevented()) { return; }
                    
                    effects[conf.effect][1].call(self, function() {
                        e.type = "onHide";
                        $self.trigger(e);       
                    });
                }
                 
                if (conf.delay && e) {
                    tip.data("timer", setTimeout(hide, conf.delay));
                    
                } else {
                    hide(); 
                }           
                
                return self;
            },
            
            isShown: function() {
                return tip.is(":visible, :animated");   
            },
                
            getConf: function() {
                return conf;    
            },
                
            getTip: function() {
                return tip; 
            },
            
            getTrigger: function() {
                return trigger; 
            },
            
            // callback functions           
            bind: function(name, fn) {
                $self.bind(name, fn);
                return self;    
            },
            
            onHide: function(fn) {
                return this.bind("onHide", fn);
            },

            onBeforeShow: function(fn) {
                return this.bind("onBeforeShow", fn);
            },
            
            onShow: function(fn) {
                return this.bind("onShow", fn);
            },
            
            onBeforeHide: function(fn) {
                return this.bind("onBeforeHide", fn);
            },

            unbind: function(name) {
                $self.unbind(name);
                return self;    
            }           

        });     

        // bind all callbacks from configuration
        $.each(conf, function(name, fn) {
            if ($.isFunction(fn)) { self.bind(name, fn); }
        });         
        
    }
        
    
    // jQuery plugin implementation
    $.prototype.tooltip = function(conf) {
        
        // return existing instance
        var api = this.eq(typeof conf == 'number' ? conf : 0).data("tooltip");
        if (api) { return api; }
        
        // setup options
        var globals = $.extend(true, {}, $.tools.tooltip.conf);     
        
        if ($.isFunction(conf)) {
            conf = {onBeforeShow: conf};
            
        } else if (typeof conf == 'string') {
            conf = {tip: conf}; 
        }

        conf = $.extend(true, globals, conf);
        
        // can also be given as string
        if (typeof conf.position == 'string') {
            conf.position = conf.position.split(/,?\s/);    
        }
        
        // assign tip's only when apiement is being mouseovered     
        if (conf.lazy !== false && (conf.lazy === true || this.length > 20)) {  
                
            this.one("mouseover", function(e) { 
                api = new Tooltip($(this), conf);
                api.show(e);
                instances.push(api);
            }); 
            
        } else {
            
            // install tooltip for each entry in jQuery object
            this.each(function() {
                api = new Tooltip($(this), conf); 
                instances.push(api);
            });
        } 

        return conf.api ? api: this;        
        
    };
        
}) (jQuery);

/**
 * tools.tooltip "Slide Effect" 1.0.0
 * 
 * Copyright (c) 2009 Tero Piirainen
 * http://flowplayer.org/tools/tooltip.html#slide
 *
 * Dual licensed under MIT and GPL 2+ licenses
 * http://www.opensource.org/licenses
 *
 * Since  : September 2009
 * Date: ${date}
 * Revision: ${revision} 
 */
(function($) { 

    // version number
    var t = $.tools.tooltip;
    t.effects = t.effects || {};
    t.effects.slide = {version: '1.0.0'}; 
        
    // extend global configuragion with effect specific defaults
    $.extend(t.conf, { 
        direction: 'up', // down, left, right 
        bounce: false,
        slideOffset: 10,
        slideInSpeed: 200,
        slideOutSpeed: 200, 
        slideFade: !$.browser.msie
    });         
    
    // directions for slide effect
    var dirs = {
        up: ['-', 'top'],
        down: ['+', 'top'],
        left: ['-', 'left'],
        right: ['+', 'left']
    };
    
    /* default effect: "slide"  */
    $.tools.tooltip.addEffect("slide", 
        
        // show effect
        function(done) { 

            // variables
            var conf = this.getConf(), 
                 tip = this.getTip(),
                 params = conf.slideFade ? {opacity: conf.opacity} : {}, 
                 dir = dirs[conf.direction] || dirs.up;

            // direction            
            params[dir[1]] = dir[0] +'='+ conf.slideOffset;
            
            // perform animation
            if (conf.slideFade) { tip.css({opacity:0}); }
            tip.show().animate(params, conf.slideInSpeed, done); 
        }, 
        
        // hide effect
        function(done) {
            
            // variables
            var conf = this.getConf(), 
                 offset = conf.slideOffset,
                 params = conf.slideFade ? {opacity: 0} : {}, 
                 dir = dirs[conf.direction] || dirs.up;
            
            // direction
            var sign = "" + dir[0];
            if (conf.bounce) { sign = sign == '+' ? '-' : '+'; }            
            params[dir[1]] = sign +'='+ offset;         
            
            // perform animation
            this.getTip().animate(params, conf.slideOutSpeed, function()  {
                $(this).hide();
                done.call();        
            });
        }
    );  
    
})(jQuery); 

/**
 * tools.tooltip "Dynamic Plugin" 1.0.1
 * 
 * Copyright (c) 2009 Tero Piirainen
 * http://flowplayer.org/tools/tooltip.html#dynamic
 *
 * Dual licensed under MIT and GPL 2+ licenses
 * http://www.opensource.org/licenses
 *
 * Since  : July 2009
 * Date: ${date}
 * Revision: ${revision} 
 */
(function($) { 

    // version number
    var t = $.tools.tooltip;
    t.plugins = t.plugins || {};
    
    t.plugins.dynamic = {
        version: '1.0.1',
    
        conf: {
            api: false,
            classNames: "top right bottom left"
        }
    };
        
    /* 
     * See if element is on the viewport. Returns an boolean array specifying which
     * edges are hidden. Edges are in following order:
     * 
     * [top, right, bottom, left]
     * 
     * For example following return value means that top and right edges are hidden
     * 
     * [true, true, false, false]
     * 
     */
    function getCropping(el) {
        
        var w = $(window); 
        var right = w.width() + w.scrollLeft();
        var bottom = w.height() + w.scrollTop();        
        
        return [
            el.offset().top <= w.scrollTop(),                       // top
            right <= el.offset().left + el.width(),             // right
            bottom <= el.offset().top + el.height(),            // bottom
            w.scrollLeft() >= el.offset().left                  // left
        ]; 
    }
    
    /*
        Returns true if all edges of an element are on viewport. false if not
        
        @param crop the cropping array returned by getCropping function
     */
    function isVisible(crop) {
        var i = crop.length;
        while (i--) {
            if (crop[i]) { return false; }  
        }
        return true;
    }
    
    // scrollable mousewheel implementation
    $.fn.dynamic = function(conf) {
        
        var globals = $.extend({}, t.plugins.dynamic.conf), ret;
        if (typeof conf == 'number') { conf = {speed: conf}; }
        conf = $.extend(globals, conf);
        
        var cls = conf.classNames.split(/\s/), orig;    
            
        this.each(function() {      
                
            if ($(this).tooltip().jquery)  {
                throw "Lazy feature not supported by dynamic plugin. set lazy: false for tooltip";  
            }
                
            var api = $(this).tooltip().onBeforeShow(function(e, pos) {             

                // get nessessary variables
                var tip = this.getTip(), tipConf = this.getConf();  

                /*
                    We store the original configuration and use it to restore back to the original state.
                */                  
                if (!orig) {
                    orig = [
                        tipConf.position[0], 
                        tipConf.position[1], 
                        tipConf.offset[0], 
                        tipConf.offset[1], 
                        $.extend({}, tipConf)
                    ];
                }
                
                /*
                    display tip in it's default position and by setting visibility to hidden.
                    this way we can check whether it will be on the viewport
                */
                $.extend(tipConf, orig[4]);
                tipConf.position = [orig[0], orig[1]];
                tipConf.offset = [orig[2], orig[3]];
                
                tip.css({
                    visibility: 'hidden',
                    position: 'absolute',
                    top: pos.top,
                    left: pos.left
                    
                }).show(); 
                
                // now let's see for hidden edges
                var crop = getCropping(tip);        
                                
                // possibly alter the configuration
                if (!isVisible(crop)) {
                    
                    // change the position and add class
                    if (crop[2]) { $.extend(tipConf, conf.top);     tipConf.position[0] = 'top';        tip.addClass(cls[0]); }
                    if (crop[3]) { $.extend(tipConf, conf.right);   tipConf.position[1] = 'right';  tip.addClass(cls[1]); }                 
                    if (crop[0]) { $.extend(tipConf, conf.bottom);  tipConf.position[0] = 'bottom'; tip.addClass(cls[2]); } 
                    if (crop[1]) { $.extend(tipConf, conf.left);        tipConf.position[1] = 'left';   tip.addClass(cls[3]); }                 
                    
                    // vertical offset
                    if (crop[0] || crop[2]) { tipConf.offset[0] *= -1; }
                    
                    // horizontal offset
                    if (crop[1] || crop[3]) { tipConf.offset[1] *= -1; }
                }  
                
                tip.css({visibility: 'visible'}).hide();
        
            });
            
            // restore positioning
            api.onShow(function() {
                var c = this.getConf(), tip = this.getTip();                
                c.position = [orig[0], orig[1]];
                c.offset = [orig[2], orig[3]];              
            });
            
            // remove custom class names and restore original effect
            api.onHide(function() {
                var tip = this.getTip(); 
                tip.removeClass(conf.classNames);
            });
                
            ret = api;
            
        });
        
        return conf.api ? ret : this;
    };  
    
}) (jQuery);

/**
 * tools.scrollable 1.1.2 - Scroll your HTML with eye candy.
 * 
 * Copyright (c) 2009 Tero Piirainen
 * http://flowplayer.org/tools/scrollable.html
 *
 * Dual licensed under MIT and GPL 2+ licenses
 * http://www.opensource.org/licenses
 *
 * Launch  : March 2008
 * Date: ${date}
 * Revision: ${revision} 
 */
(function($) { 
        
    // static constructs
    $.tools = $.tools || {};
    
    $.tools.scrollable = {
        version: '1.1.2',
        
        conf: {
            
            // basics
            size: 5,
            vertical: false,
            speed: 400,
            keyboard: true,     
            
            // by default this is the same as size
            keyboardSteps: null, 
            
            // other
            disabledClass: 'disabled',
            hoverClass: null,       
            clickable: true,
            activeClass: 'active', 
            easing: 'swing',
            loop: false,
            
            items: '.items',
            item: null,
            
            // navigational elements            
            prev: '.prev',
            next: '.next',
            prevPage: '.prevPage',
            nextPage: '.nextPage', 
            api: false
            
            // CALLBACKS: onBeforeSeek, onSeek, onReload
        } 
    };
                
    var current;        
    
    // constructor
    function Scrollable(root, conf) {   
        
        // current instance
        var self = this, $self = $(this),
             horizontal = !conf.vertical,
             wrap = root.children(),
             index = 0,
             forward;  
        
        
        if (!current) { current = self; }
        
        // bind all callbacks from configuration
        $.each(conf, function(name, fn) {
            if ($.isFunction(fn)) { $self.bind(name, fn); }
        });
        
        if (wrap.length > 1) { wrap = $(conf.items, root); }
        
        // navigational items can be anywhere when globalNav = true
        function find(query) {
            var els = $(query);
            return conf.globalNav ? els : root.parent().find(query);    
        }
        
        // to be used by plugins
        root.data("finder", find);
        
        // get handle to navigational elements
        var prev = find(conf.prev),
             next = find(conf.next),
             prevPage = find(conf.prevPage),
             nextPage = find(conf.nextPage);

        
        // methods
        $.extend(self, {
            
            getIndex: function() {
                return index;   
            },
            
            getClickIndex: function() {
                var items = self.getItems(); 
                return items.index(items.filter("." + conf.activeClass));   
            },
    
            getConf: function() {
                return conf;    
            },
            
            getSize: function() {
                return self.getItems().size();  
            },
    
            getPageAmount: function() {
                return Math.ceil(this.getSize() / conf.size);   
            },
            
            getPageIndex: function() {
                return Math.ceil(index / conf.size);    
            },

            getNaviButtons: function() {
                return prev.add(next).add(prevPage).add(nextPage);  
            },
            
            getRoot: function() {
                return root;    
            },
            
            getItemWrap: function() {
                return wrap;    
            },
            
            getItems: function() {
                return wrap.children(conf.item);    
            },
            
            getVisibleItems: function() {
                return self.getItems().slice(index, index + conf.size); 
            },
            
            /* all seeking functions depend on this */      
            seekTo: function(i, time, fn) {

                if (i < 0) { i = 0; }               
                
                // nothing happens
                if (index === i) { return self; }               
                
                // function given as second argument
                if ($.isFunction(time)) {
                    fn = time;
                }

                // seeking exceeds the end               
                if (i > self.getSize() - conf.size) { 
                    return conf.loop ? self.begin() : this.end(); 
                }               

                var item = self.getItems().eq(i);                   
                if (!item.length) { return self; }              
                
                // onBeforeSeek
                var e = $.Event("onBeforeSeek");

                $self.trigger(e, [i]);              
                if (e.isDefaultPrevented()) { return self; }                
                
                // get the (possibly altered) speed
                if (time === undefined || $.isFunction(time)) { time = conf.speed; }
                
                function callback() {
                    if (fn) { fn.call(self, i); }
                    $self.trigger("onSeek", [i]);
                }
                
                if (horizontal) {
                    wrap.animate({left: -item.position().left}, time, conf.easing, callback);                   
                } else {
                    wrap.animate({top: -item.position().top}, time, conf.easing, callback);                         
                }
                
                
                current = self;
                index = i;              
                
                // onStart
                e = $.Event("onStart");
                $self.trigger(e, [i]);              
                if (e.isDefaultPrevented()) { return self; }                
    
                
                /* default behaviour */
                
                // prev/next buttons disabled flags
                prev.add(prevPage).toggleClass(conf.disabledClass, i === 0);
                next.add(nextPage).toggleClass(conf.disabledClass, i >= self.getSize() - conf.size);
                
                return self; 
            },          
            
                
            move: function(offset, time, fn) {
                forward = offset > 0;
                return this.seekTo(index + offset, time, fn);
            },
            
            next: function(time, fn) {
                return this.move(1, time, fn);  
            },
            
            prev: function(time, fn) {
                return this.move(-1, time, fn); 
            },
            
            movePage: function(offset, time, fn) {
                forward = offset > 0;
                var steps = conf.size * offset;
                
                var i = index % conf.size;
                if (i > 0) {
                    steps += (offset > 0 ? -i : conf.size - i);
                }
                
                return this.move(steps, time, fn);      
            },
            
            prevPage: function(time, fn) {
                return this.movePage(-1, time, fn);
            },  
    
            nextPage: function(time, fn) {
                return this.movePage(1, time, fn);
            },          
            
            setPage: function(page, time, fn) {
                return this.seekTo(page * conf.size, time, fn);
            },          
            
            begin: function(time, fn) {
                forward = false;
                return this.seekTo(0, time, fn);    
            },
            
            end: function(time, fn) {
                forward = true;
                var to = this.getSize() - conf.size;
                return to > 0 ? this.seekTo(to, time, fn) : self;   
            },
            
            reload: function() {                
                $self.trigger("onReload");
                return self;
            },          
            
            focus: function() {
                current = self;
                return self;
            },
            
            click: function(i) {
                
                var item = self.getItems().eq(i), 
                     klass = conf.activeClass,
                     size = conf.size;          
                
                // check that i is sane
                if (i < 0 || i >= self.getSize()) { return self; }
                
                // size == 1                            
                if (size == 1) {
                    if (conf.loop) { return self.next(); }
                    
                    if (i === 0 || i == self.getSize() -1)  { 
                        forward = (forward === undefined) ? true : !forward;     
                    }
                    return forward === false  ? self.prev() : self.next(); 
                } 
                
                // size == 2
                if (size == 2) {
                    if (i == index) { i--; }
                    self.getItems().removeClass(klass);
                    item.addClass(klass);                   
                    return self.seekTo(i, time, fn);
                }               
        
                if (!item.hasClass(klass)) {                
                    self.getItems().removeClass(klass);
                    item.addClass(klass);
                    var delta = Math.floor(size / 2);
                    var to = i - delta;
        
                    // next to last item must work
                    if (to > self.getSize() - size) { 
                        to = self.getSize() - size; 
                    }
        
                    if (to !== i) {
                        return self.seekTo(to);     
                    }
                }
                
                return self;
            },
            
            // bind / unbind
            bind: function(name, fn) {
                $self.bind(name, fn);
                return self;    
            },  
            
            unbind: function(name) {
                $self.unbind(name);
                return self;    
            }           
            
        });
        
        // callbacks    
        $.each("onBeforeSeek,onStart,onSeek,onReload".split(","), function(i, ev) {
            self[ev] = function(fn) {
                return self.bind(ev, fn);   
            };
        });  
            
            
        // prev button      
        prev.addClass(conf.disabledClass).click(function() {
            self.prev(); 
        });
        

        // next button
        next.click(function() { 
            self.next(); 
        });
        
        // prev page button
        nextPage.click(function() { 
            self.nextPage(); 
        });
        
        if (self.getSize() < conf.size) {
            next.add(nextPage).addClass(conf.disabledClass);    
        }
        

        // next page button
        prevPage.addClass(conf.disabledClass).click(function() { 
            self.prevPage(); 
        });     
        
        
        // hover
        var hc = conf.hoverClass, keyId = "keydown." + Math.random().toString().substring(10); 
            
        self.onReload(function() { 

            // hovering
            if (hc) {
                self.getItems().hover(function()  {
                    $(this).addClass(hc);       
                }, function() {
                    $(this).removeClass(hc);    
                });                     
            }
            
            // clickable
            if (conf.clickable) {
                self.getItems().each(function(i) {
                    $(this).unbind("click.scrollable").bind("click.scrollable", function(e) {
                        if ($(e.target).is("a")) { return; }    
                        return self.click(i);
                    });
                });
            }               
            
            // keyboard         
            if (conf.keyboard) {                
                
                // keyboard works on one instance at the time. thus we need to unbind first
                $(document).unbind(keyId).bind(keyId, function(evt) {

                    // do nothing with CTRL / ALT buttons
                    if (evt.altKey || evt.ctrlKey) { return; }
                    
                    // do nothing for unstatic and unfocused instances
                    if (conf.keyboard != 'static' && current != self) { return; }
                    
                    var s = conf.keyboardSteps;             
                                        
                    if (horizontal && (evt.keyCode == 37 || evt.keyCode == 39)) {                   
                        self.move(evt.keyCode == 37 ? -s : s);
                        return evt.preventDefault();
                    }   
                    
                    if (!horizontal && (evt.keyCode == 38 || evt.keyCode == 40)) {
                        self.move(evt.keyCode == 38 ? -s : s);
                        return evt.preventDefault();
                    }
                    
                    return true;
                    
                });
                
            } else  {
                $(document).unbind(keyId);  
            }               

        });
        
        self.reload(); 
        
    } 

        
    // jQuery plugin implementation
    $.fn.scrollable = function(conf) { 
            
        // already constructed --> return API
        var el = this.eq(typeof conf == 'number' ? conf : 0).data("scrollable");
        if (el) { return el; }       
 
        var globals = $.extend({}, $.tools.scrollable.conf);
        conf = $.extend(globals, conf);
        
        conf.keyboardSteps = conf.keyboardSteps || conf.size;
        
        this.each(function() {          
            el = new Scrollable($(this), conf);
            $(this).data("scrollable", el); 
        });
        
        return conf.api ? el: this; 
        
    };
            
    
})(jQuery);

/**
 * jQuery TOOLS plugin :: scrollable.circular 0.5.1
 * 
 * Copyright (c) 2009 Tero Piirainen
 * http://flowplayer.org/tools/scrollable.html#circular
 *
 * Dual licensed under MIT and GPL 2+ licenses
 * http://www.opensource.org/licenses
 *
 * Launch  : September 2009
 * Date: ${date}
 * Revision: ${revision} 
 */
(function($) {

    // version number
    var t = $.tools.scrollable; 
    t.plugins = t.plugins || {};
    
    t.plugins.circular = {
        version: '0.5.1', 
        conf: { 
            api: false,
            clonedClass: 'cloned'
        }       
    };

    
    $.fn.circular = function(opts)  {
    
        var config = $.extend({}, t.plugins.circular.conf), ret;
        $.extend(config, opts);
        
        this.each(function() {          
 
            var api = $(this).scrollable(),
                 items = api.getItems(), 
                 conf = api.getConf(), 
                 wrap = api.getItemWrap(), 
                 index = 0;
                 
            if (api) { ret = api; }
                
            // too few items. no need for this plugin.
            if (items.length < conf.size) { return false; }
            

            // clone first visible elements and append them to the end          
            items.slice(0, conf.size).each(function(i) {
                $(this).clone().appendTo(wrap).click(function()  {
                    api.click(items.length + i);
                    
                }).addClass(config.clonedClass);            
            });         
            
            // clone last set of elements to the beginning in reversed order
            var tail = $.makeArray(items.slice(-conf.size)).reverse();
            
            $(tail).each(function(i) {
                $(this).clone().prependTo(wrap).click(function()  {
                    api.click(-i -1);           
                    
                }).addClass(config.clonedClass);                
            });
            
            var allItems = wrap.children(conf.item);
            
            
            // reset hovering for cloned items too
            var hc = conf.hoverClass;
            
            if (hc) {
                allItems.hover(function()  {
                    $(this).addClass(hc);       
                }, function() {
                    $(this).removeClass(hc);    
                });                     
            }
         
            // custom seeking function that does not trigger callbacks
            function seek(i) {
                
                var item = allItems.eq(i);
                
                if (conf.vertical) {                        
                    wrap.css({top: -item.position().top});
                } else {
                    wrap.css({left: -item.position().left});                            
                }                   
            }
            
            // skip the clones at the beginning
            seek(conf.size);            

            // overridden scrollable API methods
            $.extend(api, {

                move: function(offset, time, fn, click) {  
                    
                    var to = index + offset + conf.size;                
                    var exceed = to > api.getSize() - conf.size; 
                    
                    if (to <= 0 || exceed) {
                        var fix = index + conf.size + (exceed ? -items.length : items.length);
                        seek(fix);
                        to = fix + offset;
                    } 
                    
                    if (click) {
                        allItems.removeClass(conf.activeClass)
                            .eq(to + Math.floor(conf.size / 2)).addClass(conf.activeClass);
                    }
                    
                    // nothing happens
                    if (to === index + conf.size) { return self; }                  

                    return api.seekTo(to, time, fn);
                },          
                
                begin: function(time, fn) {
                    return this.seekTo(conf.size, time, fn);    
                },
                
                end: function(time, fn) {               
                    return this.seekTo(items.length, time, fn); 
                },
                
                click: function(i, time, fn) {      
                    
                    if (!conf.clickable) { return self; }
                    if (conf.size == 1) { return this.next(); }
                    
                    var to = i - index, klass = conf.activeClass;               
                    to -= Math.floor(conf.size / 2);                
                    
                    return this.move(to, time, fn, true);
                },
                
                getIndex: function() {
                    return index;  
                },
                
                setPage: function(page, time, fn) {
                    return this.seekTo(page * conf.size + conf.size, time, fn); 
                },
                
                getPageAmount: function()  {
                    return Math.ceil(items.length / conf.size);     
                },
                
                getPageIndex: function()  {             
                    if (index < 0) { return this.getPageAmount() -1; }
                    if (index >= items.length) { return 0; }
                    var i = (index + conf.size) / conf.size -1;
                    return i;
                },

                getVisibleItems: function() {
                    var i = index + conf.size;
                    return allItems.slice(i, i + conf.size);    
                } 
                
            });  
            
            // update index 
            api.onStart(function(e, i) {        
                index = i - conf.size;
                
                // navi buttons are never disabled
                return false;
            });             
            
            api.getNaviButtons().removeClass(conf.disabledClass);
            
                
        });
        
        return config.api ? ret : this;
        
    };

        
})(jQuery);

/**
 * jQuery TOOLS plugin :: scrollable.autoscroll 1.0.1
 * 
 * Copyright (c) 2009 Tero Piirainen
 * http://flowplayer.org/tools/scrollable.html#autoscroll
 *
 * Dual licensed under MIT and GPL 2+ licenses
 * http://www.opensource.org/licenses
 *
 * Launch  : September 2009
 * Date: ${date}
 * Revision: ${revision} 
 */
(function($) {      

    var t = $.tools.scrollable; 
    t.plugins = t.plugins || {};
    
    t.plugins.autoscroll = {
        version: '1.0.1',
        
        conf: {
            autoplay: true,
            interval: 3000,
            autopause: true,
            steps: 1,
            api: false
        }
    };  
    
    // jQuery plugin implementation
    $.fn.autoscroll = function(conf) { 

        if (typeof conf == 'number') {
            conf = {interval: conf};    
        }
        
        var opts = $.extend({}, t.plugins.autoscroll.conf), ret;
        $.extend(opts, conf);       
        
        this.each(function() {      
                
            var api = $(this).scrollable();         
            if (api) { ret = api; }
            
            // interval stuff
            var timer, hoverTimer, stopped = true;
    
            api.play = function() {
    
                // do not start additional timer if already exists
                if (timer) { return; }
                
                stopped = false;
                
                // construct new timer
                timer = setInterval(function() { 
                    api.move(opts.steps);               
                }, opts.interval);
                
                api.move(opts.steps);
            };  

            api.pause = function() {
                timer = clearInterval(timer);   
            };
            
            // when stopped - mouseover won't restart 
            api.stop = function() {
                api.pause();
                stopped = true; 
            };
        
            /* when mouse enters, autoscroll stops */
            if (opts.autopause) {
                api.getRoot().add(api.getNaviButtons()).hover(function() {          
                    api.pause();
                    clearInterval(hoverTimer);
                    
                }, function() {
                    if (!stopped) {                     
                        hoverTimer = setTimeout(api.play, opts.interval);                       
                    }
                });
            }           
            
            if (opts.autoplay) {
                setTimeout(api.play, opts.interval);                
            }

        });
        
        return opts.api ? ret : this;
        
    }; 
    
})(jQuery);

/**
 * jQuery TOOLS plugin :: scrollable.navigator 1.0.2
 * 
 * Copyright (c) 2009 Tero Piirainen
 * http://flowplayer.org/tools/scrollable.html#navigator
 *
 * Dual licensed under MIT and GPL 2+ licenses
 * http://www.opensource.org/licenses
 *
 * Launch  : September 2009
 * Date: ${date}
 * Revision: ${revision} 
 */
(function($) {
        
    var t = $.tools.scrollable; 
    t.plugins = t.plugins || {};
    
    t.plugins.navigator = {
        version: '1.0.2',
        
        conf: {
            navi: '.navi',
            naviItem: null,     
            activeClass: 'active',
            indexed: false,
            api: false,
            idPrefix: null
        }
    };      
        
    // jQuery plugin implementation
    $.fn.navigator = function(conf) {

        var globals = $.extend({}, t.plugins.navigator.conf), ret;
        if (typeof conf == 'string') { conf = {navi: conf}; }
        
        conf = $.extend(globals, conf);
        
        this.each(function() {
            
            var api = $(this).scrollable(),
                 root = api.getRoot(), 
                 navi = root.data("finder").call(null, conf.navi), 
                 els = null, 
                 buttons = api.getNaviButtons();
            
            if (api) { ret = api; }
            
            api.getNaviButtons = function() {
                return buttons.add(navi);   
            }; 
                
            // generate new entries
            function reload() {
                
                if (!navi.children().length || navi.data("navi") == api) {
                    
                    navi.empty();
                    navi.data("navi", api);
                    
                    for (var i = 0; i < api.getPageAmount(); i++) {     
                        navi.append($("<" + (conf.naviItem || 'a') + "/>"));
                    }
                    
                    els = navi.children().each(function(i) {
                        var el = $(this);
                        el.click(function(e) {
                            api.setPage(i);                         
                            return e.preventDefault();
                        });
                        
                        // possible index number
                        if (conf.indexed)  { el.text(i); }
                        if (conf.idPrefix) { el.attr("id", conf.idPrefix + i); }
                    });
                    
                    
                // assign onClick events to existing entries
                } else {
                    
                    // find a entries first -> syntaxically correct
                    els = conf.naviItem ? navi.find(conf.naviItem) : navi.children();
                    
                    els.each(function(i)  {
                        var el = $(this);
                        
                        el.click(function(evt) {
                            api.setPage(i);
                            return evt.preventDefault();                        
                        });
                        
                    });
                }
                
                // activate first entry
                els.eq(0).addClass(conf.activeClass); 
                
            }
            
            // activate correct entry
            api.onStart(function(e, index) {
                var cls = conf.activeClass;             
                els.removeClass(cls).eq(api.getPageIndex()).addClass(cls);
            });
            
            api.onReload(function() {
                reload();       
            });
            
            reload();           
            
            // look for correct navi item from location.hash
            var el = els.filter("[href=" + location.hash + "]");    
            if (el.length) { api.move(els.index(el)); }         
            
            
        });     
        
        return conf.api ? ret : this;
        
    };
    
})(jQuery);

/**
 * jQuery TOOLS plugin :: scrollable.mousewheel 1.0.1
 * 
 * Copyright (c) 2009 Tero Piirainen
 * http://flowplayer.org/tools/scrollable.html#mousewheel
 *
 * Dual licensed under MIT and GPL 2+ licenses
 * http://www.opensource.org/licenses
 *
 * Launch  : September 2009
 * Date: ${date}
 * Revision: ${revision} 
 *
 * 
 * jquery.event.wheel.js - rev 1 
 * Copyright (c) 2008, Three Dub Media (http://threedubmedia.com)
 * Liscensed under the MIT License (MIT-LICENSE.txt)
 * http://www.opensource.org/licenses/mit-license.php
 * Created: 2008-07-01 | Updated: 2008-07-14
 */
(function($) {
        
    $.fn.wheel = function( fn ){
        return this[ fn ? "bind" : "trigger" ]( "wheel", fn );
    };

    // special event config
    $.event.special.wheel = {
        setup: function(){
            $.event.add( this, wheelEvents, wheelHandler, {} );
        },
        teardown: function(){
            $.event.remove( this, wheelEvents, wheelHandler );
        }
    };

    // events to bind ( browser sniffed... )
    var wheelEvents = !$.browser.mozilla ? "mousewheel" : // IE, opera, safari
        "DOMMouseScroll"+( $.browser.version<"1.9" ? " mousemove" : "" ); // firefox

    // shared event handler
    function wheelHandler( event ) {
        
        switch ( event.type ){
            
            // FF2 has incorrect event positions
            case "mousemove": 
                return $.extend( event.data, { // store the correct properties
                    clientX: event.clientX, clientY: event.clientY,
                    pageX: event.pageX, pageY: event.pageY
                });
                
            // firefox  
            case "DOMMouseScroll": 
                $.extend( event, event.data ); // fix event properties in FF2
                event.delta = -event.detail / 3; // normalize delta
                break;
                
            // IE, opera, safari    
            case "mousewheel":              
                event.delta = event.wheelDelta / 120;
                break;
        }
        
        event.type = "wheel"; // hijack the event   
        return $.event.handle.call( this, event, event.delta );
    }
    
    
    // version number
    var t = $.tools.scrollable; 
    t.plugins = t.plugins || {};
    t.plugins.mousewheel = {    
        version: '1.0.1',
        conf: { 
            api: false,
            speed: 50
        } 
    }; 
    
    // scrollable mousewheel implementation
    $.fn.mousewheel = function(conf) {

        var globals = $.extend({}, t.plugins.mousewheel.conf), ret;
        if (typeof conf == 'number') { conf = {speed: conf}; }
        conf = $.extend(globals, conf);
        
        this.each(function() {      

            var api = $(this).scrollable();
            if (api) { ret = api; }
            
            api.getRoot().wheel(function(e, delta)  { 
                api.move(delta < 0 ? 1 : -1, conf.speed || 50);
                return false;
            });
        });
        
        return conf.api ? ret : this;
    };
    
})(jQuery); 

/**
 * tools.overlay 1.1.2 - Overlay HTML with eye candy.
 * 
 * Copyright (c) 2009 Tero Piirainen
 * http://flowplayer.org/tools/overlay.html
 *
 * Dual licensed under MIT and GPL 2+ licenses
 * http://www.opensource.org/licenses
 *
 * Launch  : March 2008
 * Date: ${date}
 * Revision: ${revision} 
 */
(function($) { 

    // static constructs
    $.tools = $.tools || {};
    
    $.tools.overlay = {
        
        version: '1.1.2',
        
        addEffect: function(name, loadFn, closeFn) {
            effects[name] = [loadFn, closeFn];  
        },
    
        conf: {  
            top: '10%', 
            left: 'center',
            absolute: false,
            
            speed: 'normal',
            closeSpeed: 'fast',
            effect: 'default',
            
            close: null,    
            oneInstance: true,
            closeOnClick: true,
            closeOnEsc: true, 
            api: false,
            expose: null,
            
            // target element to be overlayed. by default taken from [rel]
            target: null 
        }
    };

    
    var effects = {};
        
    // the default effect. nice and easy!
    $.tools.overlay.addEffect('default', 
        
        /* 
            onLoad/onClose functions must be called otherwise none of the 
            user supplied callback methods won't be called
        */
        function(onLoad) { 
            this.getOverlay().fadeIn(this.getConf().speed, onLoad); 
            
        }, function(onClose) {
            this.getOverlay().fadeOut(this.getConf().closeSpeed, onClose);          
        }       
    );
    
        
    var instances = [];     

    
    function Overlay(trigger, conf) {       
        
        // private variables
        var self = this, 
             $self = $(this),
             w = $(window), 
             closers,
             overlay,
             opened,
             expose = conf.expose && $.tools.expose.version;
        
        // get overlay and triggerr
        var jq = conf.target || trigger.attr("rel");
        overlay = jq ? $(jq) : null || trigger; 
        
        // overlay not found. cannot continue
        if (!overlay.length) { throw "Could not find Overlay: " + jq; }
        
        // if trigger is given - assign it's click event
        if (trigger && trigger.index(overlay) == -1) {
            trigger.click(function(e) {             
                self.load(e);
                return e.preventDefault();
            });
        }               
        
        // bind all callbacks from configuration
        $.each(conf, function(name, fn) {
            if ($.isFunction(fn)) { $self.bind(name, fn); }
        });   
        
        
        // API methods  
        $.extend(self, {

            load: function(e) {
                
                // can be opened only once
                if (self.isOpened()) { return self; } 

                
                // find the effect
                var eff = effects[conf.effect];
                if (!eff) { throw "Overlay: cannot find effect : \"" + conf.effect + "\""; }
                
                // close other instances?
                if (conf.oneInstance) {
                    $.each(instances, function() {
                        this.close(e);
                    });
                }
                
                // onBeforeLoad
                e = e || $.Event();
                e.type = "onBeforeLoad";
                $self.trigger(e);               
                if (e.isDefaultPrevented()) { return self; }                

                // opened
                opened = true;
                
                // possible expose effect
                if (expose) { overlay.expose().load(e); }               
                
                // calculate end position 
                var top = conf.top;                 
                var left = conf.left;

                // get overlay dimensions
                var oWidth = overlay.outerWidth({margin:true});
                var oHeight = overlay.outerHeight({margin:true}); 
                
                if (typeof top == 'string')  {
                    top = top == 'center' ? Math.max((w.height() - oHeight) / 2, 0) : 
                        parseInt(top, 10) / 100 * w.height();           
                }               
                
                if (left == 'center') { left = Math.max((w.width() - oWidth) / 2, 0); }
                
                if (!conf.absolute)  {
                    top += w.scrollTop();
                    left += w.scrollLeft();
                } 
                
                // position overlay
                overlay.css({top: top, left: left, position: 'absolute'}); 
                
                // onStart
                e.type = "onStart";
                $self.trigger(e); 
                
                // load effect                  
                eff[0].call(self, function() {                  
                    if (opened) {
                        e.type = "onLoad";
                        $self.trigger(e);
                    }
                });                 
        
                // when window is clicked outside overlay, we close
                if (conf.closeOnClick) {                    
                    $(document).bind("click.overlay", function(e) { 
                        if (!self.isOpened()) { return; }
                        var et = $(e.target); 
                        if (et.parents(overlay).length > 1) { return; }
                        $.each(instances, function() {
                            this.close(e);
                        }); 
                    });                     
                }                       
                
                // keyboard::escape
                if (conf.closeOnEsc) {
                    
                    // one callback is enough if multiple instances are loaded simultaneously
                    $(document).unbind("keydown.overlay").bind("keydown.overlay", function(e) {
                        if (e.keyCode == 27) {
                            $.each(instances, function() {
                                this.close(e);                              
                            });  
                        }
                    });         
                }

                return self; 
            }, 
            
            close: function(e) {

                if (!self.isOpened()) { return self; }
                
                e = e || $.Event();
                e.type = "onBeforeClose";
                $self.trigger(e);               
                if (e.isDefaultPrevented()) { return; }             
                
                opened = false;
                
                // close effect
                effects[conf.effect][1].call(self, function() {
                    e.type = "onClose";
                    $self.trigger(e); 
                });
                
                // if all instances are closed then we unbind the keyboard / clicking actions
                var allClosed = true;
                $.each(instances, function() {
                    if (this.isOpened()) { allClosed = false; }
                });             
                
                if (allClosed) {
                    $(document).unbind("click.overlay").unbind("keydown.overlay");      
                }
                
                            
                return self;
            }, 
            
            // @deprecated
            getContent: function() {
                return overlay; 
            }, 
            
            getOverlay: function() {
                return overlay; 
            },
            
            getTrigger: function() {
                return trigger; 
            },
            
            getClosers: function() {
                return closers; 
            },          

            isOpened: function()  {
                return opened;
            },
            
            // manipulate start, finish and speeds
            getConf: function() {
                return conf;    
            },

            // bind
            bind: function(name, fn) {
                $self.bind(name, fn);
                return self;    
            },      
            
            // unbind
            unbind: function(name) {
                $self.unbind(name);
                return self;    
            }           
            
        });
        
        // callbacks    
        $.each("onBeforeLoad,onStart,onLoad,onBeforeClose,onClose".split(","), function(i, ev) {
            self[ev] = function(fn) {
                return self.bind(ev, fn);   
            };
        });
        
        
        // exposing effect
        if (expose) {
            
            // expose configuration
            if (typeof conf.expose == 'string') { conf.expose = {color: conf.expose}; }
                        
            $.extend(conf.expose, {
                api: true,
                closeOnClick: conf.closeOnClick,
                
                // only overlay control's the esc button
                closeOnEsc: false
            });
            
            // initialize expose api
            var ex = overlay.expose(conf.expose);
            
            ex.onBeforeClose(function(e) {
                self.close(e);      
            });
            
            self.onClose(function(e) {
                ex.close(e);        
            });
        }       
        
        // close button
        closers = overlay.find(conf.close || ".close");     
        
        if (!closers.length && !conf.close) {
            closers = $('<div class="close"></div>');
            overlay.prepend(closers);   
        }       
        
        closers.click(function(e) { 
            self.close(e);  
        });                 
    }
    
    // jQuery plugin initialization
    $.fn.overlay = function(conf) {   
        
        // already constructed --> return API
        var el = this.eq(typeof conf == 'number' ? conf : 0).data("overlay");
        if (el) { return el; }           
        
        if ($.isFunction(conf)) {
            conf = {onBeforeLoad: conf};    
        }
        
        var globals = $.extend({}, $.tools.overlay.conf); 
        conf = $.extend(true, globals, conf);
        
        this.each(function() {      
            el = new Overlay($(this), conf);
            instances.push(el);
            $(this).data("overlay", el);    
        });
        
        return conf.api ? el: this;     
    }; 
    
})(jQuery);

/**
 * Overlay Gallery plugin, version: 1.0.0
 * 
 * Copyright (c) 2009 Tero Piirainen
 * http://flowplayer.org/tools/overlay.html#gallery
 *
 * Dual licensed under MIT and GPL 2+ licenses
 * http://www.opensource.org/licenses
 *
 * Since  : July 2009
 * Date: ${date}
 * Revision: ${revision} 
 */
(function($) { 
        
    // TODO: next(), prev(), getIndex(), onChange event
    
    // version number
    var t = $.tools.overlay; 
    t.plugins = t.plugins || {};
    
    t.plugins.gallery = {
        version: '1.0.0', 
        conf: { 
            imgId: 'img',
            next: '.next',
            prev: '.prev',
            info: '.info',
            progress: '.progress',
            disabledClass: 'disabled',
            activeClass: 'active',
            opacity: 0.8,
            speed: 'slow',
            template: '<strong>${title}</strong> <span>Image ${index} of ${total}</span>',  
            autohide: true,
            preload: true,
            api: false
        }
    };          
    
    $.fn.gallery = function(opts) {
        
        var conf = $.extend({}, t.plugins.gallery.conf), api;
        $.extend(conf, opts);       

        // common variables for all gallery images
        api = this.overlay();
        
        var links = this,
             overlay = api.getOverlay(),
             next = overlay.find(conf.next),
             prev = overlay.find(conf.prev),
             info = overlay.find(conf.info),
             progress = overlay.find(conf.progress),
             els = prev.add(next).add(info).css({opacity: conf.opacity}),
             close = api.getClosers(),           
             index;
        
        
//{{{ load 

        function load(el) {
            
            progress.fadeIn();
            els.hide(); close.hide();
            
            var url = el.attr("href"); 
            
            // download the image 
            var image = new Image();
            
            image.onload = function() {
                
                progress.fadeOut();
                
                // find image inside overlay
                var img = $("#" + conf.imgId, overlay); 
                
                // or append it to the overlay 
                if (!img.length) { 
                    img = $("<img/>").attr("id", conf.imgId).css("visibility", "hidden");
                    overlay.prepend(img);
                }
                
                // make initially invisible to get it's dimensions
                img.attr("src", url).css("visibility", "hidden");           
                    
                // animate overlay to fit the image dimensions
                var width = image.width;
                var left = ($(window).width() - width) / 2;
                    
                // calculate index number
                index = links.index(links.filter("[href=" +url+ "]"));  
                
                // activate trigger
                links.removeClass(conf.activeClass).eq(index).addClass(conf.activeClass);
                
                // enable/disable next/prev links
                var cls = conf.disabledClass;
                els.removeClass(cls);

                if (index === 0) { prev.addClass(cls); }
                if (index == links.length -1) { next.addClass(cls); }
                
                
                // set info text & width
                var text = conf.template
                    .replace("${title}", el.attr("title") || el.data("title"))
                    .replace("${index}", index + 1)
                    .replace("${total}", links.length);
                    
                var padd = parseInt(info.css("paddingLeft"), 10) +  parseInt(info.css("paddingRight"), 10);
                info.html(text).css({width: width - padd});             
                
                overlay.animate({
                    width: width, height: image.height, left: left}, conf.speed, function() {
                        
                    // gradually show the image
                    img.hide().css("visibility", "visible").fadeIn(function() {                     
                        if (!conf.autohide) { 
                            els.fadeIn(); close.show(); 
                        }                                                       
                    });                             

                }); 
            };
            
            image.onerror = function() {
                overlay.fadeIn().html("Cannot find image " + url); 
            };
            
            image.src = url;
            
            if (conf.preload) {
                links.filter(":eq(" +(index-1)+ "), :eq(" +(index+1)+ ")").each(function()  {
                    var img = new Image();
                    img.src = $(this).attr("href");                 
                });
            }
            
        }
        
//}}}


        // function to add click handlers to next/prev links     
        function addClick(el, isNext)  {
            
            el.click(function() {
                    
                if (el.hasClass(conf.disabledClass)) { return; }                
                
                // find the triggering link
                var trigger = links.eq(i = index + (isNext ? 1 : -1));          
                     
                // if found load it's href
                if (trigger.length) { load(trigger); }
                
            });             
        }

        // assign next/prev click handlers
        addClick(next, true);
        addClick(prev);

        
        // arrow keys
        $(document).keydown(function(evt) {
                
            if (!overlay.is(":visible") || evt.altKey || evt.ctrlKey) { return; }
            
            if (evt.keyCode == 37 || evt.keyCode == 39) {                   
                var btn = evt.keyCode == 37 ? prev : next;
                btn.click();
                return evt.preventDefault();
            }   
            return true;            
        });     
        
        function showEls() {
            if (!overlay.is(":animated")) {
                els.show(); close.show();       
            }   
        }
        
        // autohide functionality
        if (conf.autohide) { 
            overlay.hover(showEls, function() { els.fadeOut();  close.hide(); }).mousemove(showEls);
        }       
        
        // load a proper gallery image when overlay trigger is clicked
        var ret;
        
        this.each(function() {
                
            var el = $(this), api = $(this).overlay(), ret = api;
            
            api.onBeforeLoad(function() {
                load(el);
            });
            
            api.onClose(function() {
                links.removeClass(conf.activeClass);    
            });         
        });         
        
        return conf.api ? ret : this;
        
    };
    
})(jQuery); 

/**
 * tools.overlay "Apple Effect" 1.0.1
 * 
 * Copyright (c) 2009 Tero Piirainen
 * http://flowplayer.org/tools/overlay.html#apple
 *
 * Dual licensed under MIT and GPL 2+ licenses
 * http://www.opensource.org/licenses
 *
 * Since  : July 2009
 * Date: ${date}
 * Revision: ${revision} 
 */
(function($) { 

    // version number
    var t = $.tools.overlay;
    t.effects = t.effects || {};
    t.effects.apple = {version: '1.0.1'}; 
        
    // extend global configuragion with effect specific defaults
    $.extend(t.conf, { 
        start: { 
            absolute: true,
            top: null,
            left: null
        },
        
        fadeInSpeed: 'fast',
        zIndex: 9999
    });         
    
    // utility function
    function getPosition(el) {
        var p = el.offset();
        return [p.top + el.height() / 2, p.left + el.width() / 2]; 
    }
    
//{{{ load 

    var loadEffect = function(onLoad) {
        
        var overlay = this.getOverlay(),
             opts = this.getConf(),
             trigger = this.getTrigger(),
             self = this,
             oWidth = overlay.outerWidth({margin:true}),
             img = overlay.data("img");  
        
        
        // growing image is required.
        if (!img) { 
            var bg = overlay.css("backgroundImage");
            
            if (!bg) { 
                throw "background-image CSS property not set for overlay"; 
            }
            
            // url("bg.jpg") --> bg.jpg
            bg = bg.substring(bg.indexOf("(") + 1, bg.indexOf(")")).replace(/\"/g, "");
            overlay.css("backgroundImage", "none");
            
            img = $('<img src="' + bg + '"/>');
            img.css({border:0,position:'absolute',display:'none'}).width(oWidth);           
            $('body').append(img); 
            overlay.data("img", img);
        }
        
        // initial top & left
        var w = $(window),
             itop = opts.start.top || Math.round(w.height() / 2), 
             ileft = opts.start.left || Math.round(w.width() / 2);
        
        if (trigger) {
            var p = getPosition(trigger);
            itop = p[0];
            ileft = p[1];
        } 
        
        // adjust positioning relative toverlay scrolling position
        if (!opts.start.absolute) {
            itop += w.scrollTop();
            ileft += w.scrollLeft();
        }
        
        // initialize background image and make it visible
        img.css({
            top: itop, 
            left: ileft,
            width: 0,
            zIndex: opts.zIndex
        }).show();
        
        // begin growing
        img.animate({
            top: overlay.css("top"), 
            left: overlay.css("left"), 
            width: oWidth}, opts.speed, function() { 

            // set close button and content over the image
            overlay.css("zIndex", opts.zIndex + 1).fadeIn(opts.fadeInSpeed, function()  { 
                
                if (self.isOpened() && !$(this).index(overlay)) {                    
                    onLoad.call(); 
                } else {
                    overlay.hide(); 
                } 
            });
        });
        
    };
//}}}
    
    
    var closeEffect = function(onClose) {

        // variables
        var overlay = this.getOverlay(), 
             opts = this.getConf(),
             trigger = this.getTrigger(),
             top = opts.start.top,
             left = opts.start.left;
        
        
        // hide overlay & closers
        overlay.hide();
        
        // trigger position
        if (trigger) {
            var p = getPosition(trigger);
            top = p[0];
            left = p[1];
        } 
        
        // shrink image     
        overlay.data("img").animate({top: top, left: left, width:0}, opts.closeSpeed, onClose); 
    };
    
    
    // add overlay effect   
    t.addEffect("apple", loadEffect, closeEffect); 
    
})(jQuery); 

/**
 * tools.expose 1.0.5 - Make HTML elements stand out
 * 
 * Copyright (c) 2009 Tero Piirainen
 * http://flowplayer.org/tools/expose.html
 *
 * Dual licensed under MIT and GPL 2+ licenses
 * http://www.opensource.org/licenses
 *
 * Launch  : June 2008
 * Date: ${date}
 * Revision: ${revision} 
 */
(function($) {  

    // static constructs
    $.tools = $.tools || {};
    
    $.tools.expose = {
        version: '1.0.5',  
        conf: { 

            // mask settings
            maskId: null,
            loadSpeed: 'slow',
            closeSpeed: 'fast',
            closeOnClick: true,
            closeOnEsc: true,
            
            // css settings
            zIndex: 9998,
            opacity: 0.8,
            color: '#456',
            api: false
        }
    };

    /* one of the greatest headaches in the tool. finally made it */
    function viewport() {
                
        // the horror case
        if ($.browser.msie) {
            
            // if there are no scrollbars then use window.height
            var d = $(document).height(), w = $(window).height();
            
            return [
                window.innerWidth ||                            // ie7+
                document.documentElement.clientWidth ||     // ie6  
                document.body.clientWidth,                  // ie6 quirks mode
                d - w < 20 ? w : d
            ];
        } 
        
        // other well behaving browsers
        return [$(window).width(), $(document).height()];
        
    } 
    
    function Expose(els, conf) { 
        
        // private variables
        var self = this, $self = $(this), mask = null, loaded = false, origIndex = 0;       
        
        // bind all callbacks from configuration
        $.each(conf, function(name, fn) {
            if ($.isFunction(fn)) { $self.bind(name, fn); }
        });  

        // adjust mask size when window is resized (or firebug is toggled)
        $(window).resize(function() {
            self.fit();
        }); 
        
        
        // public methods
        $.extend(this, {
        
            getMask: function() {
                return mask;    
            },
            
            getExposed: function() {
                return els; 
            },
            
            getConf: function() {
                return conf;    
            },      
            
            isLoaded: function() {
                return loaded;  
            },
            
            load: function(e) { 
                
                // already loaded ?
                if (loaded) { return self;  }
    
                origIndex = els.eq(0).css("zIndex");                
                
                // find existing mask
                if (conf.maskId) { mask = $("#" + conf.maskId); }
                    
                if (!mask || !mask.length) {
                    
                    var size = viewport();
                    
                    mask = $('<div/>').css({                
                        position:'absolute', 
                        top:0, 
                        left:0,
                        width: size[0],
                        height: size[1],
                        display:'none',
                        opacity: 0,                         
                        zIndex:conf.zIndex  
                    });                     
                    
                    // id
                    if (conf.maskId) { mask.attr("id", conf.maskId); }                  
                    
                    $("body").append(mask); 
                    
                    
                    // background color 
                    var bg = mask.css("backgroundColor");
                    
                    if (!bg || bg == 'transparent' || bg == 'rgba(0, 0, 0, 0)') {
                        mask.css("backgroundColor", conf.color);    
                    }   
                    
                    // esc button
                    if (conf.closeOnEsc) {                      
                        $(document).bind("keydown.unexpose", function(evt) {                            
                            if (evt.keyCode == 27) {
                                self.close();   
                            }       
                        });         
                    }
                    
                    // mask click closes
                    if (conf.closeOnClick) {
                        mask.bind("click.unexpose", function(e)  {
                            self.close(e);      
                        });                 
                    }                   
                }               
                
                // possibility to cancel click action
                e = e || $.Event();
                e.type = "onBeforeLoad";
                $self.trigger(e);           
                
                if (e.isDefaultPrevented()) { return self; }
                
                // make sure element is positioned absolutely or relatively
                $.each(els, function() {
                    var el = $(this);
                    if (!/relative|absolute|fixed/i.test(el.css("position"))) {
                        el.css("position", "relative");     
                    }                   
                });
             
                // make elements sit on top of the mask             
                els.css({zIndex:Math.max(conf.zIndex + 1, origIndex == 'auto' ? 0 : origIndex)});               

                
                // reveal mask
                var h = mask.height();
                
                if (!this.isLoaded()) { 
                    
                    mask.css({opacity: 0, display: 'block'}).fadeTo(conf.loadSpeed, conf.opacity, function() {

                        // sometimes IE6 misses the height property on fadeTo method
                        if (mask.height() != h) { mask.css("height", h); }
                        e.type = "onLoad";                      
                        $self.trigger(e);   
                         
                    });                 
                }
                    
                loaded = true;
                
                return self;
            }, 
            
            
            close: function(e) {
                                
                if (!loaded) { return self; }   

                e = e || $.Event();
                e.type = "onBeforeClose";
                $self.trigger(e);               
                if (e.isDefaultPrevented()) { return self; }
                
                mask.fadeOut(conf.closeSpeed, function() {
                    e.type = "onClose";
                    $self.trigger(e);
                    els.css({zIndex: $.browser.msie ? origIndex : null});
                });                                             
                
                loaded = false;
                return self; 
            },
            
            fit: function() {
                if (mask) {
                    var size = viewport();              
                    mask.css({ width: size[0], height: size[1]});
                }   
            },
            
            bind: function(name, fn) {
                $self.bind(name, fn);
                return self;    
            },          
            
            unbind: function(name) {
                $self.unbind(name);
                return self;    
            }               
            
        });     
        
        // callbacks    
        $.each("onBeforeLoad,onLoad,onBeforeClose,onClose".split(","), function(i, ev) {
            self[ev] = function(fn) {
                return self.bind(ev, fn);   
            };
        });         

    }
    
    
    // jQuery plugin implementation
    $.fn.expose = function(conf) {
        
        var el = this.eq(typeof conf == 'number' ? conf : 0).data("expose");
        if (el) { return el; }
        
        if (typeof conf == 'string') {
            conf = {color: conf};
        }
        
        var globals = $.extend({}, $.tools.expose.conf);
        conf = $.extend(globals, conf);     

        // construct exposes
        this.each(function() {
            el = new Expose($(this), conf);
            $(this).data("expose", el);  
        });     
        
        return conf.api ? el: this;     
    };      


})(jQuery);

/** 
 * flowplayer.js 3.1.4. The Flowplayer API
 * 
 * Copyright 2009 Flowplayer Oy
 * 
 * This file is part of Flowplayer.
 * 
 * Flowplayer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * Flowplayer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Flowplayer.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * Date: 2009-02-25 21:24:29 +0000 (Wed, 25 Feb 2009)
 * Revision: 166 
 */
(function() {
 
/* 
    FEATURES 
    --------
    - $f() and flowplayer() functions   
    - handling multiple instances 
    - Flowplayer programming API 
    - Flowplayer event model    
    - player loading / unloading    
    - jQuery support
*/ 
 

/*jslint glovar: true, browser: true */
/*global flowplayer, $f */

// {{{ private utility methods
    
    function log(args) {
        console.log("$f.fireEvent", [].slice.call(args));   
    }

        
    // thanks: http://keithdevens.com/weblog/archive/2007/Jun/07/javascript.clone
    function clone(obj) {   
        if (!obj || typeof obj != 'object') { return obj; }     
        var temp = new obj.constructor();   
        for (var key in obj) {  
            if (obj.hasOwnProperty(key)) {
                temp[key] = clone(obj[key]);
            }
        }       
        return temp;
    }

    // stripped from jQuery, thanks John Resig 
    function each(obj, fn) {
        if (!obj) { return; }
        
        var name, i = 0, length = obj.length;
    
        // object
        if (length === undefined) {
            for (name in obj) {
                if (fn.call(obj[name], name, obj[name]) === false) { break; }
            }
            
        // array
        } else {
            for (var value = obj[0];
                i < length && fn.call( value, i, value ) !== false; value = obj[++i]) {             
            }
        }
    
        return obj;
    }

    
    // convenience
    function el(id) {
        return document.getElementById(id);     
    }   

    
    // used extensively. a very simple implementation. 
    function extend(to, from, skipFuncs) {
        if (typeof from != 'object') { return to; }
        
        if (to && from) {           
            each(from, function(name, value) {
                if (!skipFuncs || typeof value != 'function') {
                    to[name] = value;       
                }
            });
        }
        
        return to;
    }
    
    // var arr = select("elem.className"); 
    function select(query) {
        var index = query.indexOf("."); 
        if (index != -1) {
            var tag = query.substring(0, index) || "*";
            var klass = query.substring(index + 1, query.length);
            var els = [];
            each(document.getElementsByTagName(tag), function() {
                if (this.className && this.className.indexOf(klass) != -1) {
                    els.push(this);     
                }
            });
            return els;
        }
    }
    
    // fix event inconsistencies across browsers
    function stopEvent(e) {
        e = e || window.event;
        
        if (e.preventDefault) {
            e.stopPropagation();
            e.preventDefault();
            
        } else {
            e.returnValue = false;  
            e.cancelBubble = true;
        } 
        return false;
    }

    // push an event listener into existing array of listeners
    function bind(to, evt, fn) {
        to[evt] = to[evt] || [];
        to[evt].push(fn);       
    }
    
    
    // generates an unique id
   function makeId() {
      return "_" + ("" + Math.random()).substring(2, 10);   
   }
    
//}}}   
    

// {{{ Clip

    var Clip = function(json, index, player) {
        
        // private variables
        var self = this;
        var cuepoints = {};
        var listeners = {};  
        self.index = index;
        
        // instance variables
        if (typeof json == 'string') {
            json = {url:json};  
        }
    
        extend(this, json, true);   
        
        // event handling 
        each(("Begin*,Start,Pause*,Resume*,Seek*,Stop*,Finish*,LastSecond,Update,BufferFull,BufferEmpty,BufferStop").split(","),
            function() {
            
            var evt = "on" + this;
                
            // before event
            if (evt.indexOf("*") != -1) {
                evt = evt.substring(0, evt.length -1); 
                var before = "onBefore" + evt.substring(2); 
                
                self[before] = function(fn) {
                    bind(listeners, before, fn);
                    return self;
                };              
            }  
            
            self[evt] = function(fn) {
                bind(listeners, evt, fn);
                return self;
            };
            
            
            // set common clip event listeners to player level
            if (index == -1) {
                if (self[before]) {
                    player[before] = self[before];      
                }               
                if (self[evt])  {
                    player[evt] = self[evt];        
                }
            }
            
        });           
        
        extend(this, { 
             
            onCuepoint: function(points, fn) {    
                
                // embedded cuepoints
                if (arguments.length == 1) {
                    cuepoints.embedded = [null, points];
                    return self;
                }
                
                if (typeof points == 'number') {
                    points = [points];  
                }
                
                var fnId = makeId();  
                cuepoints[fnId] = [points, fn]; 
                
                if (player.isLoaded()) {
                    player._api().fp_addCuepoints(points, index, fnId); 
                }  
                
                return self;
            },
            
            update: function(json) {
                extend(self, json);

                if (player.isLoaded()) {
                    player._api().fp_updateClip(json, index);   
                }
                var conf = player.getConfig(); 
                var clip = (index == -1) ? conf.clip : conf.playlist[index];
                extend(clip, json, true);
            },
            
            
            // internal event for performing clip tasks. should be made private someday
            _fireEvent: function(evt, arg1, arg2, target) {                 
                
                if (evt == 'onLoad') { 
                    each(cuepoints, function(key, val) {
                        if (val[0]) {
                            player._api().fp_addCuepoints(val[0], index, key);      
                        }
                    }); 
                    return false;
                }
                
                // target clip we are working against
                target = target || self;    
                
                if (evt == 'onCuepoint') {
                    var fn = cuepoints[arg1];
                    if (fn) {
                        return fn[1].call(player, target, arg2);
                    }
                }  

                // 1. clip properties, 2-3. metadata, 4. updates, 5. resumes from nested clip
                if (arg1 && "onBeforeBegin,onMetaData,onStart,onUpdate,onResume".indexOf(evt) != -1) {                  
                    // update clip properties
                    extend(target, arg1);                   
                    
                    if (arg1.metaData) {
                        if (!target.duration) {
                            target.duration = arg1.metaData.duration;   
                        } else {
                            target.fullDuration = arg1.metaData.duration;   
                        }                   
                    }
                }               
                

                var ret = true;
                each(listeners[evt], function() {
                    ret = this.call(player, target, arg1, arg2);        
                }); 
                return ret;             
            }           
            
        });
        
        
        // get cuepoints from config
        if (json.onCuepoint) {
            var arg = json.onCuepoint;
            self.onCuepoint.apply(self, typeof arg == 'function' ? [arg] : arg);
            delete json.onCuepoint;
        } 
        
        // get other events
        each(json, function(key, val) {
            
            if (typeof val == 'function') {
                bind(listeners, key, val);
                delete json[key];
            }
            
        });

        
        // setup common clip event callbacks for Player object too (shortcuts)
        if (index == -1) {
            player.onCuepoint = this.onCuepoint;    
        }

    };

//}}}


// {{{ Plugin
        
    var Plugin = function(name, json, player, fn) {
    
        var listeners = {};
        var self = this;   
        var hasMethods = false;
    
        if (fn) {
            extend(listeners, fn);  
        }   
        
        // custom callback functions in configuration
        each(json, function(key, val) {
            if (typeof val == 'function') {
                listeners[key] = val;
                delete json[key];   
            }
        });  
        
        // core plugin methods      
        extend(this, {
  
            // speed and fn are optional
            animate: function(props, speed, fn) { 
                if (!props) {
                    return self;    
                }
                
                if (typeof speed == 'function') { 
                    fn = speed; 
                    speed = 500;
                }
                
                if (typeof props == 'string') {
                    var key = props;
                    props = {};
                    props[key] = speed;
                    speed = 500; 
                }
                
                if (fn) {
                    var fnId = makeId();
                    listeners[fnId] = fn;
                }
        
                if (speed === undefined) { speed = 500; }
                json = player._api().fp_animate(name, props, speed, fnId);  
                return self;
            },
            
            css: function(props, val) {
                if (val !== undefined) {
                    var css = {};
                    css[props] = val;
                    props = css;                    
                }
                json = player._api().fp_css(name, props);
                extend(self, json);
                return self;
            },
            
            show: function() {
                this.display = 'block';
                player._api().fp_showPlugin(name);
                return self;
            },
            
            hide: function() {
                this.display = 'none';
                player._api().fp_hidePlugin(name);
                return self;
            },
            
            // toggle between visible / hidden state
            toggle: function() {
                this.display = player._api().fp_togglePlugin(name);
                return self;
            },          
            
            fadeTo: function(o, speed, fn) {
                
                if (typeof speed == 'function') { 
                    fn = speed; 
                    speed = 500;
                }
                
                if (fn) {
                    var fnId = makeId();
                    listeners[fnId] = fn;
                }               
                this.display = player._api().fp_fadeTo(name, o, speed, fnId);
                this.opacity = o;
                return self;
            },
            
            fadeIn: function(speed, fn) { 
                return self.fadeTo(1, speed, fn);               
            },
    
            fadeOut: function(speed, fn) {
                return self.fadeTo(0, speed, fn);   
            },
            
            getName: function() {
                return name;    
            },
            
            getPlayer: function() {
                return player;  
            },
            
            // internal method. should be made private some day
         _fireEvent: function(evt, arg, arg2) {
                
            // update plugins properties & methods
            if (evt == 'onUpdate') {
               var json = player._api().fp_getPlugin(name); 
                    if (!json) { return;    }                   
                    
               extend(self, json);
               delete self.methods;
                    
               if (!hasMethods) {
                  each(json.methods, function() {
                     var method = "" + this;       
                            
                     self[method] = function() {
                        var a = [].slice.call(arguments);
                        var ret = player._api().fp_invoke(name, method, a); 
                        return ret === 'undefined' || ret === undefined ? self : ret;
                     };
                  });
                  hasMethods = true;         
               }
            }
            
            // plugin callbacks
            var fn = listeners[evt];

                if (fn) {
                    fn.apply(self, arg);
                    
                    // "one-shot" callback
                    if (evt.substring(0, 1) == "_") {
                        delete listeners[evt];  
                    } 
            }         
         }                  
            
        });

    };


//}}}


function Player(wrapper, params, conf) {   
    
    // private variables (+ arguments)
    var 
        self = this, 
        api = null, 
        html, 
        commonClip, 
        playlist = [], 
        plugins = {},
        listeners = {},
        playerId,
        apiId,
        
        // n'th player on the page
        playerIndex,
        
        // active clip's index number
        activeIndex,
        
        swfHeight,
        wrapperHeight;  

  
// {{{ public methods 
    
    extend(self, {
            
        id: function() {
            return playerId;    
        }, 
        
        isLoaded: function() {
            return (api !== null);  
        },
        
        getParent: function() {
            return wrapper; 
        },
        
        hide: function(all) {
            if (all) { wrapper.style.height = "0px"; }
            if (api) { api.style.height = "0px"; } 
            return self;
        },

        show: function() {
            wrapper.style.height = wrapperHeight + "px";
            if (api) { api.style.height = swfHeight + "px"; }
            return self;
        }, 
                    
        isHidden: function() {
            return api && parseInt(api.style.height, 10) === 0;
        },
        
        
        load: function(fn) { 
                        
            if (!api && self._fireEvent("onBeforeLoad") !== false) {
            
                // unload all instances
                each(players, function()  {
                    this.unload();      
                });
                            
                html = wrapper.innerHTML;               
                
                // do not use splash as alternate content for flashembed
                if (html && !flashembed.isSupported(params.version)) {
                    wrapper.innerHTML = "";                 
                }                 
                
                // install Flash object inside given container
                flashembed(wrapper, params, {config: conf});
                
                // onLoad listener given as argument
                if (fn) {
                    fn.cached = true;
                    bind(listeners, "onLoad", fn);  
                }
            }
            
            return self;    
        },
        
        unload: function() {
            
            // unload only if in splash state
            if (html.replace(/\s/g,'') !== '') {
                
                if (self._fireEvent("onBeforeUnload") === false) {
                    return self;
                }   
                
                // try closing
                try {
                    if (api) { 
                        api.fp_close();
                        
                        // fire unload only 
                        self._fireEvent("onUnload");
                    }               
                } catch (error) {}              
                
                api = null;             
                wrapper.innerHTML = html;               
            } 
            
            return self;
        
        },
        
        getClip: function(index) {
            if (index === undefined) {
                index = activeIndex;    
            }
            return playlist[index];
        },
        
        
        getCommonClip: function() {
            return commonClip;  
        },      
        
        getPlaylist: function() {
            return playlist; 
        },
        
      getPlugin: function(name) {  
         var plugin = plugins[name];
         
            // create plugin if nessessary
         if (!plugin && self.isLoaded()) {
                var json = self._api().fp_getPlugin(name);
                if (json) {
                    plugin = new Plugin(name, json, self);
                    plugins[name] = plugin;                         
                } 
         }        
         return plugin; 
      },
        
        getScreen: function() { 
            return self.getPlugin("screen");
        }, 
        
        getControls: function() { 
            return self.getPlugin("controls");
        }, 

        getConfig: function(copy) { 
            return copy ? clone(conf) : conf;
        },
        
        getFlashParams: function() { 
            return params;
        },      
        
        loadPlugin: function(name, url, props, fn) { 

            // properties not supplied          
            if (typeof props == 'function') { 
                fn = props; 
                props = {};
            } 
            
            // if fn not given, make a fake id so that plugin's onUpdate get's fired
            var fnId = fn ? makeId() : "_"; 
            self._api().fp_loadPlugin(name, url, props, fnId); 
            
            // create new plugin
            var arg = {};
            arg[fnId] = fn;
            var p = new Plugin(name, null, self, arg);
            plugins[name] = p;
            return p;           
        },
        
        
        getState: function() {
            return api ? api.fp_getState() : -1;
        },
        
        // "lazy" play
        play: function(clip, instream) {
            
            function play() {
                if (clip !== undefined) {
                    self._api().fp_play(clip, instream);
                } else {
                    self._api().fp_play();  
                }
            }
            
            if (api) {
                play();
                
            } else {
                self.load(function() { 
                    play();
                });
            }
            
            return self;
        },
        
        getVersion: function() {
            var js = "flowplayer.js 3.1.4";
            if (api) {
                var ver = api.fp_getVersion();
                ver.push(js);
                return ver;
            }
            return js; 
        },
        
        _api: function() {
            if (!api) {
                throw "Flowplayer " +self.id()+ " not loaded when calling an API method";
            }
            return api;             
        },
        
        setClip: function(clip) {
            self.setPlaylist([clip]);
            return self;
        },
        
        getIndex: function() {
            return playerIndex; 
        }
        
    }); 
    
    
    // event handlers
    each(("Click*,Load*,Unload*,Keypress*,Volume*,Mute*,Unmute*,PlaylistReplace,ClipAdd,Fullscreen*,FullscreenExit,Error,MouseOver,MouseOut").split(","),
        function() {         
            var name = "on" + this;
            
            // before event
            if (name.indexOf("*") != -1) {
                name = name.substring(0, name.length -1); 
                var name2 = "onBefore" + name.substring(2);
                self[name2] = function(fn) {
                    bind(listeners, name2, fn); 
                    return self;
                };                      
            }
            
            // normal event
            self[name] = function(fn) {
                bind(listeners, name, fn);  
                return self;
            };           
        }
    ); 
    
    
    // core API methods
    each(("pause,resume,mute,unmute,stop,toggle,seek,getStatus,getVolume,setVolume,getTime,isPaused,isPlaying,startBuffering,stopBuffering,isFullscreen,toggleFullscreen,reset,close,setPlaylist,addClip,playFeed").split(","),     
        function() {         
            var name = this;
            
            self[name] = function(a1, a2) {
                if (!api) { return self; }
                var ret = null;
                
                // two arguments
                if (a1 !== undefined && a2 !== undefined) { 
                    ret = api["fp_" + name](a1, a2);
                    
                } else { 
                    ret = (a1 === undefined) ? api["fp_" + name]() : api["fp_" + name](a1);
                }
                
                return ret === 'undefined' || ret === undefined ? self : ret;
            };           
        }
    );      
    
//}}}


// {{{ public method: _fireEvent
        
    self._fireEvent = function(a) {     
        
        if (typeof a == 'string') { a = [a]; }
        
        var evt = a[0], arg0 = a[1], arg1 = a[2], arg2 = a[3], i = 0;       
        
        if (conf.debug) { log(a); }             
        
        // internal onLoad
        if (!api && evt == 'onLoad' && arg0 == 'player') {                      
            
            api = api || el(apiId); 
            swfHeight = api.clientHeight;
            
            each(playlist, function() {
                this._fireEvent("onLoad");      
            });
            
            each(plugins, function(name, p) {
                p._fireEvent("onUpdate");       
            }); 
            
            commonClip._fireEvent("onLoad");  
        }
        
        // other onLoad events are skipped
        if (evt == 'onLoad' && arg0 != 'player') { return; }
        
        
        // "normalize" error handling
        if (evt == 'onError') { 
            if (typeof arg0 == 'string' || (typeof arg0 == 'number' && typeof arg1 == 'number'))  {
                arg0 = arg1;
                arg1 = arg2;
            }            
        }
        
        
      if (evt == 'onContextMenu') {
         each(conf.contextMenu[arg0], function(key, fn)  {
            fn.call(self);
         });
         return;
      }

        if (evt == 'onPluginEvent') { 
            var name = arg0.name || arg0;
            var p = plugins[name];

            if (p) {
                p._fireEvent("onUpdate", arg0);
                p._fireEvent(arg1, a.slice(3));     
            }
            return;
        }       

        // replace whole playlist
        if (evt == 'onPlaylistReplace') {
            playlist = [];
            var index = 0;
            each(arg0, function() {
                playlist.push(new Clip(this, index++, self));
            });     
        }
        
        // insert new clip to the playlist. arg0 = clip, arg1 = index 
        if (evt == 'onClipAdd') {
            
            // instream clip additions are ignored at this point
            if (arg0.isInStream) { return; }
            
            // add new clip into playlist           
            arg0 = new Clip(arg0, arg1, self);
            playlist.splice(arg1, 0, arg0);
            
            // increment index variable for the rest of the clips on playlist 
            for (i = arg1 + 1; i < playlist.length; i++) {
                playlist[i].index++;    
            }
        }
        
        
        var ret = true;
        
        // clip event
        if (typeof arg0 == 'number' && arg0 < playlist.length) {
            
            activeIndex = arg0;
            var clip = playlist[arg0];          
            
            if (clip) {
                ret = clip._fireEvent(evt, arg1, arg2); 
            } 
            
            if (!clip || ret !== false) {

                // clip argument is given for common clip, because it behaves as the target
                ret = commonClip._fireEvent(evt, arg1, arg2, clip); 
            }  
        }
        
        
        // trigger player event
        each(listeners[evt], function() {
            ret = this.call(self, arg0, arg1);      
            
            // remove cached entry
            if (this.cached) {
                listeners[evt].splice(i, 1);    
            }
            
            // break loop
            if (ret === false) { return false;   }
            i++;
            
        }); 

        return ret;
    };

//}}}
 

// {{{ init
    
   function init() {
        
        // replace previous installation 
        if ($f(wrapper)) {
            $f(wrapper).getParent().innerHTML = ""; 
            playerIndex = $f(wrapper).getIndex();
            players[playerIndex] = self;
            
        // register this player into global array of instances
        } else {
            players.push(self);
            playerIndex = players.length -1;
        }
        
        wrapperHeight = parseInt(wrapper.style.height, 10) || wrapper.clientHeight;     
        
        // flashembed parameters
        if (typeof params == 'string') {
            params = {src: params}; 
        }    
        
        // playerId 
        playerId = wrapper.id || "fp" + makeId();
        apiId = params.id || playerId + "_api";
        params.id = apiId;
        conf.playerId = playerId;
        

        // plain url is given as config
        if (typeof conf == 'string') {
            conf = {clip:{url:conf}};   
        } 
        
        if (typeof conf.clip == 'string') {
            conf.clip = {url: conf.clip};   
        }
        
        // common clip is always there
        conf.clip = conf.clip || {};  
        
        
        // wrapper href as common clip's url
        if (wrapper.getAttribute("href", 2) && !conf.clip.url) { 
            conf.clip.url = wrapper.getAttribute("href", 2);            
        } 
        
        commonClip = new Clip(conf.clip, -1, self); 
        
        // playlist
        conf.playlist = conf.playlist || [conf.clip]; 
        
        var index = 0;
        
        each(conf.playlist, function() {
            
            var clip = this;            
            
            /* sometimes clip is given as array. this is not accepted. */
            if (typeof clip == 'object' && clip.length) {
                clip = {url: "" + clip};    
            }           
            
            // populate common clip properties to each clip
            each(conf.clip, function(key, val) {
                if (val !== undefined && clip[key] === undefined && typeof val != 'function') {
                    clip[key] = val;    
                }
            }); 
            
            // modify playlist in configuration
            conf.playlist[index] = clip;            
            
            // populate playlist array
            clip = new Clip(clip, index, self);
            playlist.push(clip);                        
            index++;            
        });             
        
        // event listeners
        each(conf, function(key, val) {
            if (typeof val == 'function') {
                
                // common clip event
                if (commonClip[key]) {
                    commonClip[key](val);
                    
                // player event
                } else {
                    bind(listeners, key, val);  
                }               
                
                // no need to supply for the Flash component
                delete conf[key];   
            }
        });      
        
        
        // plugins
        each(conf.plugins, function(name, val) {
            if (val) {
                plugins[name] = new Plugin(name, val, self);
            }
        });
        
        
        // setup controlbar plugin if not explicitly defined
        if (!conf.plugins || conf.plugins.controls === undefined) {
            plugins.controls = new Plugin("controls", null, self);  
        }
        
        // setup canvas as plugin
        plugins.canvas = new Plugin("canvas", null, self);
        
        
        // Flowplayer uses black background by default
        params.bgcolor = params.bgcolor || "#000000";
        
        
        // setup default settings for express install
        params.version = params.version || [9, 0];      
        params.expressInstall = 'http://www.flowplayer.org/swf/expressinstall.swf';
        
        
        // click function
        function doClick(e) { 
            if (!self.isLoaded() && self._fireEvent("onBeforeClick") !== false) {
                self.load();        
            } 
            return stopEvent(e);                    
        }
        
        // defer loading upon click
        html = wrapper.innerHTML;
        if (html.replace(/\s/g, '') !== '') {    
            
            if (wrapper.addEventListener) {
                wrapper.addEventListener("click", doClick, false);  
                
            } else if (wrapper.attachEvent) {
                wrapper.attachEvent("onclick", doClick);    
            }
            
        // player is loaded upon page load 
        } else {
            
            // prevent default action from wrapper. (fixes safari problems)
            if (wrapper.addEventListener) {
                wrapper.addEventListener("click", stopEvent, false);    
            }
            
            // load player
            self.load();
        }
    }

    // possibly defer initialization until DOM get's loaded
    if (typeof wrapper == 'string') { 
        flashembed.domReady(function() {
            var node = el(wrapper); 
            
            if (!node) {
                throw "Flowplayer cannot access element: " + wrapper;   
            } else {
                wrapper = node; 
                init();                 
            } 
        });
        
    // we have a DOM element so page is already loaded
    } else {        
        init();
    }
    
    
//}}}


}


// {{{ flowplayer() & statics 

// container for player instances
var players = [];


// this object is returned when multiple player's are requested 
function Iterator(arr) {
    
    this.length = arr.length;
    
    this.each = function(fn)  {
        each(arr, fn);  
    };
    
    this.size = function() {
        return arr.length;  
    };  
}

// these two variables are the only global variables
window.flowplayer = window.$f = function() {

    var instance = null;
    var arg = arguments[0]; 
    
    // $f()
    if (!arguments.length) {
        each(players, function() {
            if (this.isLoaded())  {
                instance = this;    
                return false;
            }
        });
        
        return instance || players[0];
    } 
    
    if (arguments.length == 1) {
        
        // $f(index);
        if (typeof arg == 'number') { 
            return players[arg];    
    
            
        // $f(wrapper || 'containerId' || '*');
        } else {
            
            // $f("*");
            if (arg == '*') {
                return new Iterator(players);   
            }
            
            // $f(wrapper || 'containerId');
            each(players, function() {
                if (this.id() == arg.id || this.id() == arg || this.getParent() == arg)  {
                    instance = this;    
                    return false;
                }
            });
            
            return instance;                    
        }
    }           

    // instance builder 
    if (arguments.length > 1) {     

        var swf = arguments[1];
        var conf = (arguments.length == 3) ? arguments[2] : {};
                        
        if (typeof arg == 'string') {
            
            // select arg by classname
            if (arg.indexOf(".") != -1) {
                var instances = [];
                
                each(select(arg), function() { 
                    instances.push(new Player(this, clone(swf), clone(conf)));      
                }); 
                
                return new Iterator(instances);
                
            // select node by id
            } else {        
                var node = el(arg);
                return new Player(node !== null ? node : arg, swf, conf);   
            } 
            
            
        // arg is a DOM element
        } else if (arg) {
            return new Player(arg, swf, conf);                      
        }
        
    } 
    
    return null; 
};
    
extend(window.$f, {

    // called by Flash External Interface       
    fireEvent: function() {
        var a = [].slice.call(arguments);
        var p = $f(a[0]);       
        return p ? p._fireEvent(a.slice(1)) : null;
    },
    
    
    // create plugins by modifying Player's prototype
    addPlugin: function(name, fn) {
        Player.prototype[name] = fn;
        return $f;
    },
    
    // utility methods for plugin developers
    each: each,
    
    extend: extend
    
});


/* sometimes IE leaves sockets open (href="javascript:..." links break this)
if (document.all) {
    window.onbeforeunload = function(e) { 
        $f("*").each(function() {
            if (this.isLoaded()) {
                this.close();   
            }
        });
    };  
}
*/

    
//}}}


//{{{ jQuery support

if (typeof jQuery == 'function') {
    
    jQuery.prototype.flowplayer = function(params, conf) {  
        
        // select instances
        if (!arguments.length || typeof arguments[0] == 'number') {
            var arr = [];
            this.each(function()  {
                var p = $f(this);
                if (p) {
                    arr.push(p);    
                }
            });
            return arguments.length ? arr[arguments[0]] : new Iterator(arr);
        }
        
        // create flowplayer instances
        return this.each(function() { 
            $f(this, clone(params), conf ? clone(conf) : {});   
        }); 
        
    };
    
}

//}}}


})();
/**
 * tools.flashembed 1.0.4 - The future of Flash embedding.
 * 
 * Copyright (c) 2009 Tero Piirainen
 * http://flowplayer.org/tools/flash-embed.html
 *
 * Dual licensed under MIT and GPL 2+ licenses
 * http://www.opensource.org/licenses
 *
 * Launch  : March 2008
 * Date: ${date}
 * Revision: ${revision} 
 */ 
(function() {  
        
//{{{ utility functions 
        
var jQ = typeof jQuery == 'function';

var options = {
    
    // very common opts
    width: '100%',
    height: '100%',     
    
    // flashembed defaults
    allowfullscreen: true,
    allowscriptaccess: 'always',
    quality: 'high',    
    
    // flashembed specific options
    version: null,
    onFail: null,
    expressInstall: null, 
    w3c: false,
    cachebusting: false 
};

if (jQ) {
        
    // tools version number
    jQuery.tools = jQuery.tools || {};
    
    jQuery.tools.flashembed = { 
        version: '1.0.4', 
        conf: options
    };      
}


// from "Pro JavaScript techniques" by John Resig
function isDomReady() {
    
    if (domReady.done)  { return false; }
    
    var d = document;
    if (d && d.getElementsByTagName && d.getElementById && d.body) {
        clearInterval(domReady.timer);
        domReady.timer = null;
        
        for (var i = 0; i < domReady.ready.length; i++) {
            domReady.ready[i].call();   
        }
        
        domReady.ready = null;
        domReady.done = true;
    } 
}

// if jQuery is present, use it's more effective domReady method
var domReady = jQ ? jQuery : function(f) {
    
    if (domReady.done) {
        return f(); 
    }
    
    if (domReady.timer) {
        domReady.ready.push(f); 
        
    } else {
        domReady.ready = [f];
        domReady.timer = setInterval(isDomReady, 13);
    } 
};  


// override extend opts function 
function extend(to, from) {
    if (from) {
        for (key in from) {
            if (from.hasOwnProperty(key)) {
                to[key] = from[key];
            }
        }
    }
    
    return to;
}   


// JSON.asString() function
function asString(obj) {
     
    switch (typeOf(obj)){
        case 'string':
            obj = obj.replace(new RegExp('(["\\\\])', 'g'), '\\$1');
            
            // flash does not handle %- characters well. transforms "50%" to "50pct" (a dirty hack, I admit)
            obj = obj.replace(/^\s?(\d+)%/, "$1pct");
            return '"' +obj+ '"';
            
        case 'array':
            return '['+ map(obj, function(el) {
                return asString(el);
            }).join(',') +']'; 
            
        case 'function':
            return '"function()"';
            
        case 'object':
            var str = [];
            for (var prop in obj) {
                if (obj.hasOwnProperty(prop)) {
                    str.push('"'+prop+'":'+ asString(obj[prop]));
                }
            }
            return '{'+str.join(',')+'}';
    }
    
    // replace ' --> "  and remove spaces
    return String(obj).replace(/\s/g, " ").replace(/\'/g, "\"");
}


// private functions
function typeOf(obj) {
    if (obj === null || obj === undefined) { return false; }
    var type = typeof obj;
    return (type == 'object' && obj.push) ? 'array' : type;
}


// version 9 bugfix: (http://blog.deconcept.com/2006/07/28/swfobject-143-released/)
if (window.attachEvent) {
    window.attachEvent("onbeforeunload", function() {
        __flash_unloadHandler = function() {};
        __flash_savedUnloadHandler = function() {};
    });
}

function map(arr, func) {
    var newArr = []; 
    for (var i in arr) {
        if (arr.hasOwnProperty(i)) {
            newArr[i] = func(arr[i]);
        }
    }
    return newArr;
}
    
function getHTML(p, c) {
        
    var e = extend({}, p);   
    var ie = document.all;  
    var html = '<object width="' +e.width+ '" height="' +e.height+ '"';
    
    // force id for IE or Flash API cannot be returned
    if (ie && !e.id) {
        e.id = "_" + ("" + Math.random()).substring(9);
    }
    
    if (e.id) { 
        html += ' id="' + e.id + '"';   
    }
    
    // prevent possible caching problems
    if (e.cachebusting) {
        e.src += ((e.src.indexOf("?") != -1 ? "&" : "?") + Math.random());      
    }           
    
    if (e.w3c || !ie) {
        html += ' data="' +e.src+ '" type="application/x-shockwave-flash"';     
    } else {
        html += ' classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"';    
    }
    
    html += '>'; 
    
    if (e.w3c || ie) {
        html += '<param name="movie" value="' +e.src+ '" />';   
    }

    // parameters
    e.width = e.height = e.id = e.w3c = e.src = null;
    
    for (var k in e) {
        if (e[k] !== null) {
            html += '<param name="'+ k +'" value="'+ e[k] +'" />';
        }
    }   

    // flashvars
    var vars = "";
    
    if (c) {
        for (var key in c) {
            if (c[key] !== null) {
                vars += key +'='+ (typeof c[key] == 'object' ? asString(c[key]) : c[key]) + '&';
            }
        }
        vars = vars.substring(0, vars.length -1);
        html += '<param name="flashvars" value=\'' + vars + '\' />';
    }
    
    html += "</object>";    
    
    return html;

}

//}}}


function Flash(root, opts, flashvars) {
    
    var version = flashembed.getVersion(); 
    
    // API methods for callback
    extend(this, {
            
        getContainer: function() {
            return root;    
        },
        
        getConf: function() {
            return opts;    
        },
    
        getVersion: function() {
            return version; 
        },  
        
        getFlashvars: function() {
            return flashvars;   
        }, 
        
        getApi: function() {
            return root.firstChild; 
        }, 
        
        getHTML: function() {
            return getHTML(opts, flashvars);    
        }
        
    });

    // variables    
    var required = opts.version; 
    var express = opts.expressInstall;
    
    
    // everything ok -> generate OBJECT tag 
    var ok = !required || flashembed.isSupported(required);
    
    if (ok) {
        opts.onFail = opts.version = opts.expressInstall = null;
        root.innerHTML = getHTML(opts, flashvars);
        
    // fail #1. express install
    } else if (required && express && flashembed.isSupported([6,65])) {
        
        extend(opts, {src: express});
        
        flashvars = {
            MMredirectURL: location.href,
            MMplayerType: 'PlugIn',
            MMdoctitle: document.title
        };
        
        root.innerHTML = getHTML(opts, flashvars);  
        
    // fail #2. 
    } else { 
    
        // fail #2.1 custom content inside container
        if (root.innerHTML.replace(/\s/g, '') !== '') {
            // minor bug fixed here 08.04.2008 (thanks JRodman)         
        
        // fail #2.2 default content
        } else {            
            root.innerHTML = 
                "<h2>Flash version " + required + " or greater is required</h2>" + 
                "<h3>" + 
                    (version[0] > 0 ? "Your version is " + version : "You have no flash plugin installed") +
                "</h3>" + 
                
                (root.tagName == 'A' ? "<p>Click here to download latest version</p>" : 
                    "<p>Download latest version from <a href='http://www.adobe.com/go/getflashplayer'>here</a></p>");
                
            if (root.tagName == 'A') {  
                root.onclick = function() {
                    location.href= 'http://www.adobe.com/go/getflashplayer';
                };
            }               
        }
    }
    
    // onFail
    if (!ok && opts.onFail) {
        var ret = opts.onFail.call(this);
        if (typeof ret == 'string') { root.innerHTML = ret; }   
    }
    
    // http://flowplayer.org/forum/8/18186#post-18593
    if (document.all) {
        window[opts.id] = document.getElementById(opts.id);
    } 
    
}

window.flashembed = function(root, conf, flashvars) {   
    
//{{{ construction
    
    // root must be found / loaded  
    if (typeof root == 'string') {
        var el = document.getElementById(root);
        if (el) {
            root = el;  
        } else {
            domReady(function() {
                flashembed(root, conf, flashvars);
            });
            return;         
        } 
    }
    
    // not found
    if (!root) { return; }
    
    if (typeof conf == 'string') {
        conf = {src: conf}; 
    }
    
    var opts = extend({}, options);
    extend(opts, conf);     
    
    return new Flash(root, opts, flashvars);
    
//}}}
    
    
};


//{{{ static methods

extend(window.flashembed, {

    // returns arr[major, fix]
    getVersion: function() {
    
        var version = [0, 0];
        
        if (navigator.plugins && typeof navigator.plugins["Shockwave Flash"] == "object") {
            var _d = navigator.plugins["Shockwave Flash"].description;
            if (typeof _d != "undefined") {
                _d = _d.replace(/^.*\s+(\S+\s+\S+$)/, "$1");
                var _m = parseInt(_d.replace(/^(.*)\..*$/, "$1"), 10);
                var _r = /r/.test(_d) ? parseInt(_d.replace(/^.*r(.*)$/, "$1"), 10) : 0;
                version = [_m, _r];
            }
            
        } else if (window.ActiveXObject) {

            try { // avoid fp 6 crashes
                var _a = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7");
                
            } catch(e) {
                
                try { 
                    _a = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6");
                    version = [6, 0];
                    _a.AllowScriptAccess = "always"; // throws if fp < 6.47 
                    
                } catch(ee) {
                    if (version[0] == 6) { return version; }
                }
                try {
                    _a = new ActiveXObject("ShockwaveFlash.ShockwaveFlash");
                } catch(eee) {
                
                }
                
            }
            
            if (typeof _a == "object") {
                _d = _a.GetVariable("$version"); // bugs in fp 6.21 / 6.23
                if (typeof _d != "undefined") {
                    _d = _d.replace(/^\S+\s+(.*)$/, "$1").split(",");
                    version = [parseInt(_d[0], 10), parseInt(_d[2], 10)];
                }
            }
        } 
        
        return version;
    },
    
    isSupported: function(version) {
        var now = flashembed.getVersion();
        var ret = (now[0] > version[0]) || (now[0] == version[0] && now[1] >= version[1]);          
        return ret;
    },
    
    domReady: domReady,
    
    // returns a String representation from JSON object 
    asString: asString,
    
    
    getHTML: getHTML
    
});

//}}}


// setup jquery support
if (jQ) {
    
    jQuery.fn.flashembed = function(conf, flashvars) {
        
        var el = null;
        
        this.each(function() { 
            el = flashembed(this, conf, flashvars);
        });
        
        return conf.api === false ? this : el;      
    };

}

})();