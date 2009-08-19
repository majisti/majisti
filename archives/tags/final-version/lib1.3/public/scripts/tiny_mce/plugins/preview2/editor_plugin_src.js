/**
 * $Id: editor_plugin_src.js 895 2009-04-17 09:27:23Z spocke $
 *
 * @author Yanick Rochon
 * @copyright Copyright © 2009, Majisti Technologies, All rights reserved.
 */

(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('preview2');

	tinymce.create('tinymce.plugins.Preview2', {
		init : function(ed, url) {
			var options = ed.settings.preview2_options;
			
			var element = options.source_element;
			var oldContent = element ? element.innerHTML : '';
			var showCallback = options.onPreview;
			
			if ( typeof showCallback == 'string' ) {
        showCallback = tinymce.resolve(showCallback);
			}

			ed.addCommand('mcePreview2', function() {
        if ( element ) {
          element.innerHTML = ed.getContent();
          if ( showCallback ) {
            showCallback(ed);
          }
        } else {
          alert(ed.getLang('preview2.undefined_element'));
        }
			});
			
			ed.addCommand('mcePreview2_cancel', function() {
        if ( element ) {
          element.innerHTML = oldContent;
        } else {
          alert(ed.getLang('preview2.undefined_element'));
        }
			});

			// Register example button
			ed.addButton('preview2', {
				title : 'preview2.desc',
				cmd : 'mcePreview2',
				image : url + '/img/preview2.gif'
			});
		},

		getInfo : function() {
			return {
				longname : 'Preview2',
				author : 'Yanick Rochon',
				authorurl : 'http://www.majisti.com',
				infourl : 'http://',
				version : "0.1"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('preview2', tinymce.plugins.Preview2);
})();