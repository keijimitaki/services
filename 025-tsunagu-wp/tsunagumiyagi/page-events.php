<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package TsunaguMiyagi
 */

get_header();
?>

  <div class="container is-fluid">

	<!-- <main id="primary" class="site-main"> -->
	<main>

    <!-- repeat articles  --> 
    <div class="columns is-multiline is-desktop">
    
    <?php
    
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array(
   	'post_type' => 'tmevent',
	'paged' => $paged, // サブループなので設定
  	'page' => $paged,    
    'posts_per_page' => 9, // サブループなので設定
    'meta_query' => array(
    	array(
    		'key'=>'event_next_date',
    		'type'=> 'char',
    	),
//    	array(
//    		'key'=>'event_disp_order',
//    		'type'=> 'numeric',
//    	),
    	array(
    		'key'=>'event_end_flag',
    		'value'=> '0',
    		'compare'=>'=',
    	)
    ),
    'orderby' => 'event_next_date',
    'order' => 'ASC',
     
);
    
    $the_query = new WP_Query($args);
    
    ?>
    
    
		<?php while($the_query->have_posts()) : $the_query->the_post(); ?>


        <div class="card column is-one-third-desktop"> 
            <div class="column card-image">
            <a href="<?php the_permalink(); ?>">
            
	            <figure class="image">
					<?php $eventImage = get_field('image'); ?>
					
					<?php if ($eventImage) : ?>

						<?php
							$img_attr = wp_get_attachment_image_src($eventImage['url']);
						?>				
					
				      	<img src="<?php echo($eventImage['url']) ; ?>" alt="Placeholder image" style="width:100%;height:100%;" >
				     
		            <?php else: ?>
		              	<img src="https://bulma.io/images/placeholders/1280x960.png" alt="Placeholder image">

					<?php endif; ?>
	              
	            </figure>
	            
            </a>
            </div>
            <div class="card-content">
            <div class="media">
                <div class="media-left">
                </div>
                <div class="media-content">
                <p class="title is-4"><?php echo nl2br(esc_html(get_field('name'))); ?></p>
                <p class="subtitle is-6 tmevent-list-date"><?php echo (get_field('date')); ?> </p>
                <p class="subtitle is-6"><?php echo nl2br(esc_html(get_field('title'))); ?></p>
                </div>
            </div>
        
            <div class="content">
            	<?php echo nl2br(esc_html(get_field('summary'))); ?>
                <br>
                <time datetime="2016-1-1"><?php echo nl2br(esc_html(get_field('area'))); ?></time>
            </div>
            </div>
        </div>
			
		<?php endwhile;  wp_reset_postdata(); ?>

	</div>
	
	
	
	<nav class="events_page_nav" >
          <ul class="page-list">
<?php
echo paginate_links([
  'total' => $the_query->max_num_pages, 
  'current' => max( 1, $paged ),
]);

?>	

            </ul>
	</nav>
	

	</main><!-- #main -->



</div>

<?php
get_sidebar();
get_footer();
