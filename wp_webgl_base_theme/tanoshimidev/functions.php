<?php
/**
 * Tanoshimidev functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Tanoshimidev
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function tanoshimidev_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on TsunaguMiyagi, use a find and replace
		* to change 'tanoshimidev' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'tanoshimidev', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'tanoshimidev' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'tsunagumiyagi_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'tanoshimidev_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function tanoshimidev_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'tanoshimidev_content_width', 640 );
}
add_action( 'after_setup_theme', 'tanoshimidev_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function tanoshimidev_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'tanoshimidev' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'tsunagumiyagi' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
//add_action( 'widgets_init', 'tanoshimidev_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function tanoshimidev_scripts() {
	wp_enqueue_style( 'tanoshimidev-style', get_theme_file_uri('css/style.css'), array(), _S_VERSION );
	wp_style_add_data( 'tanoshimidev-style', 'rtl', 'replace' );

	wp_enqueue_style( 'tanoshimidev-style-custom-reset', get_theme_file_uri('css/reset.css'), array(), _S_VERSION );
	wp_enqueue_style( 'tanoshimidev-style-custom-parts', get_theme_file_uri('css/parts.css'), array(), _S_VERSION );
	wp_enqueue_style( 'tanoshimidev-style-custom-layout', get_theme_file_uri('css/layout.css'), array(), _S_VERSION );


	wp_enqueue_script( 'tanoshimidev-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	wp_enqueue_script( 'tanoshimidev-script', get_template_directory_uri() . '/js/script.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'tanoshimidev-script-webgl1', get_template_directory_uri() . '/js/webgl_minMatrix.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'tanoshimidev-script-webgl2', get_template_directory_uri() . '/js/webgl_script.js', array(), _S_VERSION, true );
	

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'tanoshimidev_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

// 自動実行用関数
function tanoshimidev_update_next_event_date() {

	global $wpdb;
	$rows = $wpdb->get_results("
	SELECT ". $wpdb->prefix. "posts.id
	,mt1.meta_key AS mt1_key ,mt1.meta_value AS mt1_val
	,mt2.meta_key AS mt2_key ,mt2.meta_value AS mt2_val
	,mt3.meta_key AS mt3_key ,mt3.meta_value AS mt3_val
	FROM " .$wpdb->prefix. "posts
	INNER JOIN " .$wpdb->prefix. "postmeta AS mt1 ON ". $wpdb->prefix. "posts.id = mt1.post_id
	INNER JOIN " .$wpdb->prefix. "postmeta AS mt2 ON ". $wpdb->prefix. "posts.id = mt2.post_id
	LEFT OUTER JOIN ". $wpdb->prefix. "postmeta AS mt3 ON (". $wpdb->prefix. "posts.id = mt3.post_id AND mt3.meta_key = 'event_next_date')
	WHERE mt1.meta_key = 'event_end_flag' AND mt1.meta_value = '0'
	AND mt2.meta_key = 'date'
	", ARRAY_N);
	

	date_default_timezone_set('Asia/Tokyo');  // タイムゾーンの設定
	$today = date("Ymd");
	error_log(print_r($today . "\n" , true),"3", 'debug.log');
	
	foreach ($rows as $row) {
		
		//開催日を取得
		$date = $row[4];
		if(empty($date)){
			continue;
		}
		//IDを取得
		$id = $row[0];
		
		
		$dates = explode("、", $date);
	
		$end_flag = '0';
		$next_date = '';
		foreach ($dates as $dt) {
			
			//yyyy/mm/ddの形式でない場合、次の日付を取得
			if(strlen($dt) != 10){
				error_log(print_r("ID:".$id. "で指定された開催日". $dt . "が不正です。",true),"0", 'debug.log');
				continue;
			}
			
			
			$next_date = str_replace("/", "", $dt);
			
			//開催日が本日移行の場合
			if($next_date >= $today){
				break;
			} else {
				continue;
			}

			
		}

		if(!empty($next_date)){
			
			if($next_date < $today){
				//終了フラグを更新
				
				$rows = $wpdb->get_results("
					SELECT ". $wpdb->prefix. "posts.id
					,mt1.meta_key AS mt1_key ,mt1.meta_value AS mt1_val
					FROM " .$wpdb->prefix. "posts
					INNER JOIN " .$wpdb->prefix. "postmeta AS mt1 ON ". $wpdb->prefix. "posts.id = mt1.post_id
					WHERE mt1.meta_key = 'event_end_flag'
					", ARRAY_N);
				
				$end_flag = '1';
				if(empty($rows)){
					//INSERT
					$sql = $wpdb->prepare( "INSERT INTO ". $wpdb->prefix. "postmeta (meta_id, post_id, meta_key, meta_value) VALUES (NULL, %s, 'event_end_flag' , %s)" ,
						$id, $end_flag);
					$wpdb->query($sql);
					error_log(print_r("ID:".$id. "終了フラグを登録:" ,true),"0", 'debug.log');
				
				} else {
					//UPDATE
					$result = $wpdb->query("UPDATE ".$wpdb->prefix."postmeta SET meta_value=" .$end_flag. " WHERE post_id= ". $id ." AND meta_key='event_end_flag'");
					error_log(print_r("ID:".$id. "終了フラグを更新:". $result ."件" ,true),"0", 'debug.log');
				
				}
				
			
			
			} else {
				//次回開催日を更新
			
				$rows = $wpdb->get_results("
					SELECT ". $wpdb->prefix. "posts.id
					,mt1.meta_key AS mt1_key ,mt1.meta_value AS mt1_val
					FROM " .$wpdb->prefix. "posts
					INNER JOIN " .$wpdb->prefix. "postmeta AS mt1 ON ". $wpdb->prefix. "posts.id = mt1.post_id
					WHERE mt1.meta_key = 'event_next_date'
					", ARRAY_N);
				
				if(empty($rows)){
					//INSERT
					$sql = $wpdb->prepare( "INSERT INTO ". $wpdb->prefix. "postmeta (meta_id, post_id, meta_key, meta_value) VALUES (NULL, %s, 'event_next_date' , %s)" ,
						$id, $next_date);
					$wpdb->query($sql);
					error_log(print_r("ID:".$id. "次回開催日を登録:" ,true),"0", 'debug.log');
					
				} else {
					//UPDATE
					$result = $wpdb->query("UPDATE ".$wpdb->prefix."postmeta SET meta_value=". $next_date ." WHERE post_id= ". $id ." AND meta_key='event_next_date'");
					error_log(print_r("ID:".$id. "次回開催日を更新:". $result ."件" ,true),"0", 'debug.log');
				
				}
				
			
			}

			
		}
		

	}
	
	
}

add_action ( 'tsunagu_auto_function_cron', 'tsunagu_update_next_event_date' );


// cron登録処理
if ( !wp_next_scheduled( 'tsunagu_auto_function_cron' ) ) {  // 何度も同じcronが登録されないように
  date_default_timezone_set('Asia/Tokyo');  // タイムゾーンの設定
  wp_schedule_event( strtotime('2022-05-28 14:13:00'), 'daily', 'tsunagu_auto_function_cron' );
}
