<?php
/*
@package  NewsPostType
/*
  Plugin Name:  content gallery
  Plugin URI: https://www.Ebizontech.com/gallery
  Description: Plugin for News Custom Post Type  .  
  Author: Ebizon
  Version: 1.0
  Author URI: https://www.Ebizontech.com
*/
 ini_set('display_errors', '1');

define( 'cgallery_PLUGIN', __FILE__ );

define( 'cgallery_PLUGIN_BASENAME', plugin_basename( cgallery_PLUGIN ) );

define( 'cgallery_PLUGIN_NAME', trim( dirname( cgallery_PLUGIN_BASENAME ), '/' ) );

define( 'cgallery_PLUGIN_DIR', untrailingslashit( dirname( cgallery_PLUGIN ) ) );

define( 'cgallery_PLUGIN_DIR_URL', untrailingslashit( plugin_dir_url( cgallery_PLUGIN ) ) );

ob_start();


global $jal_db_version;
$jal_db_version = '1.0';

add_action( 'init', 'contentgallery' );
function contentgallery() {
	$args = array(
	  'public' => true,
	  'label' => "Content Gallery",
	  'labels'  =>   array(
		'name' => "Content Gallery",
		'add_new'     => _x( 'Add New', 'gallery', 'contentgallery' ),
		'add_new_item'=> __( 'Add New', 'contentgallery' )
	  ),
	  'description'         => 'This is a description for my post type.',
	  'capabilities' => array(
	  'create_posts' => true, // false < WP 4.5, credit @Ewout disable add new
		),
	  'map_meta_cap' => true,
	  'publicly_queryable' => true,
	  'show_ui'            => true,
	  'query_var'          => true,
	  'rewrite'            => array( 'slug' => 'contentgallery' ),
	  'capability_type'    => 'post',
	  'supports' =>array('title'),
	  // 'taxonomies'          => array( 'category' )
	  // 'rewrite' => array( 'slug' => 'tag' )
	);
register_post_type( 'contentgallery', $args );
}

add_action('admin_menu', 'cgallery_menu_page',12);
function cgallery_menu_page(){
    add_menu_page('gallery', 'Content Gallery ', 'manage_options', 'contentgallery','redirect_toCustomUrl1','dashicons-align-left');
    add_submenu_page('contentgallery', 'gallery', 'All content Gallery', 'manage_options', 'contentgallery', 'redirect_contentgallery' );
    add_submenu_page('contentgallery', 'gallery', 'Add content Gallery', 'manage_options', admin_url().'post-new.php?post_type=contentgallery');
}
function redirect_contentgallery(){
	$url = admin_url().'edit.php?post_type=contentgallery';
	header("Location:$url");
	exit;
}

add_filter( 'manage_contentgallery_posts_columns', 'set_contentGallery_Column' );
add_action( 'manage_contentgallery_posts_custom_column' , 'custom_contentGallery_updated_column', 10, 2 );

function set_contentGallery_Column($columns) {
	unset( $columns['date'] );
	$columns['Shortcode'] = __( 'Shortcode' );
	$columns['date'] = __( 'Date' );
	$columns['Author'] = __( 'Author');
	$columns['Tgroup'] = __( 'content Gallerys Group' );
	$columns['country'] = __( 'country' );
	return $columns;
}
function custom_contentGallery_updated_column( $column, $post_id ) {
	$meta= get_post_meta($post_id,"__gallerycontentdata")[0];
	switch ( $column ) {
		case 'Shortcode' :
			echo '[custumcontentGalleryEBZ postid='.$post_id.']';
			 break;
		case 'Tgroup' :
			echo $meta["type_render"] ;
			 break;
		case 'country' :
			echo $meta["country_render"] ;
			 break;	 	      
	}
}


function contentGallery_remove_menu_items()
{
	remove_menu_page( 'edit.php?post_type=contentGallery');
}
add_action( 'admin_menu', 'contentGallery_remove_menu_items' );


function contentGallery_row_actions( $actions, $post )
{
	if ($post->post_type == 'contentGallery'  )
	{
		unset( $actions['inline hide-if-no-js'] );
		return $actions;
	}
	return $actions;
}
add_action( 'post_row_actions', 'contentGallery_row_actions', 10, 2 );


function contentGallery_get_edit_post_link( $link, $post_id, $concat_postontext ) {
	global $post;
	if ( 'contentGallery' == $post->post_type )
		$link =  get_site_url().'/wp-admin/post.php?post='.$post_id.'&action=edit';
	return $link;
};

add_filter( 'get_edit_post_link', 'contentGallery_get_edit_post_link', 10, 3 );



function callCreatecontentGallery() 
{ 
  include_once(cgallery_PLUGIN_DIR.'/createcontentGallery.php' );
  return new CreatecontentGallery();
}
if ( is_admin() )
	add_action( 'init', 'callCreatecontentGallery' );



/*******************************************/
include 'contentGallery-shortcode.php';		         
/*******************************************/

add_action( 'wp_ajax_addrating', 'rateforcurrentUser' );
add_action( 'wp_ajax_nopriv_addrating', 'rateforcurrentUser' );

function rateforcurrentUser(){
$currRate=$_POST['data']["data"];
$id=$_POST['data']["id"];
print_r($_POST);
$user_id=get_current_user_id();
$rating=get_post_meta($id,"__g_rating")[0];
$init = array(
			'rating' => 0,
			'type' => 'rating',
			'number' => 0
		);
$rating=isset($rating)&&sizeof($rating)?$rating:$init;
$rating["number"]=$rating["number"]+1;
$rating["rating"]=($rating["rating"]+$currRate+0.0)/($rating["number"]+0.0);
$userrate=$init;
$userrate["rating"]=$currRate;
print_r($rating);
print_r($userrate);
update_post_meta($id,"__g_rating_by_u_".$user_id,$userrate );
update_post_meta($id,"__g_rating",$userrate);
die("@#@#");
}




