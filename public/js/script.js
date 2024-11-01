jQuery(document).ready(function(){

    jQuery('.dswcf7_digital_sig').each( function(){
        var current = jQuery(this);
        var digital_sig_name = current.find(".digital_sig_canvas").attr("name");
        var digital_sig_pad = new SignaturePad(document.getElementById("digital_sig_canvas_"+digital_sig_name));
        jQuery(document).on('touchstart touchend click', "#digital_sig_canvas_"+digital_sig_name, function(event){
            if(event.handled === false) return
            event.stopPropagation();
            event.handled = true;
            var dsimgdata = digital_sig_pad.toDataURL('image/png');
            jQuery("input[name="+digital_sig_name+"]").val(dsimgdata);
        });

        jQuery(current).find(".dswcf7-sign").click(function(e){
            e.preventDefault();
            digital_sig_pad.clear();
            jQuery("input[name="+digital_sig_name+"]").val('');
        });    
    });
});