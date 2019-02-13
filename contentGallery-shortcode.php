<?php

function Enquescriptforallgallery()
{
	require_once( ABSPATH . 'wp-admin/includes/template.php' );
 wp_enqueue_style('gll0', plugin_dir_url(__FILE__) . 'css/customgallery.css');
	wp_enqueue_script('gll1', plugin_dir_url(__FILE__) . 'js/allgallery.js');
	wp_localize_script('gll1', 'galleryVar', array(
		'pluginsUrl' => plugins_url(__FILE__),
		'currentUser' => get_current_user_id(),
		'ajax'=>admin_url('admin-ajax.php')
	)); 
}

add_shortcode("printallgalleryEBZ",'gallery_printAll');

function gallery_printAll($args){
	global $wpdb;
	
	Enquescriptforallgallery();
	// $valtype=$args['type'];
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	$args1 = array('posts_per_page' => 6, 'paged' => $paged,'post_type'=>'contentgallery');
	$pst = new WP_Query($args1);
	if ($pst->have_posts()){
	?>

	<div id="gallery_listing" class=="gallery_listing">
		<div class ="sorted_by">
		<span class="sorted_by_type"> Sort By :  
			<span class ="sortType"><select id="type_render" name="type_render">
        		<option <?php echo ((!isset($type))?'selected':'')  ?>value="-1" > Type </option>
        		<option value="studentphoto" <?php echo((isset($type)&&($type=="studentphoto"))?'selected':'') ?> > Student Photo</option>
        		<option value="infographics" <?php echo((isset($type)&&($type=="infographics"))?'selected':'') ?> > Info Graphic</option>
        		<option value="studentwork" <?php echo((isset($type)&&($type=="studentwork"))?'selected':'') ?> > Student Work</option>
           </select> <span>
           |
           <span class="sort_by_country">

           </span>
		</span> </div>
<?php
	while ($pst->have_posts()){ 
		$pst->the_post();
		$id=get_the_ID();
		$tstm=get_post_meta($id,"__gallerycontentdata")[0];
		// print_r($tstm);
		$subcontent=substr($tstm['gcontent-'.$id],0,200)."...";
		$rated = array(
			'rating' => 4,
			'type' => 'rating',
			'number' => 0,
		);
		$rate_it_now = array(
			'rating' => 0,
			'type' => 'rating',
			'number' => 0
		);
		$rating=get_post_meta($id,"__galleryRating")[0];
		$rating=isset($rating)?$rating:$rated;
		?>
		<article>
		<div id="gallery_<?php echo $id;?>" class="one_gfile" >	
			<div class="img_portion"><img src="<?php echo $tstm['content_img']; ?>"></div>
			<div class="content_portion"><span> <?php echo $subcontent; ?></span></div>
			<div class="contentx_rating"> <?php wp_star_rating($rating); ?> </div>
			<!-- <div class="rate_it_now">Here you rated : <?php wp_star_rating($rate_it_now); ?> </div>  -->
		</div></article>	
		<?php
	}
	?>

	<div class="pagination">
		<?php 
		echo paginate_links( array(
			'base'         => str_replace( 9999999999 , '%#%', esc_url( get_pagenum_link( 9999999999 ) ) ),
			'total'        => $pst->max_num_pages,
			'current'      => max( 1, get_query_var( 'paged' ) ),
			'format'       => '?paged=%#%',
			'show_all'     => false,
			'type'         => 'plain',
			'end_size'     => 2,
			'mid_size'     => 1,
			'prev_next'    => true,
			'prev_text'    => sprintf( '<i></i> %1$s', __( '« Previous', 'text-domain' ) ),
			'next_text'    => sprintf( '%1$s <i></i>', __( 'Next »', 'text-domain' ) ),
			'add_args'     => false,
			'add_fragment' => '',
			) );
			?>
		</div>
    </div>
	<?php
   }
}



function slidergalleryEnque($data,$htmlArray)
{
    wp_enqueue_style('sgll0', plugin_dir_url(__FILE__) . 'css/sliderstudentpage.css');
	wp_enqueue_script('sgll1', plugin_dir_url(__FILE__) . 'js/slidergallery.js');
	wp_localize_script('sgll1', 'SliderVar', array(
		'pluginsUrl' => plugins_url(__FILE__),
		'currentUser' => get_current_user_id(),
		'ajax'=>admin_url('admin-ajax.php'),
		'sliderData'=> $data,
		'htmlbigDescription' => $htmlArray
	)); 
}


add_shortcode("studentpageGallerySlider",'SliderGallery');

function SliderGallery(){
	global $wpdb;
	require_once( ABSPATH . 'wp-admin/includes/template.php' );
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	$args1 = array('posts_per_page' => 7, 'paged' => $paged,'post_type'=>'contentgallery');
	$pst = new WP_Query($args1);
	// echo "<pre>";
	$user_id=get_current_user_id();
	$xx=array();
	$htmlArray = array();
	while ($pst->have_posts()){ 
		$pst->the_post();
		$id=get_the_ID();
		$tstm=get_post_meta($id,"__gallerycontentdata")[0];
		$rating=get_post_meta($id,"__g_rating")[0];
		$User_rating=get_post_meta($id,"__g_rating_by_u_".$user_id)[0];
		$gcontent["gmeta_data"]=$tstm;
		$gcontent["g_rating"]=$rating;
		$gcontent["g_rating_byU"]=$User_rating;
		array_push($xx, $gcontent);
		array_push($htmlArray,gethtml($gcontent));

	}
	
	$jscontent=json_encode($xx);
	$htmlArray=json_encode($htmlArray);
	// print_r($jscontent);
	slidergalleryEnque($jscontent,$htmlArray);

?>
	<div class="sliderStudent">

		<div id="showMain" class="showMain">
				<?php
				$bigimgdata=$xx[0];
				?>
				<div id="bigImgContent">
				 	<?php echo gethtml($bigimgdata); ?>
			   </div>
				<span class="viewAll"> <a href="/all-gallery"> View All </a> </span>
		</div>
		<div id="showrightimgs" class="showrightimgs">
			<?php
				for ($ind=1; $ind <sizeof($xx) ; $ind++) { 
					$data=$xx[$ind]["gmeta_data"];
					// print_r($data);
					?>
					<span class="smallimg" >
						<img src="<?php echo $data['content_img']; ?>">
					</span>

					<?php
				}
				?>
			
		</div>			

	</div>

<?php 
}

function gethtml($bigimgdata){
	$id=$bigimgdata["gmeta_data"]["id"];
	$rating=$bigimgdata["g_rating"];
	$rating['echo']=false;
	$rate_it_now=$bigimgdata["g_rating_byU"];
	$rate_it_now['echo']=false;
	// print_r($bigimgdata["gmeta_data"]["gcontent-".$id]);
	$description=substr($bigimgdata["gmeta_data"]["gcontent-".$id],0,200).(strlen($bigimgdata["gmeta_data"]["gcontent-".$id])<200?"":"..");
	$str='
				<span class="bigimg">
						<img src="'.$bigimgdata["gmeta_data"]["content_img"] .'">
				 </span>
				 <div class="sliderDescription">
					<div class="studentName"> '.$bigimgdata["gmeta_data"]["student_render"] .'</div>
					<div class="school"> '. $bigimgdata["gmeta_data"]["school_render"] .'</div>
					<div class="description"> '.$description.'</div>
					<div class="rating">
					<div class="content_rating"> '. wp_star_rating($rating) .' </div> 
		 	        <div class="rate_it_now">Your Rating : '. wp_star_rating($rate_it_now) .' </div> 
		 	        </div>
				 </div>
	';
	return $str;
}



























