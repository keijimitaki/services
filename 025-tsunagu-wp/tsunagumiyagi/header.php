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
	 
	 <nav>
	 	<div class="columns" style="display:flex; flex-direction:row;  justify-content: center">
        
	        <div class="column" style="display:flex; flex-direction:row;  justify-content: start; width:60px; flex-grow:0.2;">
	          <a class="navbar-item" href="./events">
	           <img src="<?php echo get_template_directory_uri(); ?>/image/IMG_5040.png" width="60" height="60" style="max-height:100%;">
	          </a>
	   
	        </div>
	        
	        <div class="column is-align-self-center" style="display:flex; flex-direction:row;  justify-content:center;  flex-grow:1;">
	        	<div style="width:240px; text-align:center;">
	        	<span style="font-size:1.25rem;font-family: a-otf-ud-reimin-pr6n, sans-serif;font-style: normal;font-weight: 300;">宮城の子育て情報サイト</span><br>
	        	<span style="font-size:1.25rem;font-family: futura-pt, sans-serif;font-style: normal;font-weight: 800;">TSUNAGU MIYAGI</span>
	        </div>
	        </div>
	        
	        
	        <div class="column is-hidden-touch" style="display:flex; flex-direction:row;  justify-content: end;  flex-grow:0.2; 
	        	font-family: a-otf-ud-reimin-pr6n, sans-serif;font-style: normal;font-weight: 300;">

	            <div>
	            <a class="navbar-item" href="./">
	              ホーム
	            </a>
	            </div>
	      
	            <div>
	              <span class="navbar-item is-hoverable p-0 m-0">
	              <a class="navbar-link is-arrowless">
	                イベント
	              </a>
	      
	              <div class="navbar-dropdown">
	                <a class="navbar-item" href="./events/">
	                  開催中のイベント
	                </a>
	                <a class="navbar-item" href="./events-closed/">
	                  終了したイベント
	                </a>
	              </div>
	              </span>
	            </div>
	            
	            <div>
	              <a class="navbar-item">
	                施設
	              </a>
	            </div>
	            
	            
	        </div>
	        
	        <div class="column is-hidden-desktop" style="display:flex; flex-direction:row;  justify-content: end;  flex-grow:0.2;">
	          <a role="button" class="navbar-burger" data-target="navbarBasicExample" aria-label="menu" aria-expanded="false">
	            <span aria-hidden="true"></span>
	            <span aria-hidden="true"></span>
	            <span aria-hidden="true"></span>
	          </a>
	        </div>
        
        
        </div>

		<div id="mobile-menu" class="columns mt-0 pt-0 mb-5 pb-5" style="display:flex; flex-direction:column;  justify-content: start; display:none;
			font-family: a-otf-ud-reimin-pr6n, sans-serif;font-style: normal;font-weight: 300;">
	        
	        <div class="px-2 mx-2">
	        
	            <div>
	            <a class="navbar-item" href="./">
	              ホーム
	            </a>
	            </div>
	      
	            <div>
	              <span class="navbar-item has-dropdown is-hoverable p-0 m-0">
	              <a class="navbar-link is-arrowless">
	                イベント
	              </a>
	      
	              <div class="navbar-dropdown">
	                <a class="navbar-item" href="./events/">
	                  開催中のイベント
	                </a>
	                <a class="navbar-item" href="./events-closed/">
	                  終了したイベント
	                </a>
	              </div>
	              </span>
	            </div>
	            
	            <div>
	              <a class="navbar-item">
	                施設
	              </a>
	            </div>
		
	        </div>
        </div>

  	
  	</nav>
  	
  	
  	
  	
  	

    
  	
  </div>

  <script>
document.addEventListener('DOMContentLoaded', () => {
  
  let mobileMenuActive = false;

  // Get all "navbar-burger" elements
  const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);

  // Add a click event on each of them
  $navbarBurgers.forEach( el => {
    el.addEventListener('click', () => {

      //window.alert('click');
      
      
      
      // Get the target from the "data-target" attribute
      //const target = el.dataset.target;
      //const $target = document.getElementById(target);

      // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
      el.classList.toggle('is-active');
      
      //$target.classList.toggle('is-active');
      
      mobileMenuActive = !mobileMenuActive;
      displayStyle='none';
      if(mobileMenuActive){
      	displayStyle="block";      
      }
      document.getElementById('mobile-menu').style.display = displayStyle;

    });
    
    
  });
  

});

  (function(d) {
    var config = {
      kitId: 'upo3tsm',
      scriptTimeout: 3000,
      async: true
    },
    h=d.documentElement,t=setTimeout(function(){h.className=h.className.replace(/\bwf-loading\b/g,"")+" wf-inactive";},config.scriptTimeout),tk=d.createElement("script"),f=false,s=d.getElementsByTagName("script")[0],a;h.className+=" wf-loading";tk.src='https://use.typekit.net/'+config.kitId+'.js';tk.async=true;tk.onload=tk.onreadystatechange=function(){a=this.readyState;if(f||a&&a!="complete"&&a!="loaded")return;f=true;clearTimeout(t);try{Typekit.load(config)}catch(e){}};s.parentNode.insertBefore(tk,s)
  })(document);

  </script>


			

