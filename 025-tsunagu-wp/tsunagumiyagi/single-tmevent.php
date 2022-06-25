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


	<main id="primary" class="site-main">
	
	<div class="container">

		<?php while(have_posts()) :the_post(); ?>

			<h1 class="title"><?php echo nl2br(esc_html(get_field('name'))); ?></h1>
			<p class="subtitle"><?php echo nl2br(esc_html(get_field('title'))); ?></p>

		    <div class="tile is-ancestor">
		      <div class="tile is-parent ">
		      
		        <article class="tile is-child">
		          
					<?php $eventImage = get_field('image'); ?>
					
					<!--
					<?php foreach($eventImage as $key=>$value ): ?>		
						<?php echo ('key='.$key.' val='.$value); ?>
					<?php endforeach; ?>
					-->
					
					<?php if ($eventImage) : ?>
						<?php
							$img_attr = wp_get_attachment_image_src($eventImage['url']);
						?>				
					
						<div class="card">
						  <div class="card-image" >
						      <img src="<?php echo($eventImage['url']) ; ?>" alt="Placeholder image"
						       style="width:640;height:480px;" >
						  </div>
						</div>


					<?php endif; ?>
		          
		          
		        </article>
		      </div>

		      <div class="tile is-parent is-vertical">
		        
		        <article class="tile is-child notification">

			        <div class="block">
						<span class="tag is-info is-light is-large"> 情報 </span>
			        </div>


					<table class="table" style="table-layout: fixed;">
					  <tbody>
					    <tr>
					      <th style="width:110px;">イベント名</th>
					      <td><?php echo nl2br(esc_html(get_field('name'))); ?></td>
					    </tr>
					    <tr>
					      <th>概要</th>
					      <td><?php echo nl2br(esc_html(get_field('summary'))); ?></td>
					    </tr>
					    <tr>
					      <th>開催場所</th>
					      <td><?php echo nl2br(esc_html(get_field('area'))); ?></td>
					    </tr>
					    <tr>
					      <th>開催日</th>
					      <td><?php echo nl2br(esc_html(get_field('date'))); ?></td>
					    </tr>
					    <tr>
					      <th>主催者</th>
					      <td><?php echo nl2br(esc_html(get_field('organizer'))); ?></td>
					    </tr>
					    <tr>
					      <th>ご予約方法</th>
					      <td style="word-wrap: break-word;"><?php echo nl2br(esc_html(get_field('reservation_method'))); ?></td>
					    </tr>
					    <tr>
					      <th>ご予約URL</th>
					      <td style="word-wrap: break-word;"><a href="<?php echo get_field('reservation_url'); ?>" target="_blank" rel="noopener"><?php echo get_field('reservation_url'); ?></a></td>
					    </tr>
					    <tr>
					      <th>料金</th>
					      <td style="word-wrap: break-word;"><?php echo nl2br(esc_html(get_field('price'))); ?></td>
					    </tr>
					    <tr>
					      <th>人数</th>
					      <td style="word-wrap: break-word;"><?php echo nl2br(esc_html(get_field('number_of_peaple'))); ?></td>
					    </tr>
					    
					    <?php if (!empty(get_field('line_official_url'))) : ?>
					    <tr>
					      <th>LINE公式</th>
					      <td style="word-wrap: break-word;"><a href="<?php echo get_field('line_official_url'); ?>" target="_blank" rel="noopener"><?php echo get_field('line_official_url'); ?></a></td>
					    </tr>
					    <?php endif; ?>

					    <?php if (!empty(get_field('url'))) : ?>
					    <tr>
					      <th>Instagram</th>
					      <td style="word-wrap: break-word;"><a href="<?php echo get_field('url'); ?>" target="_blank" rel="noopener"><?php echo get_field('url'); ?></a></td>
					    </tr>
					    <?php endif; ?>

					  </tbody>
					</table>          

		        </article>
		        
		        
		      </div>
		    </div>

		    <div class="tile is-parent">
		      <article class="tile is-child notification ">
		        <div class="block">
					<span class="tag is-info is-light is-large"> イベント詳細 </span>
		        </div>
		        
		        <div class="block">
		          <?php echo nl2br(esc_html(get_field('detail'))); ?>
		        </div>
		        
		    </div>
		    
			
		    
		<?php endwhile; ?>

    </div>
  
	</main><!-- #main -->

<?php
get_sidebar();
get_footer();
