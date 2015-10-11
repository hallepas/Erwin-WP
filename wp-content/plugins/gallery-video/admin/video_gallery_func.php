<?php
if(function_exists('current_user_can'))
//if(!current_user_can('manage_options')) {
    
if(!current_user_can('delete_pages')) {
    die('Access Denied');
}	
if(!function_exists('current_user_can')){
	die('Access Denied');
}

function showvideogallery() 
  {
	global $wpdb;
	$limit=0;
	if(isset($_POST['search_events_by_title'])){
	$search_tag=esc_html(stripslashes($_POST['search_events_by_title']));
	}
	else {
	$search_tag ='';
	}
	$cat_row_query="SELECT id,name FROM ".$wpdb->prefix."huge_it_videogallery_galleries WHERE sl_width=0";
	$cat_row=$wpdb->get_results($cat_row_query);
	
	$query = $wpdb->prepare("SELECT COUNT(*) FROM ".$wpdb->prefix."huge_it_videogallery_galleries WHERE name LIKE %s" , "%{$search_tag}}%");
	
	$total = $wpdb->get_var($query);

	$query =$wpdb->prepare("SELECT  a.* ,  COUNT(b.id) AS count, g.par_name AS par_name FROM ".$wpdb->prefix."huge_it_videogallery_galleries  AS a LEFT JOIN ".$wpdb->prefix."huge_it_videogallery_galleries AS b ON a.id = b.sl_width 
LEFT JOIN (SELECT  ".$wpdb->prefix."huge_it_videogallery_galleries.ordering as ordering,".$wpdb->prefix."huge_it_videogallery_galleries.id AS id, COUNT( ".$wpdb->prefix."huge_it_videogallery_videos.videogallery_id ) AS prod_count
FROM ".$wpdb->prefix."huge_it_videogallery_videos, ".$wpdb->prefix."huge_it_videogallery_galleries
WHERE ".$wpdb->prefix."huge_it_videogallery_videos.videogallery_id = ".$wpdb->prefix."huge_it_videogallery_galleries.id
GROUP BY ".$wpdb->prefix."huge_it_videogallery_videos.videogallery_id) AS c ON c.id = a.id LEFT JOIN
(SELECT ".$wpdb->prefix."huge_it_videogallery_galleries.name AS par_name,".$wpdb->prefix."huge_it_videogallery_galleries.id FROM ".$wpdb->prefix."huge_it_videogallery_galleries) AS g
 ON a.sl_width=g.id WHERE a.name LIKE %s  group by a.id  ","%".$search_tag."%");

$rows = $wpdb->get_results($query);

$rows=open_cat_in_tree($rows);
	$query ="SELECT  ".$wpdb->prefix."huge_it_videogallery_galleries.ordering,".$wpdb->prefix."huge_it_videogallery_galleries.id, COUNT( ".$wpdb->prefix."huge_it_videogallery_videos.videogallery_id ) AS prod_count
FROM ".$wpdb->prefix."huge_it_videogallery_videos, ".$wpdb->prefix."huge_it_videogallery_galleries
WHERE ".$wpdb->prefix."huge_it_videogallery_videos.videogallery_id = ".$wpdb->prefix."huge_it_videogallery_galleries.id
GROUP BY ".$wpdb->prefix."huge_it_videogallery_videos.videogallery_id " ;
	$prod_rows = $wpdb->get_results($query);
		
foreach($rows as $row)
{
	foreach($prod_rows as $row_1)
	{
		if ($row->id == $row_1->id)
		{
			$row->ordering = $row_1->ordering;
			$row->prod_count = $row_1->prod_count;
		}
	}
}
	$cat_row=open_cat_in_tree($cat_row);
	$pageNav='';
	$sort='';
		html_showvideogallerys( $rows, $pageNav,$sort,$cat_row);
  }

function open_cat_in_tree($catt,$tree_problem='',$hihiih=1){

global $wpdb;
global $glob_ordering_in_cat;
static $trr_cat=array();
if(!isset($search_tag))
$search_tag='';
if($hihiih)
$trr_cat=array();
foreach($catt as $local_cat){
	$local_cat->name=$tree_problem.$local_cat->name;
	array_push($trr_cat,$local_cat);
}
return $trr_cat;

}
function editvideogallery($id)
  {
	  
	  global $wpdb;
	  
	  				 @session_start();
		 if(isset($_POST['csrf_token_hugeit_gallery']) && (!isset($_SESSION["csrf_token_hugeit_gallery"]) || $_SESSION["csrf_token_hugeit_gallery"] != @$_POST['csrf_token_hugeit_gallery']))
		 { exit; }
	  
	  if(isset($_GET["removeslide"])){
	     if($_GET["removeslide"] != ''){
	
	$idfordelete = $_GET["removeslide"];
	$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."huge_it_videogallery_videos  WHERE id = %d ", $idfordelete));
	   }
	   }
	   $query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_it_videogallery_galleries WHERE id= %d",$id);
	   $row=$wpdb->get_row($query);
	   if(!isset($row->videogallery_list_effects_s))
	   return 'id not found';
       $images=explode(";;;",$row->videogallery_list_effects_s);
	   $par=explode('	',$row->param);
	   $count_ord=count($images);
	   $cat_row=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_it_videogallery_galleries WHERE id!= %d and sl_width=0", $id));
       $cat_row=open_cat_in_tree($cat_row);
	   	  $query=$wpdb->prepare("SELECT name,ordering FROM ".$wpdb->prefix."huge_it_videogallery_galleries WHERE sl_width=%d  ORDER BY `ordering` ",$row->sl_width);
	   $ord_elem=$wpdb->get_results($query);
	   
	    $query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_it_videogallery_videos where videogallery_id = %d order by ordering ASC  ",$row->id);
			   $rowim=$wpdb->get_results($query);
			   
			   if(isset($_GET["addslide"])){
			   if($_GET["addslide"] == 1){
	
$table_name = $wpdb->prefix . "huge_it_videogallery_videos";
    $sql_2 = "
INSERT INTO 

`" . $table_name . "` ( `name`, `videogallery_id`, `description`, `image_url`, `sl_url`, `ordering`, `published`, `published_in_sl_width`) VALUES
( '', '".$row->id."', '', '', '', 'par_TV', 2, '1' )";

    $wpdb->query($sql_huge_it_videogallery_videos);
	

      $wpdb->query($sql_2);
	
	   }
	   }
	   
	   $query="SELECT * FROM ".$wpdb->prefix."huge_it_videogallery_galleries order by id ASC";
			   $rowsld=$wpdb->get_results($query);

	
	 $query="SELECT * FROM ".$wpdb->prefix."posts where post_type = 'post' and post_status = 'publish' order by id ASC";
			   $rowsposts=$wpdb->get_results($query);
	 
	 $rowsposts8 = '';
	 $postsbycat = '';
	 if(isset($_POST["iframecatid"])){
	 	  $query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."term_relationships where term_taxonomy_id = %d order by object_id ASC",$_POST["iframecatid"]);
		$rowsposts8=$wpdb->get_results($query);

			   foreach($rowsposts8 as $rowsposts13){
	 $query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."posts where post_type = 'post' and post_status = 'publish' and ID = %d  order by ID ASC",$rowsposts13->object_id);
			   $rowsposts1=$wpdb->get_results($query);
			   $postsbycat = $rowsposts1;
			   
	 }
	 }

    Html_editvideogallery($ord_elem, $count_ord, $images, $row, $cat_row, $rowim, $rowsld, $rowsposts, $rowsposts8, $postsbycat);
  }
  
