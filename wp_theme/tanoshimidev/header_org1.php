<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package TsunaguMiyagi
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
	
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">

</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'tsunagumiyagi' ); ?></a>

  <div style="margin: 0.25rem 0.75rem 0 0.75rem;">
	 
	 <nav style="display:flex; flex-direction:row;  justify-content: space-between;">
        <div>
          <a class="navbar-item" href="./events">
           <img src="<?php echo get_template_directory_uri(); ?>/image/logo200.png" width="160" height="128" style="max-height:100%;">
          </a>
   
        </div>
        
        
        <div style="padding-top:0.75rem;">
            <a class="button is-light" style="font-size:0.65rem; margin:0rem; padding:0.2rem;">
              開催中のイベント
            </a>
            <a class="button is-light" style="font-size:0.65rem; margin:0rem; padding:0.25rem;">
              終了したイベント
            </a>
        </div>
  	</nav>
  </div>


			

