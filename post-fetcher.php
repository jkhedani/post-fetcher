<?php

/**
 *	Post Fetcher Functions
 *	Requires: Wordpress
 */
function post_fetcher_setup() {
	$protocol = 'http:'; 
  if( !empty( $_SERVER['HTTPS'] ) ) $protocol = 'https:'; // discover the correct protocol to use
	wp_enqueue_script('post-fetcher-scripts', get_stylesheet_directory_uri() . '/lib/post-fetcher/post-fetcher.js', array('jquery','json2'), false, true ); // Enqueue post-fetcher.js
  wp_localize_script('post-fetcher-scripts', 'post_fetcher_data', array( // Localize post-fetcher.js
    'ajaxurl' => admin_url('admin-ajax.php',$protocol),
    'nonce' => wp_create_nonce('post_fetcher_nonce')
  ));
}
add_action( 'wp_enqueue_scripts', 'post_fetcher_setup' );

/**
 *	Fetch Posts
 */
function fetch_posts() {

	// INIT
	$nonce = $_REQUEST['nonce'];
	if ( !wp_verify_nonce( $nonce, 'post_fetcher_nonce') ) die( __('Busted.') ); // Nonce check
	$html = "";
	$success = false;
	$posttype = $_REQUEST['posttype'];
	$filtertype = $_REQUEST['filtertype'];
	$termslug = $_REQUEST['termslug'];

	// CONTENT
	// CASE A: Filter by category
	if ( $filtertype === 'category' ) {
		$posts = new WP_Query( array(
			'post_type' => $posttype,
			'category_name' => $termslug,
			'posts_per_page' => 6,
		));
		while ( $posts->have_posts() ) : $posts->the_post();
			$html .= get_the_title();
		endwhile;
		wp_reset_postdata();
	}

	// RESPONSE
	$success = true;
	$response = json_encode(array(
		'success' => $success,
		'html' => $html,
	));

	header("content-type: application/json");
	echo $response;
	exit;
}
add_action('wp_ajax_nopriv_fetch_posts', 'fetch_posts');
add_action('wp_ajax_fetch_posts', 'fetch_posts');

/**
 *	Run Ajax calls even if user is logged in
 */
if( isset($_REQUEST['action']) && ($_REQUEST['action']=='fetch_posts') ):
	do_action( 'wp_ajax_' . $_REQUEST['action'] );
  do_action( 'wp_ajax_nopriv_' . $_REQUEST['action'] );
endif;

?>