jQuery(document).ready(function(){

    var multiple = 1;
    jQuery('.dswcf7_digital_sig').each( function(){
        if(multiple == 1){
            var current = jQuery(this);
            var pad_backcolor = current.find(".sfwcf7_canvas").attr("pad_backcolor");
            var pad_pencolor = current.find(".sfwcf7_canvas").attr("pad_pencolor");
            var pen_width = current.find(".sfwcf7_canvas").attr("pen_width");
            var digital_sig_name = current.find(".sfwcf7_canvas").attr("name");
            
            var digital_sig_pad = new SignaturePad(document.getElementById("digital_sig_canvas_"+digital_sig_name),{
                backgroundColor: pad_backcolor,
                penColor: pad_pencolor,
                maxWidth: pen_width
            });

            jQuery(document).on('touchstart touchend click', "#digital_sig_canvas_"+digital_sig_name, function(event){
                if(event.handled === false) return
                event.stopPropagation();
                event.handled = true;
                var dsimgdata = digital_sig_pad.toDataURL('image/png');
                jQuery("input[name="+digital_sig_name+"]").val(dsimgdata);
            });

            jQuery(current).find(".sfwcf7-sign").click(function(e){
                e.preventDefault();
                digital_sig_pad.clear();
                jQuery("input[name="+digital_sig_name+"]").val('');
            });    
        }else {
            jQuery(this).html("<p>Multiple Signaturepad is Valid in pro version of signature field with contact form 7 <a href='https://topsmodule.com/product/signature-field-with-contact-form-7/' target='_blank'>Click here Get Pro Version</a></p>");
        }
        multiple++;
    });
});