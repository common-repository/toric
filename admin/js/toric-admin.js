(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

})( jQuery );



window.addEventListener("load" ,function(){
    //toricLivePreview();
});

function showSpinnerPreloader(){
    var toric_preview_div=document.getElementById("toric-preview-meta-box-preview");
    toric_preview_div.innerHTML='<div class="toric spinner-container"><div id="spinner-preloader"></div></div>';
}

function toricContentOnInput(value){
    showSpinnerPreloader();
    toric_ajax(value);// call the function.
}


/*function toricLivePreview(){
    var toric_content_input=document.getElementById("toric-content-meta-box-content");
    if(null !== toric_content_input){
        toric_content_input.onchange=toricContentOnchange;
    }
}*/
