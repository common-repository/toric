/**
 * Function to copy text from an element to clipboard.
 * @param {*} element_to_copy_text_id the ID of the element with text to be copied.
 * @param {*} element_to_show_success_id useful in tooltips.
 * @param {*} success_message success message to be shown on success. Inject text copied using %s. e.g 'Copied: %s'
 */
 function toricCopyToClipboard(element_to_copy_text_id,element_to_show_success_id, success_message) {
  var element = document.getElementById(element_to_copy_text_id);
  var element_type = element.tagName; // Obtain html element type.
  var copied_text="";
  if("P" == element_type){
      copied_text = element.innerHTML;// Obtain inner text if it is a P tag.
  }else if("INPUT" == element_type){
      element.select();
      element.setSelectionRange(0, 99999);
      copied_text = element.value;
  }
  navigator.clipboard.writeText(copied_text);// Add the text to the clipboard.
  showTooltipOnCopyToClipboardButton(element_to_show_success_id,sprintf(success_message,copied_text));
}

function showTooltipOnCopyToClipboardButton(element_id, message){
  var tooltip = document.getElementById(element_id);
  tooltip.innerHTML = message;
}



/**
* Function to mimic sprintf.
* 
* sprintf('My %s text', 'first') outputs=> 'My first text'
* 
* @param {*} str 
* @returns string
*/
function sprintf(str) {
  var args = arguments,
  flag = true,
  i = 1;

  str = str.replace(/%s/g, function() {
      var arg = args[i++];

      if (typeof arg === 'undefined') {
          flag = false;
          return '';
      }
      return arg;
  });
  return flag ? str : '';
};