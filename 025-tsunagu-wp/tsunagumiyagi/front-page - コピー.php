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
        <img src="<?php echo get_template_directory_uri(); ?>/image/image0.png" alt="Placeholder image" />
        <!-- <img src="image0.png" alt="Placeholder image" style="max-width:1600x; max-height:600px;"/> -->
      </div>

      <div class="column">
        <article class="message is-info">
          <div class="message-body">
            イベント開催中！！&nbsp;&nbsp;&nbsp;一覧は<a href="https://tsunagu-miyagi.info/events">こちら</a>
          </div>
        </article>
      </div>

      <div class="column">
        <?php while($the_query->have_posts()) : $the_query->the_post(); ?>
	        <div>
	          <span class="tag is-info">New</span>
	          <span class="has-text-weight-bold" ><a href="<?php the_permalink(); ?>"><?php echo nl2br(esc_html(get_field('name'))); ?></a></span>
	          <span class="tmevent-list-date mx-2 px-2"><?php echo (get_field('date')); ?></span>
	          <span><?php echo nl2br(esc_html(get_field('title'))); ?></span>
	        </div>
        <hr/>
        <?php endwhile;  wp_reset_postdata(); ?>


      </div>


    </div>

	  <div class="container mt-3">
      <div class="column">
        <img src="<?php echo get_template_directory_uri(); ?>/image/envato_yoga.png" alt="Placeholder image"/>
      </div>
      
      <div class="column">
        <article class="message is-success">
          <div class="message-body">
            ブログ一覧は<a>こちら</a>
          </div>
        </article>
      </div>

      <div class="column">

        <article class="media">
          <figure class="media-left">
            <p class="image is-64x64">
              <img src="https://bulma.io/images/placeholders/128x128.png">
            </p>
          </figure>
          <div class="media-content">
            <div class="content">
              <p>
                <strong>タイトル</strong> <small>2022.06.01</small> <small>31m</small>
                <br>
                サンプルテキストサンプルテキストサンプルテキストサンプルテキストサンプルテキストサンプルテキスト
              </p>
            </div>
          </div>

        </article> 
        
        <hr>
        <article class="media">
          <figure class="media-left">
            <p class="image is-64x64">
              <img src="https://bulma.io/images/placeholders/128x128.png">
            </p>
          </figure>
          <div class="media-content">
            <div class="content">
              <p>
                <strong>タイトル</strong> <small>2022.06.01</small> <small>31m</small>
                <br>
                サンプルテキストサンプルテキストサンプルテキストサンプルテキストサンプルテキストサンプルテキスト
              </p>
            </div>
          </div>

        </article> 
        
        <hr>
        <article class="media">
          <figure class="media-left">
            <p class="image is-64x64">
              <img src="https://bulma.io/images/placeholders/128x128.png">
            </p>
          </figure>
          <div class="media-content">
            <div class="content">
              <p>
                <strong>タイトル</strong> <small>2022.06.01</small> <small>31m</small>
                <br>
                サンプルテキストサンプルテキストサンプルテキストサンプルテキストサンプルテキストサンプルテキスト
              </p>
            </div>
          </div>

        </article> 
                
      </div>

    </div>    
	
	
	
	

	</main><!-- #main -->



	</div>

<?php
get_sidebar();
get_footer();
