/*
 *  Rich in place editor using FCKEditor and Prototype Window
 *  
 *  This script requiers FCKEditor 3.2.1+, and 
 *  Prototype 1.6.0.3+ + Prototype Window to be loaded
 *  
 *  The editable containers should be made relative with a specified
 *  width or height, or the overlay may not display properly.
 *  
 *  @author Yanick Rochon
 *  @version 1.0
 */

var CMSEditor = (new function() {
	// private
	var _ready = false;
	var _initialized = false;
	var _saving = false;
	
	var _currentElement = null;
	var _currentKey = null;
	
	var _editor = null;
	var _editorId = null;
	var _editorOptions = {};
	
	var _window = {};
	Prototype.onReady(function() {
		
		_window = new Window({
			id: "edWindow",
			title: CMSEditorTranslation.windowTitle + (_editorOptions.EditorConfig.DefaultLanguage ? ' - ' + _editorOptions.EditorConfig.DefaultLanguage : ''),
			className: "alphacube",
			maximizable:false,
			minimizable:true,
			resizable:false,
			width: 660,
			height: 310
		});
		
		_window.setCloseCallback( function() { _hideEditor(); } );

		_ready = true;
		if ( _editorId ) {
			_init();
		}
	});
	
	var _init = function() {
		if ( _initialized ) return;

		_editor = new FCKeditor(_editorId);
		_editor.BasePath = _editorOptions.EditorPath;
		_editor.ToolbarSet = _editorOptions.ToolbarSet;
		Object.extend(_editor.Config, _editorOptions.EditorConfig);
		_editor.Width = '100%' ;
		_editor.Height = '300' ;
		_editor.Config.OnPreview = "CMSEditor.preview()";
		_editor.ReplaceTextarea();
		
		var container = $$(".inPlaceEditor").first();
		container.insert({top:'<div id="editorStatus" class="status" style="display:none;">Test</div>'});
		
		_window.setContent( container );
		
		// prepare editable containers
		$$(".editable").each(function(el) {
			var key = Element.readAttribute(el,"rel");
			if ( key ) {
				var id = 'editable_'+ key.replace(/\./g, "-").camelize();
				el.innerHTML = 
					  '<div class="button editLink" onclick="CMSEditor.editContent(\''+key+'\');"><img class="edit" src="' + _editorOptions.BasePath + 'images/edit.png" alt="Edit" onmouseover="$(this).setOpacity(1);" onmouseout="$(this).setOpacity(.7);" /></div>'
					+ '<div class="button delLink" onclick="CMSEditor.deleteContent(\''+key+'\');"><img class="del" src="' + _editorOptions.BasePath + 'images/eraser.png" alt="Delete" onmouseover="$(this).setOpacity(1);" onmouseout="$(this).setOpacity(.7);" /></div>'
					+ '<div id="' + id + '">' + el.innerHTML + '</div>';
				
			} else {
				alert( "CMSEditor: " + CMSEditorTranslation.errorNoRelAttribute );
			}
		});
		
		// execute later...
		(function() {
			$$("img.edit").each(function(el) {
				el.setOpacity(.7);
			});
		}).defer();
	};
	
	var _originalContent = null;
	var _updateContent = function(reset) {
		var ed = FCKeditorAPI.GetInstance(_editor.InstanceName);
		var content = ed.GetHTML(true);

		if ( reset ) {
			_originalContent = content;
			ed.ResetIsDirty();
		}
		
		_currentElement.update(content);
	};
		
	var _hideEditor = function() {
		if ( _saving ) return false;
		
		var ed = FCKeditorAPI.GetInstance(_editor.InstanceName);
		if ( ed.IsDirty() ) {
			if ( !confirm( unescape( CMSEditorTranslation.editCancel ) ) ) {
				return false;
			}
			
			// restore if anything...
			if (_originalContent) _currentElement.update(_originalContent); 
		}
		
		if ( _currentElement ) {
			_currentElement.removeClassName("editSourceContainer");
			_window.hide();
			
			_currentElement = null;
			_currentKey = null;
		}
		
		_updateStatus(null);  // null = hide
		
		return true;
	};
	
	var _statusTimeout = null;
	var _updateStatus = function(msg) {
		if ( _statusTimeout ) {
			window.clearTimeout(_statusTimeout);
		}
		var status = $('editorStatus');
		if (msg) {
			_statusTimeout = status.show().update(msg).hide.bind(status).delay(60);
		} else {
			status.hide();
		}
	};
	
	// public
	
	this.init = function(id,options) {
		_editorId = id;
		_editorOptions = options;
		
		if ( _ready ) {
			_init();
		}
	};
	
	this.editContent = function(key) {
		if ( _currentElement ) {
			_hideEditor();
		}

		var id = 'editable_'+ key.replace(/\./g, "-").camelize();
		_currentElement = $(id);
		if ( _currentElement ) {
			
			_currentKey = key;
			
			_currentElement.addClassName("editSourceContainer");
			_window.showCenter(true, null);
			if ( _window.isMinimized() ) {
				_window.minimize();
			}

			var ed = FCKeditorAPI.GetInstance(_editor.InstanceName);
			_originalContent = _currentElement.innerHTML;  // _updateContent
			ed.SetHTML(_currentElement.innerHTML);
			
			(function() { ed.Focus(); ed.ResetIsDirty(); }).delay(0.5);
		}
	};
	
	this.deleteContent = function(key) {
		if ( _editorOptions.SavePath ) {
			if ( !confirm( CMSEditorTranslation.deleteConfirm ) )
				return;

			new Ajax.Request(_editorOptions.SavePath, {
				method: 'post',
				parameters: {
					'key': key,
					'delete': true,
					'lang': _editorOptions.EditorConfig.DefaultLanguage || ''
				},
				onFailure: function(transport) {
					alert( unescape( CMSEditorTranslation.deleteError + transport.status) );
					_saving = false;
				},
				onSuccess: function(transport) {
					if ( !transport.responseText.blank() ) {
						alert( transport.responseText );
						_saving = false;
					} else {
            alert( CMSEditorTranslation.deleteSuccess );
						window.document.location.reload();
					}
				}
			});

		}
	};
	
	this.preview = function() {
		_updateContent(false);   // do not reset old content
	};

	this.saveContent = function() {
		var ed = FCKeditorAPI.GetInstance(_editor.InstanceName);
		var content = ed.GetHTML(true);
		
		if ( _editorOptions.SavePath ) {
			if ( !confirm( CMSEditorTranslation.saveConfirm ) )
				return;
			
			_saving = true;
			
			new Ajax.Request(_editorOptions.SavePath, {
				method: 'post',
				parameters: {
					'key': _currentKey,
					'content': escape(content),
					'lang': _editorOptions.EditorConfig.DefaultLanguage || ''
				},
				onFailure: function(transport) {
					alert( unescape( CMSEditorTranslation.saveError + transport.status) );
					
					_saving = false;
				},
				onSuccess: function(transport) {
					if ( _window.visible ) {
						_updateStatus(transport.responseText);
					} else {
						alert( transport.responseText );
					}
					_updateContent.defer(true);  // reset old data
					
					_saving = false;
				}
			});
		}
	};
			
	this.isEditing = function() {
		return _currentElement != null;
	}
	
});

//called when FCKeditor is done starting..
function FCKeditor_OnComplete( editorInstance ){
	// replace Save function
	editorInstance.EditorWindow.parent.FCK.Commands
		.GetCommand("Save").Execute = function() { CMSEditor.saveContent(); };

	// replace Preview function
	editorInstance.EditorWindow.parent.FCK.Preview = function() {
		CMSEditor.preview();
	};
}

