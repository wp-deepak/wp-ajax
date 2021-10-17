<?php

/*load js files*/
function custom_thems_style_fun(){

	wp_enqueue_script( 'custom-js', get_theme_file_uri( '/filter-loadmore-texonomy.js'));
	wp_localize_script( 'custom-js', 'custom_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}
add_action('wp_enqueue_scripts', 'custom_thems_style_fun');

/*Create post type dealer and dealer category */

/*Creating CPT for Dealers*/
function my_custom_post_dealer() {
  $labels = array(
    'name'               => _x( 'Dealers', 'post type general name' ),
    'singular_name'      => _x( 'Dealer', 'post type singular name' ),
    'add_new'            => _x( 'Add New', 'book' ),
    'add_new_item'       => __( 'Add New Dealer' ),
    'edit_item'          => __( 'Edit Dealer' ),
    'new_item'           => __( 'New Dealer' ),
    'all_items'          => __( 'All Dealers' ),
    'view_item'          => __( 'View Dealer' ),
    'search_items'       => __( 'Search Dealers' ),
    'not_found'          => __( 'No Dealers found' ),
    'not_found_in_trash' => __( 'No Dealers found in the Trash' ), 
    'parent_item_colon'  => ’,
    'menu_name'          => 'Dealers'
  );
  $args = array(
    'labels'        => $labels,
    'description'   => 'Holds our Dealers and dealer specific data',
    'public'        => true,
    'menu_position' => 5,
    'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
    'has_archive'   => true,
  );
  register_post_type( 'dealer', $args ); 
}
add_action( 'init', 'my_custom_post_dealer' );


function my_taxonomies_dealer() {
  $labels = array(
    'name'              => _x( 'Dealer Categories', 'taxonomy general name' ),
    'singular_name'     => _x( 'Dealer Category', 'taxonomy singular name' ),
    'search_items'      => __( 'Search Dealer Categories' ),
    'all_items'         => __( 'All Dealer Categories' ),
    'parent_item'       => __( 'Parent Dealer Category' ),
    'parent_item_colon' => __( 'Parent Dealer Category:' ),
    'edit_item'         => __( 'Edit Dealer Category' ), 
    'update_item'       => __( 'Update Dealer Category' ),
    'add_new_item'      => __( 'Add New Dealer Category' ),
    'new_item_name'     => __( 'New Dealer Category' ),
    'menu_name'         => __( 'Dealer Categories' ),
  );
  $args = array(
    'labels' => $labels,
    'hierarchical' => true,
  );
  register_taxonomy( 'dealer_category', 'dealer', $args );
}
add_action( 'init', 'my_taxonomies_dealer', 0 );

/*End texonomy and post type*/


/* Create shortcode for listing content fornt-end */
// Create Shortcode to Display Dealers Post Types
 
function shortcode_dealer_post_type(){
	ob_start();

	$taxonomies = get_terms( array(
	    'taxonomy' => 'dealer_category',
	    'hide_empty' => false
	) );


	if ( !empty($taxonomies) ) :
    $output = '<ul class="cat-list">';
    foreach( $taxonomies as $category ) {                   
        $output.= '<li><input name="cat-name" id="'.esc_html( $category->name ).'" value="'. esc_attr( $category->term_id ) .'" data-value="'. esc_attr( $category->term_id ) .'" type="radio"><label for="'.esc_html( $category->name ).'">'. esc_html( $category->name ) .'</label></li>';
    }
    $output.='</ul>';
    echo $output;
	endif;

	$postsPerPage = 4;
	$args = array(
	    'post_type'      => 'dealer',
	    'posts_per_page' => $postsPerPage,
	    'publish_status' => 'published',
	 );
    $query = new WP_Query($args);
    if($query->have_posts()) : ?>
    	<div class="outer">
    		<?php while($query->have_posts()) :
		        $query->the_post() ; ?>
		        <div class="inner">
		        	<div class="img"><?php echo get_the_post_thumbnail();?></div>
		        	<div class="title"><?php echo get_the_title();?></div>
		        </div>
	        <?php endwhile;?>
    	</div>
    	<div class="load-cls">
    		<a href="javascript:void(0);" class="load-more">Load More</a>
    	</div>
        <?php wp_reset_postdata();
    endif; ?>
    <?php   
    return ob_get_clean();
}
add_shortcode( 'dealer-list', 'shortcode_dealer_post_type' ); 
 
/* End Shortcode */

/*Ajax call function */

add_action('wp_ajax_nopriv_dealer_list', 'dealer_list');
add_action('wp_ajax_dealer_list', 'dealer_list');

function dealer_list(){

	$ppp = (isset($_POST["ppp"])) ? $_POST["ppp"] : 3;
    $page = (isset($_POST['pageNumber'])) ? $_POST['pageNumber'] : 0;
    $is_cat = 0;
    $hide = 0;
    if($_POST["cat_id"]){
    	$is_cat = 1;
    	$args = array(
            'post_status' => 'publish',
            'post_type' => 'dealer',
            'posts_per_page' => $ppp,
	          'paged'    => $page,
            'tax_query' => array(
	            array(
	                'taxonomy' => 'dealer_category',
	                'field' => 'term_id',
	                'terms' => $_POST["cat_id"],
	            ),
	        ),
        );
    }else{
    	$args = array(
		    'post_type'      => 'dealer',
		    'publish_status' => 'published',
		    'posts_per_page' => $ppp,
	        'paged'    => $page
		 );
    }
    
    $query = new WP_Query($args);
    $out = '';

    if ($query -> have_posts()) :  
    	while ($query -> have_posts()) : 
    		$query -> the_post();
        	$out .= '<div class="inner"><div class="img">'.get_the_post_thumbnail().'</div><div class="title">'.get_the_title().'</div></div>';
    	endwhile;
    else :
    	$hide = 1;
    	$out .= '<p style="flex: 0 0 100%;">No more dealer</p>';
    endif;
    wp_reset_postdata();
    header("Content-type:application/json");
	wp_send_json(['result' => $out, 'is_cat' => $is_cat, 'hide_btn' => $hide]);
	exit();
}
/*End ajax call back function*/