function add_videogallery()
{
	global $wpdb;

	$table_name = $wpdb->prefix . "huge_it_videogallery_galleries";
    $sql_2 = "
INSERT INTO 

`" . $table_name . "` ( `name`, `sl_height`, `sl_width`, `pause_on_hover`, `videogallery_list_effects_s`, `description`, `param`, `sl_position`, `ordering`, `published`, `huge_it_sl_effects`) VALUES
( 'New Video Gallery', '375', '600', 'on', 'cubeH', '4000', '1000', 'center', '1', '300', '4')";


      $wpdb->query($sql_2);

   $query="SELECT * FROM ".$wpdb->prefix."huge_it_videogallery_galleries order by id ASC";
			   $rowsldcc=$wpdb->get_results($query);
			   $last_key = key( array_slice( $rowsldcc, -1, 1, TRUE ) );
			   
			   
	foreach($rowsldcc as $key=>$rowsldccs){
		if($last_key == $key){
			header('Location: admin.php?page=videogallerys_huge_it_videogallery&id='.$rowsldccs->id.'&task=apply');
		}
	}	
}


function videogallery_video($id)
{
	  global $wpdb;

	  if(isset($_POST["huge_it_add_video_input"])){
	  if($_POST["huge_it_add_video_input"] != ''){
	  $table_name = $wpdb->prefix . "huge_it_videogallery_videos";
	  

	  $sql_video = "INSERT INTO 
`" . $table_name . "` ( `name`, `videogallery_id`, `description`, `image_url`, `sl_url`, `sl_type`, `link_target`, `ordering`, `published`, `published_in_sl_width`) VALUES 
( '".$_POST["show_title"]."', '".$id."', '".$_POST["show_description"]."', '".$_POST["huge_it_add_video_input"]."', '".$_POST["show_url"]."', 'video', 'on', '0', '1', '1' )";
	

	  $query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_it_videogallery_galleries WHERE id= %d",$id);
	   $row=$wpdb->get_row($query);
	    $query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_it_videogallery_videos where videogallery_id = %d order by id ASC", $row->id);
			   $rowplusorder=$wpdb->get_results($query);
			   
			   foreach ($rowplusorder as $key=>$rowplusorders){
$rowplusorderspl=$rowplusorders->ordering+1;
$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_it_videogallery_videos SET ordering = '".$rowplusorderspl."' WHERE id = %d ", $rowplusorders->id));
}
	   $wpdb->query($sql_video);
	  }
	  }
	 
    Html_videogallery_video();
}


