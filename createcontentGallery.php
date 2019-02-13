<?php 
/** 
 * This Class handles Frontend Through Metaboxes
 */
class CreatecontentGallery
{
  const LANG = 'EN';

  private $setting;
  public function __construct(){ 
    // global $post;  
    add_action( 'add_meta_boxes', array( &$this, 'add_some_meta_box_news' ) );
    add_action( 'save_post' , array( &$this, 'create_save_news'),10,1 );
    $this->setting =  get_post_meta($_GET["post"],"__gallerycontentdata")[0];
    // print_r($this->setting);
  }

  public function add_some_meta_box_news()
  {
    add_meta_box( 
     'featured_image'
     ,__( 'Content Image ', self::LANG )
     ,array( &$this, 'rcontent' )
     ,'contentgallery' 
     ,'advanced'
     ,'high'
   );
    add_meta_box( 
     'gallery_content'
     ,__( 'Gallery Content', self::LANG )
     ,array( &$this, 'cContent_render' )
     ,'contentgallery' 
     ,'advanced'
     ,'high'
   );

    add_meta_box( 
     'type'
     ,__( 'Gallery content type', self::LANG )
     ,array( &$this, 'type_render' )
     ,'contentgallery' 
     ,'advanced'
     ,'high'
   );
add_meta_box( 
     'country'
     ,__( 'country', self::LANG )
     ,array( &$this, 'country_render' )
     ,'contentgallery' 
     ,'advanced'
     ,'high'
   );


  }



 public function country_render($post){
    $gx=$this->setting;
    $country_render=isset($gx["country_render"])?$gx["country_render"]:"";
   ?> 
   <div>
    <input type="country" id="country_render" name="country_render" value="<?php echo $country_render ;?>" style="width: 100%;" placeholder="Enter country" >
   </div>
   <?php
}


  public function rcontent($post) 
  {
    $this->enqueue_scripts();
    $gx=$this->setting;
    $default=$gx["content_img"];
    $src=(!$default) ? Recorder_PLUGIN_DIR_URL . '/images/P.png':$default;
    ?>
    <input type="hidden" id="rec_overlay" name="rec_overlay" <?php echo (!$default) ? '' : 'value="'.$default.'"'; ?> >
    <img id="upload_image" name="upload_image" src="<?php echo $src; ?>" style="width: 100%; margin-bottom:-5px" >
    <script>
      jQuery(document).ready( function( $ ) {

        $('#upload_image').click(function() {

          formfield = $('#upload_image').attr('name');
          tb_show( '', 'media-upload.php?type=image&amp;TB_iframe=true' );
          window.send_to_editor = function(html) {
           imgurl = $(html).attr('src');console.log(html);
           $('#rec_overlay').val(imgurl);
           jQuery('#upload_image').attr("src", imgurl);
           tb_remove();
         }

         return false;
       });

      });
    </script>

    <?php
    echo $_GET['page'];
  }

  public function type_render($post){
    $gx=$this->setting;
    $type=isset($gx["type_render"])?$gx["type_render"]:"";
    ?> 
    <div>
      <select id="type_render" name="type_render">
        <option <?php echo ((!isset($type))?'selected':'')  ?>value="-1" >Select Content Gallery Type </option>
        <option value="studentphoto" <?php echo((isset($type)&&($type=="studentphoto"))?'selected':'') ?> > Student Photo</option>
        <option value="infographics" <?php echo((isset($type)&&($type=="infographics"))?'selected':'') ?> > Info Graphic</option>
        <option value="studentwork" <?php echo((isset($type)&&($type=="studentwork"))?'selected':'') ?> > Student Work</option>
      </select>
    </div>

    <?php
  }


public function cContent_render($post){
  $gx=$this->setting;
  // print_r($gx);
  $post_id=$_GET["post"];
    $content=isset($gx["gcontent-".$post_id])?$gx["gcontent-".$post_id]:""; 
    $editor_id="gcontent-".$post->ID;
    ?>
   <div>
      <?php echo wp_editor( $content, $editor_id ); ?> 
   </div>
   <?php
}





  public function enqueue_scripts() {    
    wp_enqueue_script('jquery');
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
    wp_enqueue_style('thickbox');
  }

  public function create_save_news( $post_id ) {
     if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
     return $post_id;

// print_r($_POST['post_type']);die("eqw");
    // Check permissions to edit pages and/or posts
    if ( 'contentgallery' == $_POST['post_type'] ) {
      if ( !current_user_can( 'edit_page', $post_id ) || !current_user_can( 'edit_post', $post_id ))
        return $post_id;
    } else{
      return;
    } 
// print_r($_POST);
// die("3234");
    $metaData["content_img"]=$_POST["rec_overlay"];
    $metaData["post_title"] = $_POST["post_title"];
    $metaData["country_render"] = strtolower($_POST["country_render"]);
    $metaData["gcontent-".$post_id] = $_POST["gcontent-".$post_id];
    $metaData["type_render"] = $_POST["type_render"];
    $metaData["id"] = $post_id;
    // print_r($metaData);die();
    $this->setting=$metaData;
    update_post_meta($post_id, '__gallerycontentdata', $metaData);
    update_post_meta($post_id, '__gallerycontentcountry', $metaData['country_render']); 
    update_post_meta($post_id, '__gallerycontenttype', $metaData['type_render']); 
  }
}