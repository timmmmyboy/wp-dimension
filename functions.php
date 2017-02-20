<?php
/* Functions and stuff for the WP-Dimension theme
   
   Based on HTML5up html5up.net
   
   mods by and blame go to http://cog.dog
*/

// give us thumbnails and righteous sizes
add_theme_support( 'post-thumbnails' ); 
set_post_thumbnail_size( 480, 200, array( 'center', 'center') );

// give us custom backgrounds
add_theme_support( 'custom-background' );

// add menu order to posts
function dimension_posts_order() {
    add_post_type_support( 'post', 'page-attributes' );
}

add_action( 'admin_init', 'dimension_posts_order' );


// enqueue the scripts'n styles... do it right!

function dimension_scripts() {

	// dimension CSS
	wp_register_style( 'dimension-style', get_stylesheet_directory_uri() . '/assets/css/main.css' );
	wp_enqueue_style( 'dimension-style' );
	
	// dimension no script CSS
	wp_register_style( 'dimension-scriptless-style', get_stylesheet_directory_uri() . '/assets/css/noscript.css' );
	wp_enqueue_style( 'dimension-scriptless-style' );
	
	// custom jquery down in the footer you go
	wp_register_script( 'dimension-skel' , get_stylesheet_directory_uri() . '/assets/js/skel.min.js', array( 'jquery' ), false, true );
	wp_enqueue_script( 'dimension-skel' );
	
	wp_register_script( 'dimension-util' , get_stylesheet_directory_uri() . '/assets/js/util.js', array( 'jquery' ), false, true );
	wp_enqueue_script( 'dimension-util' );


	wp_register_script( 'dimension-main' , get_stylesheet_directory_uri() . '/assets/js/main.js', array( 'jquery' ), false, true );
	wp_enqueue_script( 'dimension-main' );
	
}

add_action( 'wp_enqueue_scripts', 'dimension_scripts' );




/*** Customizer settings to allow editing of a front quote, customizing the footer, and ivon ***/

add_action( 'customize_register', 'dimension_register_theme_customizer' );

// register custom customizer stuff

function dimension_register_theme_customizer( $wp_customize ) {
	// Create custom panel.
	$wp_customize->add_panel( 'text_blocks', array(
		'priority'       => 500,
		'theme_supports' => '',
		'title'          => __( 'Dimension Front Text', 'dimension' ),
		'description'    => __( 'Set editable text for front page content. Title and tagline are drawn from blog settings', 'dimension' ),
	) );

	// Add section for quote
	$wp_customize->add_section( 'custom_quote_text' , array(
		'title'    => __('Edit Custom Quote','dimension'),
		'panel'    => 'text_blocks',
		'priority' => 10
	) );
	// Add setting for quote
	$wp_customize->add_setting( 'quote_text_block', array(
		 'default'           => __( '', 'dimension' ),
		 'sanitize_callback' => 'sanitize_text'
	) );
	// Add control for quote
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'quote_text_block',
		    array(
		        'label'    => __( 'Quote Text', 'dimension' ),
		        'section'  => 'custom_quote_text',
		        'settings' => 'quote_text_block',
		        'type'     => 'textarea'
		    )
	    )
	);

	// Add section for custom footer
	$wp_customize->add_section( 'custom_footer_text' , array(
		'title'    => __('Change Footer Text','dimension'),
		'panel'    => 'text_blocks',
		'priority' => 20
	) );
	// Add setting for footer
	$wp_customize->add_setting( 'footer_text_block', array(
		 'default'           => __( '', 'dimension' ),
		 'sanitize_callback' => 'sanitize_text'
	) );
	// Add control for footer
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'custom_footer_text',
		    array(
		        'label'    => __( 'Footer Text', 'dimension' ),
		        'section'  => 'custom_footer_text',
		        'settings' => 'footer_text_block',
		        'type'     => 'text'
		    )
	    )
	);
	
	// add section for custom logo
	// ----- h/t https://kwight.ca/2012/12/02/adding-a-logo-uploader-to-your-wordpress-site-with-the-theme-customizer/
	$wp_customize->add_section( 'dimension_logo_section' , array(
		'title'       => __( 'Dimension Logo', 'dimension' ),
		'priority'    => 510,
		'description' => 'Upload your own logo to replace the little gears',
) );

	// add setting for logo
	$wp_customize->add_setting( 'dimension_logo' );
	
	// add controller for logo
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'dimension_logo', array(
		'label'    => __( 'Logo', 'dimension' ),
		'section'  => 'dimension_logo_section',
		'settings' => 'dimension_logo',
) ) );



 	// Sanitize text
	function sanitize_text( $text ) {
	    return sanitize_text_field( $text );
	}
}


function dimension_footer_text() {
	 if ( get_theme_mod( 'footer_text_block') != "" ) {
	 	echo get_theme_mod( 'footer_text_block');
	 } else {
	 	echo 'web work: <a href="http://cog.dog">cog.dog</a> &bull; theme: <a href="https://html5up.net">Dimension by HTML5 UP</a>';
	 }	
}

function dimension_quote_text() {
	 if ( get_theme_mod( 'quote_text_block') != "" ) {
	 	echo '<p>' . get_theme_mod( 'quote_text_block') . '</p>';
	 }	
}

/* post meta boxes 
	https://codex.wordpress.org/Plugin_API/Action_Reference/add_meta_boxes
*/

function dimension_add_meta_boxes( $post ){
	add_meta_box( 'dimension_meta_box', __( 'Link Info', 'dimension' ), 'dimension_build_meta_box', 'post', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'dimension_add_meta_boxes' );

function dimension_build_meta_box( $post ){
	
	wp_nonce_field( basename( __FILE__ ), 'dimension_meta_box_nonce' );
	
	// retrieve the _dimension_link current value
	$current_link = get_post_meta( $post->ID, '_dimension_link', true );
	
	// retrieve the _link_fa_icon current value
	$current_link_icon = get_post_meta( $post->ID, '_link_fa_icon', true );
	if ( empty( $current_link_icon ) ) $current_link_icon = 'fa-share';
	
	?>
			<p>
			<label for="dimension_link" style="font-weight:bold">Destination URL</label><br />
			<input type="text" name="dimension_link" value="<?php echo $current_link; ?>" style="width:100%" />
			</p>
			
			<p>

			<label for="link_fa_icon"  style="font-weight:bold">Font Awesome Button Icon</label><br />
			Use a <a href="http://fontawesome.io/icons/" target="_blank">Font Awesome icon</a> on the link button, enter class name e.g. <em>fa-car</em> or <em>fa-share</em><br />
			<input type="text" name="link_fa_icon" value="<?php echo $current_link_icon; ?>" /> 
			</p>

<?php
}

function dimension_save_meta_boxes_data( $post_id ){
	// got nonce?
	if ( !isset( $_POST['dimension_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['dimension_meta_box_nonce'], basename( __FILE__ ) ) ) return;

	// no autosave calls
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	
	// editors only
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;
	
	// update link meta data
	if ( isset( $_REQUEST['dimension_link'] ) ) {
		update_post_meta( $post_id, '_dimension_link', sanitize_text_field( $_POST['dimension_link'] ) );
	}
	
	// update icon meta data
	if ( isset( $_REQUEST['link_fa_icon'] ) ) {
		update_post_meta( $post_id, '_link_fa_icon', sanitize_text_field( $_POST['link_fa_icon'] ) );
	}
	

}

add_action( 'save_post', 'dimension_save_meta_boxes_data', 10, 2 );



?>