function removevideogallery($id)
{
	global $wpdb;
	 $sql_remov_tag=$wpdb->prepare("DELETE FROM ".$wpdb->prefix."huge_it_videogallery_galleries WHERE id = %d", $id);
 if(!$wpdb->query($sql_remov_tag))
 {
	  ?>
	  <div id="message" class="error"><p>videogallery Not Deleted</p></div>
      <?php
 }
 else{
 ?>
 <div class="updated"><p><strong><?php _e('Item Deleted.' ); ?></strong></p></div>
 <?php
 }
}

function apply_cat($id)
{	

		 global $wpdb;

		 if(!is_numeric($id)){
			 echo 'insert numerc id';
		 	return '';
		 }
		 if(!(isset($_POST['sl_width']) && isset($_POST["name"]) ))
		 {
			echo '';
		 }
		 $cat_row=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_it_videogallery_galleries WHERE id!= %d ", $id));

		 $max_ord=$wpdb->get_var('SELECT MAX(ordering) FROM '.$wpdb->prefix.'huge_it_videogallery_galleries');
	
            $query=$wpdb->prepare("SELECT sl_width FROM ".$wpdb->prefix."huge_it_videogallery_galleries WHERE id = %d", $id);
	        $id_bef=$wpdb->get_var($query);
			
					 @session_start();
		 if(isset($_POST['csrf_token_hugeit_gallery']) && (!isset($_SESSION["csrf_token_hugeit_gallery"]) || $_SESSION["csrf_token_hugeit_gallery"] != @$_POST['csrf_token_hugeit_gallery']))
		 { exit; }
      
	if(isset($_POST["content"])){
	$script_cat = preg_replace('#<script(.*?)>(.*?)</script>#is', '', stripslashes($_POST["content"]));
	}
			if(isset($_POST["name"]) && isset($_POST["display_type"]) && isset($_POST["content_per_page"])){
			if($_POST["name"] != ''){
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_it_videogallery_galleries SET  name = '".$_POST["name"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_it_videogallery_galleries SET  sl_width = '".$_POST["sl_width"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_it_videogallery_galleries SET  sl_height = '".$_POST["sl_height"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_it_videogallery_galleries SET  pause_on_hover = '".$_POST["pause_on_hover"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_it_videogallery_galleries SET  videogallery_list_effects_s = '".$_POST["videogallery_list_effects_s"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_it_videogallery_galleries SET  description = '".$_POST["sl_pausetime"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_it_videogallery_galleries SET  param = '".$_POST["sl_changespeed"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_it_videogallery_galleries SET  sl_position = '".$_POST["sl_position"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_it_videogallery_galleries SET  huge_it_sl_effects = '".$_POST["huge_it_sl_effects"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_it_videogallery_galleries SET  display_type = '".$_POST["display_type"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_it_videogallery_galleries SET  content_per_page = '".$_POST["content_per_page"]."'  WHERE id = '".$id."' ");
			
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_it_videogallery_galleries SET  ordering = '1'  WHERE id = '".$id."' ");
			}
			}

			if(isset($_POST["name"])){
			if($_POST["name"] != ''){
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_it_videogallery_galleries SET  name = '".$_POST["name"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_it_videogallery_galleries SET  sl_width = '".$_POST["sl_width"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_it_videogallery_galleries SET  sl_height = '".$_POST["sl_height"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_it_videogallery_galleries SET  pause_on_hover = '".$_POST["pause_on_hover"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_it_videogallery_galleries SET  videogallery_list_effects_s = '".$_POST["videogallery_list_effects_s"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_it_videogallery_galleries SET  description = '".$_POST["sl_pausetime"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_it_videogallery_galleries SET  param = '".$_POST["sl_changespeed"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_it_videogallery_galleries SET  sl_position = '".$_POST["sl_position"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_it_videogallery_galleries SET  huge_it_sl_effects = '".$_POST["huge_it_sl_effects"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_it_videogallery_galleries SET  ordering = '1'  WHERE id = '".$id."' ");
			}
			}
	
		
	$query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_it_videogallery_galleries WHERE id = %d", $id);
	   $row=$wpdb->get_row($query);

			    $query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_it_videogallery_videos where videogallery_id = %d order by id ASC", $row->id);
			   $rowim=$wpdb->get_results($query);
			   
			   foreach ($rowim as $key=>$rowimages){
if(isset($_POST["order_by_".$rowimages->id.""])){
$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_it_videogallery_videos SET  ordering = '".$_POST["order_by_".$rowimages->id.""]."'  WHERE ID = %d ", $rowimages->id));
$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_it_videogallery_videos SET  link_target = '".$_POST["sl_link_target".$rowimages->id.""]."'  WHERE ID = %d ", $rowimages->id));
$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_it_videogallery_videos SET  sl_url = '".str_replace('%','__5_5_5__',$_POST["sl_url".$rowimages->id.""])."' WHERE ID = %d ", $rowimages->id));
$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_it_videogallery_videos SET  name = '".str_replace('%','__5_5_5__',$_POST["titleimage".$rowimages->id.""])."'  WHERE ID = %d ", $rowimages->id));
$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_it_videogallery_videos SET  description = '".str_replace('%','__5_5_5__',$_POST["im_description".$rowimages->id.""])."'  WHERE ID = %d ", $rowimages->id));
$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_it_videogallery_videos SET  image_url = '".$_POST["imagess".$rowimages->id.""]."'  WHERE ID = %d ", $rowimages->id));
$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_it_videogallery_videos SET  thumb_url = '".$_POST["thumbs".$rowimages->id.""]."'  WHERE ID = %d ", $rowimages->id));
}
}

if (isset($_POST['params'])) {
      $params = $_POST['params'];
      foreach ($params as $key => $value) {
          $wpdb->update($wpdb->prefix . 'huge_it_videogallery_params',
              array('value' => $value),
              array('name' => $key),
              array('%s')
          );
      }
     
    }
	
		
	   if(isset($_POST["imagess"])){
	   if($_POST["imagess"] != ''){
				   		   $query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_it_videogallery_videos where videogallery_id = %d order by id ASC", $row->id);
			   $rowim=$wpdb->get_results($query);
	  foreach ($rowim as $key=>$rowimages){
	  $orderingplus = $rowimages->ordering+1;
	  $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_it_videogallery_videos SET  ordering = %d  WHERE ID = %d ", $orderingplus, $rowimages->id));
	  }
	$table_name = $wpdb->prefix . "huge_it_videogallery_videos";
	$imagesnewuploader = explode(";;;", $_POST["imagess"]);
	array_pop($imagesnewuploader);
	foreach($imagesnewuploader as $imagesnewupload){
	
    $sql_2 = "
INSERT INTO 

`" . $table_name . "` ( `name`, `videogallery_id`, `description`, `image_url`, `sl_url`, `sl_type`, `link_target`, `ordering`, `published`, `published_in_sl_width`) VALUES
( '', '".$row->id."', '', '".$imagesnewupload."', '', 'image', 'on', 'par_TV', 2, '1' )";

      $wpdb->query($sql_2);
		}	
	   }
	  }
	   
	if(isset($_POST["posthuge-it-description-length"])){
	 $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_it_videogallery_galleries SET  published = %d WHERE id = %d ", $_POST["posthuge-it-description-length"], $_GET['id']));
}
	?>
	<div class="updated"><p><strong><?php _e('Item Saved'); ?></strong></p></div>
	<?php
	
    return true;
	
}

?>