/**
 * Prototype extension (Prototype 1.6.0.x) 
 * by Yanick Rochon
 * version 1.1
 * 
 * last modified 2009-02-18
 */

/**
 * Prototype.onReady( f );
 * 
 * Call the onReady function on functions that require the DOM to be loaded
 * prior to be executed. The function will invoke only when the DOM is fully
 * loaded. If Prototype.onReady  is called faster the DOM is loaded, the function
 * will be executed immediately.
 * 
 * @param f some given function to be executed only when the DOM is fully loaded
 * @return boolean true if the document is loaded (the function was executed immediatly)
 *                 false the function was pushed to a call stack waiting for the DOm to 
 *                 finish loading
 */
Prototype.onReady = (function() {
	var __DOMreadyCallbacks = [];
	document.observe("dom:loaded", function() {
		__DOMreadyCallbacks.each(function(f){f()});
		__DOMreadyCallbacks = null;
	});
	return function(f) {
		if ( document.loaded ) {
			f();
		} else {
			__DOMreadyCallbacks.push(f);
		}
		return document.loaded;
	};
})();
