<?php 
add_action('wpcf7_admin_init','sfwcf7_signature_tag_generator');
function sfwcf7_signature_tag_generator($post){
    if (!class_exists('WPCF7_TagGenerator')) {
        return;
    }
    $tag_generator = WPCF7_TagGenerator::get_instance();
    $tag_generator->add( 'digital_sign', __( 'digital_sign', 'signature-field-with-contact-form-7' ) , 'sfwcf7_tag_generator_signature' );
}


function sfwcf7_tag_generator_signature($contact_form, $args = '' ){


	$args = wp_parse_args( $args, array() );
	
	$wpcf7_contact_form = WPCF7_ContactForm::get_current();
	$contact_form_tags = $wpcf7_contact_form->scan_form_tags();
	$type = 'digital_sign';
	$description = __( "Generate a form-tag for a signature field.", 'signature-field-with-contact-form-7' );
	?>
	<div class="control-box">
		<fieldset>
			<legend><?php echo esc_attr($description); ?></legend>
			<table class="form-table">
				<tr>
					<th>
						<label for="<?php echo esc_attr( $args['content'] . '-filed_type' ); ?>"><?php echo esc_html( __( 'Field type', 'signature-field-with-contact-form-7' ) ); ?></label>
					</th>
					<td>
						<input type="checkbox" name="required" class=" required_files" required>
						<label><?php echo esc_html( __( 'Required Field', 'signature-field-with-contact-form-7' ) ); ?></label>
					</td>
					</tr>
				<tr>
					<th><?php echo esc_html( __( 'Name', 'signature-field-with-contact-form-7' ) ); ?></th>
					<td>
						<input type="text" name="name">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'signature-field-with-contact-form-7' ) ); ?></label>
					</th>
					<td>
						<input type="text" name="id" class="signature_id oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'signature-field-with-contact-form-7' ) ); ?></label>
					</th>
					<td>
						<input type="text" name="class" class="signature_value oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $args['content'] . '-pad_backcolor' ); ?>"><?php echo esc_html( __( 'Pad background color', 'signature-field-with-contact-form-7' ) ); ?></label>
					</th>
					<td>
						<input type="text" name="pad_backcolor" value="#dddddd"  data-alpha="true" data-default-color="#dddddd" class="pad_backcolor oneline option color-picker" id="<?php echo esc_attr( $args['content'] . '-pad_backcolor' ); ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $args['content'] . '-pad_pencolor' ); ?>"><?php echo esc_html( __( 'Pad Pen color', 'signature-field-with-contact-form-7' ) ); ?></label>
					</th>
					<td>
						<input type="text" name="pad_pencolor" value="#000000"  data-alpha="true" data-default-color="#000000" class="pad_pencolor oneline option color-picker" id="<?php echo esc_attr( $args['content'] . '-pad_pencolor' ); ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $args['content'] . '-pad_width' ); ?>"><?php echo esc_html( __( 'Width', 'signature-field-with-contact-form-7' ) ); ?></label>
					</th>
					<td>
						<input type="number" name="pad_width" value="300" class="pad_width oneline option" id="<?php echo esc_attr( $args['content'] . '-pad_width' ); ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $args['content'] . '-pad_height' ); ?>"><?php echo esc_html( __( 'Height', 'signature-field-with-contact-form-7' ) ); ?></label>
					</th>
					<td>
						<input type="number" name="pad_height" value="200" class="pad_height oneline option" id="<?php echo esc_attr( $args['content'] . '-pad_height' ); ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $args['content'] . '-pen_width' ); ?>"><?php echo esc_html( __( 'Pen Width', 'signature-field-with-contact-form-7' ) ); ?></label>
					</th>
					<td>
						<input type="number" name="pen_width" value="2" class="pen_width oneline option" id="<?php echo esc_attr( $args['content'] . '-pen_width' ); ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $args['content'] . '-clear_text' ); ?>"><?php echo esc_html( __( 'Clear Text', 'signature-field-with-contact-form-7-pro' ) ); ?></label>
					</th>
					<td>
						<input type="text" name="clear_text" value="Clear" class="clear_text oneline option" id="<?php echo esc_attr( $args['content'] . '-clear_text' ); ?>" />
					</td>
				</tr>
			</table>
		</fieldset>
	</div>
	<div class="insert-box"> 
		<input type="text" name="<?php echo esc_attr($type); ?>" class="tag code" readonly="readonly" onfocus="this.select()" />
		<div class="submitbox">
			<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'signature-field-with-contact-form-7' ) ); ?>" />
		</div>
		<br class="clear" />
		<p class="description mail-tag">
			<label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'signature-field-with-contact-form-7' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?>
				<input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" />
			</label>
		</p>
	</div>
	<?php
	}
?>