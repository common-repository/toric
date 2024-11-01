var toric_current_request=null;

function toric_ajax(value) {
   
    toric_current_request=jQuery.ajax({
        type: "post",
        url: toric.ajax_url, // admin-ajax.php path,
        beforeSend:function(){
            if(null != toric_current_request){
                toric_current_request.abort();
            }
        },
        data: {
            action: toric.action, // action
            nonce: toric.nonce,   // pass the nonce here
            value:value
        },
        success: function (data) {
            var toric_preview_div=document.getElementById("toric-preview-meta-box-preview");
            toric_preview_div.innerHTML=data.trim();
        },
        error: function (request, textStatus,errorThrown) {
            if('abort'==textStatus){
                return;
            }
            var toric_preview_div=document.getElementById("toric-preview-meta-box-preview");
            toric_preview_div.innerHTML='<div class="toric preview-error">'+toric.code_retrieval_error_message+'</div>';
        }
    });
}

//toric_ajax('test value 2');// call the function.