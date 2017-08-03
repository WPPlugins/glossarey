<?php
/**
 * @package Glossarey
 * @version 0.1
 **/
/*
Plugin Name: Glossarey
Plugin URI: 
Description: Glossary of your custom terms and terminology for wordpress. I am still working on this project, It needs a lot of improvements. Let me know if you have any ideas.
Version: 0.1
Author: Lubomir Herko
Author URI: https://www.facebook.com/lubomir.herko
*/


/** Including necessary resources (JS, CSS, puts JS tooltip starter at the end of </bod>) */
function gsry_load_scripts(){
  wp_enqueue_script( 'jquery-ui-core' );
  wp_enqueue_script( 'jquery-ui-widget' );
  wp_enqueue_script( 'jquery-ui-mouse' );
  wp_enqueue_script( 'jquery-ui-position' );
  wp_enqueue_script( 'jquery-ui-tooltip' );
  wp_register_style( 'lightness', plugins_url( '/assets/css/ui-lightness/lightness.css', __FILE__ ) );
  wp_enqueue_style( 'lightness' );
}
add_action( 'wp_enqueue_scripts', 'gsry_load_scripts' );

function gsry_listen_to_tooltips() { ?>
  <script type="text/javascript">
  jQuery(document).ready(function(){
    jQuery( "a.glossey_tooltip" ).tooltip({
      track: true
    });
  });
  </script>
<?php }
add_action( 'wp_footer', 'gsry_listen_to_tooltips' );

/* 
* Glossary terms post type 
**/ 
function gsry_glossary_term_posttype() {
  $labels = array(
    'name'                => 'Glossary entries',
    'singular_name'       => 'Glossary entry',
    'add_new'             => 'Add New',
    'add_new_item'        => 'Add new glossary entry',
    'edit_item'           => 'Edit entry',
    'new_item'            => 'New entry',
    'all_items'           => 'All entries',
    'view_item'           => 'View entry',
    'search_items'        => 'Search entry',
    'not_found'           => 'No entries found',
    'not_found_in_trash'  => 'No entries found in Trash',
    'parent_item_colon'   => '',
    'menu_name'           => 'Glossarey'
  );
  
  $args = array(
    'labels'        => $labels,
    'description'   => "Holds glossary entries with their explanations",
    'public'        => true,
    'menu_position' => 5,
    'supports'      => array('title', 'editor'),
    'has_archive'   => false,
    'menu_icon'     => plugins_url( '/assets/img/dictionary.png', __FILE__ )
  );
  register_post_type( 'glossary_term', $args );
}
add_action( 'init', 'gsry_glossary_term_posttype' );

/** 
* Generating tooltips based on shortcodes 
**/
function gsry_tooltip_shortcode( $atts, $keyword = null ) {
  global $wpdb;
  
  if ( $atts ) {
    # look up term specified by parameter
    $definition_id = $wpdb->get_var("SELECT ID from $wpdb->posts where lower(post_title) = '" . strtolower($atts['keyword']) . "' and post_type = 'glossary_term' and post_status = 'publish'");
  } else {
    # look up term specified by content
    $definition_id = $wpdb->get_var("SELECT ID from $wpdb->posts where lower(post_title) = '" . strtolower($keyword) . "' and post_type = 'glossary_term' and post_status = 'publish'");
  }
  
  if ( $definition_id ) {
    $definition = get_post( $definition_id );
    $output = "";
    $output .= "<a href='javascript:void(0);' class='glossey_tooltip' title='$definition->post_content'>$keyword</a>";
    return $output;
  } else {
    return $keyword;
  }
}
add_shortcode( 'term', 'gsry_tooltip_shortcode' );

/**
* Add 'Description' column to administration panel 
**/
function gsry_columns( $defaults ){
  unset($defaults['date']);
  $defaults['description'] = 'Description';
  return $defaults;
}
function gsry_custom_columns( $column_name ) {
  global $post;
  if ($column_name == 'description') {
    the_content();
  }
}
add_filter( 'manage_edit-glossary_term_columns', 'gsry_columns' );
add_action( 'manage_posts_custom_column', 'gsry_custom_columns' );

/**
* Add Options page to Glossey admin menu 
**/
function gsry_admin_options_page() {
  add_submenu_page( 'edit.php?post_type=glossary_term', "Options", "Options", 'manage_options', 'glossary-options', 'options_page_generator');
}
function options_page_generator() { ?>
  <div class="wrap">
    <div id="icon-options-general" class="icon32"><br/></div>
    <h2>Glossarey options</h2>
    <p>
      Coming very soon to version 0.2
    </p>
  </div>
<?php }
add_action( 'admin_menu', 'gsry_admin_options_page' );

/**
 * Adding a custom button to TinyMCE editor
 **/
function add_gsry_button() {
  if ( get_user_option( 'rich_editing' ) == 'true' ) {
    add_filter( 'mce_external_plugins', 'add_gsry_tinymce_plugin' );
    add_filter( 'mce_buttons', 'register_gsry_button' );
  }
}
add_action( 'init', 'add_gsry_button' );

function register_gsry_button( $buttons ){
  array_push( $buttons, "|", "glossarey" );
  return $buttons;
}

function add_gsry_tinymce_plugin( $plugin_array ){
  $plugin_array['glossarey'] = plugins_url( '/assets/js/editor_plugin.js', __FILE__ );
  return $plugin_array;
}

function my_refresh_mce($ver){
  $ver += 3;
  return $ver;
}

add_filter( 'tiny_mce_version', 'my_refresh_mce' );
?>