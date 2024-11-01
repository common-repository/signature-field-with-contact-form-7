<?php 
add_action( 'wpcf7_init' , 'sfwcf7_add_form_tag_signature' , 10, 0 );
function sfwcf7_add_form_tag_signature() {
	wpcf7_add_form_tag( array( 'digital_sign', 'digital_sign*' ), 'sfwcf7_signature_tag_handler',array('name-attr' => true) );
}


function sfwcf7_signature_tag_handler($tag){
	if ( empty( $tag->name ) ) {
		return '';
	}

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( $tag->type );

	if ( $validation_error ) {
		$class .= ' wpcf7-not-valid';
	}

	$atts = array();

	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$pad_backcolor = $tag->get_option( 'pad_backcolor', '', true );
	$pad_pencolor = $tag->get_option( 'pad_pencolor', '', true );
	$pad_width = $tag->get_option( 'pad_width', 'signed_int', true );
	$pad_height = $tag->get_option( 'pad_height', 'signed_int', true );
	$pen_width = $tag->get_option( 'pen_width', 'signed_int', true );
	$clear_text = $tag->get_option( 'clear_text', '', true );


	if ( $tag->has_option( 'readonly' ) ) {
		$atts['readonly'] = 'readonly';
	}

	if ( $tag->is_required() ) {
		$atts['aria-required'] = 'true';
	}

	if ( $validation_error ) {
		$atts['aria-invalid'] = 'true';
		$atts['aria-describedby'] = wpcf7_get_validation_error_reference(
			$tag->name
		);
	} else {
		$atts['aria-invalid'] = 'false';
	}

	$atts['name'] = $tag->name;
	$atts['type'] = 'hidden';

	$atts = wpcf7_format_atts( $atts );
	$html = sprintf(
		'
		<div class="dswcf7_digital_sig ">
			<span class="wpcf7-form-control-wrap wpcf7-sign-wrap %1$s" data-name="'.$tag->name.'"><input %2$s />%3$s
	            <canvas id="digital_sig_canvas_%1$s" name="%1$s" class="sfwcf7_canvas" pad_backcolor="'.$pad_backcolor.'" pad_pencolor="'.$pad_pencolor.'" width="'.$pad_width.'" height="'.$pad_height.'" pen_width="'.$pen_width.'"></canvas>
				<button class="btn btn-default sfwcf7-sign" id="sig-clearBtn">'.$clear_text.'</button>
	        </span>
        </div>',
		sanitize_html_class($tag->name), $atts, $validation_error, $tag->name);
	return $html;
}

/* validation of digital sign */
add_filter( 'wpcf7_validate_digital_sign', 'sfwcf7_digital_sign_validation', 10, 2 );
add_filter( 'wpcf7_validate_digital_sign*', 'sfwcf7_digital_sign_validation', 10, 2 );
function sfwcf7_digital_sign_validation( $result, $tag ) {
	$tag = new WPCF7_FormTag( $tag );

	$name = $tag->name;

	$value = isset($_POST[$name]) ? trim( wp_unslash( strtr( (string) sanitize_text_field($_POST[$name]), "\n", " " ) ) ) : '';

	if ( 'digital_sign' == $tag->basetype ) {
		if ( $tag->is_required() and '' === $value ) {
			if (method_exists($result,"invalidate")){
				$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
				return $result;
			} elseif ( '' !== $value ) {
				$result['valid'] = false;
				$result['reason'][$name] = wpcf7_get_message( 'invalid_required' );
			}
		}
	}

	if ( isset( $result['reason'][$name] ) && $id = $tag->get_id_option() ) {
		$result['idref'][$name] = $id;
	}

	return $result;
}

add_filter('wpcf7_posted_data', 'SFWCF7_manage_signature_data' );
function SFWCF7_manage_signature_data ($posted_data) {
	foreach ($posted_data as $key => $data) {
		if (is_string($data) && strrpos($data, "data:image/png;base64", -strlen($data)) !== FALSE){	      
  			$data_pieces = explode(",", $data);
			$encoded_image = $data_pieces[1];
			$decoded_image = base64_decode($encoded_image);
			$filename = sanitize_file_name(wpcf7_canonicalize($key."-".time().".png"));
			$sfwcf7_signature_dir = trailingslashit(SFWCF7_signature_dir());

			if (empty($posted_data[$key."-attachment"] == 1)){
	        	wpcf7_init_uploads();
				$uploads_dir = wpcf7_upload_tmp_dir();
				$uploads_dir = wpcf7_maybe_add_random_dir( $uploads_dir );
				$filename = wp_unique_filename( $uploads_dir, $filename );
				$filepath = trailingslashit( $uploads_dir ) . $filename;

	        	if ( $handle = @fopen( $filepath, 'w' ) ) {
					fwrite( $handle, $decoded_image );
					fclose( $handle );
		        	@chmod( $filepath, 0400 );
				}

	        	if( !file_exists( $sfwcf7_signature_dir ) ){ // Creating directory and htaccess file
		    		if (wp_mkdir_p( $sfwcf7_signature_dir )){
		    			$htaccess_file = $sfwcf7_signature_dir . '.htaccess';
						if ( !file_exists( $htaccess_file ) && $handle = @fopen( $htaccess_file, 'w' ) ) {
							fwrite( $handle, 'Order deny,allow' . "\n" );
							fwrite( $handle, 'Deny from all' . "\n" );
							fwrite( $handle, '<Files ~ "^[0-9A-Za-z_-]+\\.(png)$">' . "\n" );
							fwrite( $handle, '    Allow from all' . "\n" );
							fwrite( $handle, '</Files>' . "\n" );
							fclose( $handle );
						}
		    		}
	        	}

	        	$filepath = wp_normalize_path( $sfwcf7_signature_dir . $filename );

         		if ( $handle = @fopen( $filepath, 'w' ) ) {
					fwrite( $handle, $decoded_image );
					fclose( $handle );
	        		@chmod( $filepath, 0644 );
				}

				if (file_exists($filepath)){
				  	$fileurl = SFWCF7_signature_url($filename);
					$posted_data[$key] = $fileurl;
				}else{
					error_log("Cannot create signature file : ".$filepath);
				}
        	}   
		}
	}
	return $posted_data;
}

function SFWCF7_signature_dir() {
	return wpcf7_upload_dir( 'dir' ) . '/sfwcf7_signatures';

}

function SFWCF7_signature_url( $filename ) {
	return wpcf7_upload_dir( 'url' ) . '/sfwcf7_signatures/'. $filename;
}