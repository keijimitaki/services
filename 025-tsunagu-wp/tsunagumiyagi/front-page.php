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

  	<div class="container is-fluid mx-1 px-1">

	<main>

    <?php
		$args = array(
		   	'post_type' => 'tmevent',
		  	'page' => 1, 
		    'posts_per_page' => 3, // サブループなので設定
		    'meta_query' => array(
		    	array(
		    		'key'=>'event_next_date',
		    		'type'=> 'char',
		    	),
		    	array(
		    		'key'=>'event_end_flag',
		    		'value'=> '0',
		    		'compare'=>'=',
		    	),
		    ),
		    
		);
	    
	    $the_query = new WP_Query($args);
    
    ?>

    <div class="container">

      <div class="column" >
        <img src="<?php echo get_template_directory_uri(); ?>/image/1-5.jpeg" alt="Placeholder image" />
        <!-- <img src="image0.png" alt="Placeholder image" style="max-width:1600x; max-height:600px;"/> -->
      </div>
      
      
      <div class="column">
		<div class="box16">
		    <p class="m-1 p-4">TSUNAGU MIYAGIは、『宮城を子育てしやすい街に』を目標に宮城の子育て世代と子育て支援をする皆様をサポートしいたします。</p>
		</div>
      </div>
      
      <div class="column">
      	<div class="is-flex is-justify-content-center" style="height:30px;">
			<div class="happyline"><hr class="left"></div>
			<div class="is-flex is-align-content-center is-align-items-end"><span style="font-size:1.125rem">親子で行ける</span></div>
			<div class="happyline"><hr class="right"></div>
      	</div>
      	<div class="is-flex is-justify-content-center">
      		<span style="font-size:1.25rem">イベント情報</span>
      	</div>
      </div>

      <div class="column">
	      <div class="columns is-mobile">
	      
		    <?php while($the_query->have_posts()) : $the_query->the_post(); ?>

	        <div class="card column mx-0 px-0"> 
	            <div class="column card-image mx-0 px-1">
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
	            <div class="card-content mx-1 px-1"  style="font-size:0.75rem;">
		            <div class="media mx-0 px-0">
		                <div class="media-left">
		                </div>
		                <div class="media-content">
		                <?php echo nl2br(esc_html(get_field('name'))); ?>
		                <p class="tmevent-list-date"><?php echo (get_field('date')); ?> </p>
		                <?php echo nl2br(esc_html(get_field('title'))); ?>
		                </div>
		            </div>
	        
		            <div class="content mx-0 px-0">
		            	<?php echo nl2br(esc_html(get_field('summary'))); ?>
		                <br>
		                <time datetime="2016-1-1"><?php echo nl2br(esc_html(get_field('area'))); ?></time>
		            </div>
		            
		        </div>
		        
	        </div>      
	        <?php endwhile;  wp_reset_postdata(); ?>

	      </div>
      </div>

      <div class="column">
			<div  class="is-flex is-justify-content-end">
	          <a href="https://tsunagu-miyagi.info/events">
	          	<div  class="is-flex">
		          	<div><u>イベント一覧はこちら</u></div>
		          	<div><img class="px-2" src="<?php echo get_template_directory_uri(); ?>/image/advance.png" alt="arrow_right" /></div>
	            </div>
	          </a>
          </div>
      </div>

    </div>

    <div class="container mt-3">

      
      <div class="column">
      	<div class="is-flex is-justify-content-center" style="height:30px;">
			<div class="happyline"><hr class="left"></div>
			<div class="is-flex is-align-content-center is-align-items-end"><span style="font-size:1.125rem">子供と一緒に行ける</span></div>
			<div class="happyline"><hr class="right"></div>
      	</div>
      	<div class="is-flex is-justify-content-center">
      		<span style="font-size:1.25rem">施設・公園・お店情報</span>
      	</div>
      </div>

      <div class="column">
	      <div class="columns is-mobile">
	      
		    <?php while($the_query->have_posts()) : $the_query->the_post(); ?>

	        <div class="card column mx-0 px-0"> 
	            <div class="column card-image mx-0 px-1">
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
	            <div class="card-content mx-1 px-1"  style="font-size:0.75rem;">
		            <div class="media mx-0 px-0">
		                <div class="media-left">
		                </div>
		                <div class="media-content">
		                <?php echo nl2br(esc_html(get_field('name'))); ?>
		                <p class="tmevent-list-date"><?php echo (get_field('date')); ?> </p>
		                <?php echo nl2br(esc_html(get_field('title'))); ?>
		                </div>
		            </div>
	        
		            <div class="content mx-0 px-0">
		            	<?php echo nl2br(esc_html(get_field('summary'))); ?>
		                <br>
		                <time datetime="2016-1-1"><?php echo nl2br(esc_html(get_field('area'))); ?></time>
		            </div>
		            
		        </div>
		        
	        </div>      
	        <?php endwhile;  wp_reset_postdata(); ?>

	      </div>
      </div>

      <div class="column">
			<div  class="is-flex is-justify-content-end">
	          <a href="https://tsunagu-miyagi.info/events">
	          	<div  class="is-flex">
		          	<div><u>一覧はこちら</u></div>
		          	<div><img class="px-2" src="<?php echo get_template_directory_uri(); ?>/image/advance.png" alt="arrow_right" /></div>
	            </div>
	          </a>
          </div>
      </div>

    </div>
	
	
	
    <div class="container mt-3">

		SNSリンク
		
    </div>	

	</main><!-- #main -->



	</div>

<?php
get_sidebar();
get_footer();
