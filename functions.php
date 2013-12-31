<?php
/**
 * P-Not-Comics Custom Theme functions and definitions
 *
 * @package P-Not-Comics Custom Theme
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}

if ( ! function_exists( 'pnc_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function pnc_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on P-Not-Comics Custom Theme, use a find and replace
	 * to change 'pnc' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'pnc', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	//add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'pnc' ),
	) );

	// Enable support for Post Formats.
	add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ) );

	// Setup the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'pnc_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif; // pnc_setup
add_action( 'after_setup_theme', 'pnc_setup' );

/**
 * Custom P-Not-Comics Post Type
 */
if ( ! function_exists( 'create_pnc_post' ) ) :
function create_pnc_post() {
	register_post_type( 'pnc_post',
		array(
			'labels' => array(
				'name' => __( 'Comics' ),
				'singular_name' => __( 'Comic' ),
				'add_new' => _x('Add New', 'Comic'),
				'add_new_item' => __( 'Add New Comic'),
				'edit_item' => __( 'Edit Comic' ),
				'new_item' => __( 'New Comic' ),
				'view_item' => __( 'View Comic' )
			),
			'public' => true,
			'has_archive' => true,
		)
	);
}
endif; // create_pnc_post
add_action( 'init', 'create_pnc_post' );

/**
 * Custom meta boxes for P-Not-Comics Post Type.
 *
 * This probably belongs in a plugin and not my theme. 
 * But I don't care much about proper Wordpress architecture
 * tonight, and everything is basically global in PHP anyway.
 */
if ( ! function_exists( 'setup_pnc_metaboxes' ) ) :
function setup_pnc_metaboxes() {
	add_action( 'add_meta_boxes', 'create_pnc_metaboxes' );
	add_action( 'save_post', 'pnc_comic_meta_box_post', 10, 2 );
}
endif; // create_pnc_post
if ( ! function_exists( 'create_pnc_metaboxes' ) ) :
function create_pnc_metaboxes() {
	// The main comic image
	add_meta_box(
		'pnc-comic-main', // Unique ID
		esc_html__( 'Main Comic', 'main-comic' ), // Title
		'pnc_comic_main_meta_box', // Callback function
		'pnc_post', // post type
		'normal', // context -- really the main body of these posts
		'high' // priority -- this IS the post
	);
	// The previous/preview comic frame
	add_meta_box(
		'pnc-comic-frame', // Unique ID
		esc_html__( 'Previous Comic (or other Frame)', 'main-comic' ), // Title
		'pnc_comic_frame_meta_box', // Callback function
		'pnc_post', // post type
		'normal', // context -- really the main body of these posts
		'default' // priority -- this IS the post
	);
}
endif; // create_pnc_metaboxes

/**
 * P-Not-Comics Metabox Display Functions 
 */
if ( ! function_exists( 'pnc_comic_main_meta_box' ) ) :
function pnc_comic_main_meta_box( $post ) {
    $image_meta = 'pnc_main';
    $image_id = get_post_meta( $post->ID, $image_meta, true );
    $image_src = wp_get_attachment_url( $image_id );

    // Security nonce ("Number used Once")
    wp_nonce_field( basename( __FILE__ ), 'pnc_main_nonce' );
?>  
    <div class="custom_uploader">
        <img class="custom_media_image" src="<?php echo $image_src; ?>" style="max-width: 100%;"/><br />
        <a href="#" class="custom_media_add" style="<?php echo ( ! $image_id ? '' : 'display:none;' ) ?>">Set image</a>
        <a href="#" class="custom_media_remove" style="<?php echo ( ! $image_id ? 'display:none;' : '' ) ?>">Remove</a>
        <input class="custom_media_id" type="hidden" name="<?php echo $image_meta; ?>" value="<?php echo $image_id; ?>">
    </div>
<?php }
endif; // pnc_comic_main_meta_box

if ( ! function_exists( 'pnc_comic_frame_meta_box' ) ) :
function pnc_comic_frame_meta_box( $post ) {
    $image_meta = 'pnc_frame';
    $image_id = get_post_meta( $post->ID, $image_meta, true );
    $image_src = wp_get_attachment_url( $image_id );

    // Security nonce ("Number used Once")
    wp_nonce_field( basename( __FILE__ ), 'pnc_frame_nonce' );
?>  
    <div class="custom_uploader">
	<img class="custom_media_image" src="<?php echo $image_src; ?>" style="max-width: 100%;"/><br />
	<a href="#" class="custom_media_add" style="<?php echo ( ! $image_id ? '' : 'display:none;' ) ?>">Set image</a>
	<a href="#" class="custom_media_remove" style="<?php echo ( ! $image_id ? 'display:none;' : '' ) ?>">Remove</a>
	<input class="custom_media_id" type="hidden" name="<?php echo $image_meta; ?>" value="<?php echo $image_id; ?>">
    </div>

<?php 

    // JavaScript code for display, control, etc...
    wp_enqueue_script( 'pnc-custom-upload', get_template_directory_uri() . '/js/custom-upload.js', array(), '20131229', true );
}
endif; // pnc_comic_frame_meta_box

/**
 * P-Not-Comics Metabox POST Function(s)
 */
if ( ! function_exists( 'pnc_comic_meta_box_post' ) ) :
function pnc_comic_meta_box_post( $post_id, $post ) {

	// Check the type of POST and the verify the nonce
	$nonce_error = false;

	if( isset( $_POST['pnc_main_nonce'] ) && !wp_verify_nonce( $_POST['pnc_main_nonce'], basename( __FILE__ ) ) )
		return $post_id;

	if( isset( $_POST['pnc_frame_nonce'] ) && !wp_verify_nonce( $_POST['pnc_frame_nonce'], basename( __FILE__ ) ) )
		return $post_id;

	if ( $nonce_error )
		// 403
		return $post_id;

	// Save POST metadata, the comic URL
	$post_type = get_post_type_object( $post->post_type );

	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id; // 403

	if ( isset($_POST['pnc_main']) && intval($_POST['pnc_main']))
	   $new_main_meta_value = intval($_POST['pnc_main']);

	if ( isset($_POST['pnc_frame']) && intval($_POST['pnc_frame']))
	   $new_frame_meta_value = intval($_POST['pnc_frame']);

	$update_meta = function ($name, &$ref, &$post_id) {
		$meta_key = "pnc_$name";

		$meta_value = get_post_meta( $post_id, $meta_key, true );

		if ( $ref && '' == $meta_value ) {
			add_post_meta( $post_id, $meta_key, $ref, true );
		} elseif ( $ref && $ref != $meta_value ) {
			update_post_meta( $post_id, $meta_key, $ref );
		} elseif ( '' == $ref && $meta_value ) {
			delete_post_meta( $post_id, $meta_key, $meta_value );
		}
	};

	$update_meta("main", $new_main_meta_value, $post_id); // Handle main comic
	$update_meta("frame", $new_frame_meta_value, $post_id); // Handle frame comic
		
}
endif; // pnc_comic_meta_box_post


// Attach all of this P-Not-Comics stuff:
add_action( 'load-post.php', 'setup_pnc_metaboxes' );
add_action( 'load-post-new.php', 'setup_pnc_metaboxes' );

     
/**
 * Register widgetized area and update sidebar with default widgets.
 */
function pnc_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'pnc' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'pnc_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function pnc_scripts() {
	wp_enqueue_style( 'pnc-style', get_stylesheet_uri() );

	wp_enqueue_script( 'pnc-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );

	wp_enqueue_script( 'pnc-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'pnc_scripts' );

/**
 * Implement the Custom Header feature.
 */
//require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';
