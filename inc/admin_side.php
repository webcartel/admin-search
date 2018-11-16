<?php

add_action('admin_menu', function() {
	$page = add_menu_page(
		'Search',
		'Search', 
		'manage_options',
		'admin_search',
		'admin_search_admin',
		'dashicons-search',
		100
	);

	add_action( 'admin_print_styles-' . $page, 'admin_search_admin_css' );
	add_action( 'admin_print_scripts-' . $page, 'admin_search_admin_js' );
});

// Добавляет ссылку в админ бар
// add_action( 'admin_bar_menu', 'my_admin_bar_menu', 300 );
// function my_admin_bar_menu( $wp_admin_bar ) {
// 	$wp_admin_bar->add_menu( array(
// 		'id'    => 'menu_id',
// 		'title' => 'Search',
// 		'href'  => '#',
// 	) );
// }

// Регистрация скриптов и стилей админки
function admin_search_admin_css()
{
	wp_enqueue_style( 'admin-search-admin-font', 'https://fonts.googleapis.com/css?family=Roboto:400,500&amp;subset=cyrillic' );
	wp_enqueue_style( 'admin-search-admin-css', WCST_ADMIN_SEARCH_PLUGIN_DIR_URL . 'css/app-admin.css' );
	// wp_enqueue_style( 'admin_searchusercss', WCST_ADMIN_SEARCH_PLUGIN_DIR_URL . 'css/main.css' );
}

function admin_search_admin_js()
{ 
	wp_enqueue_script('admin-search-admin-axios', 'https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.js', array(), null, 'in_footer');
	wp_enqueue_script('admin-search-admin-vue', 'https://cdn.jsdelivr.net/npm/vue/dist/vue.js', array(), null, 'in_footer');
	wp_enqueue_script('admin-search-admin-app', WCST_ADMIN_SEARCH_PLUGIN_DIR_URL . 'js/app-admin.js', array('admin-search-admin-vue'), null, 'in_footer');
	// wp_enqueue_script('admin_searchuserjs', WCST_ADMIN_SEARCH_PLUGIN_DIR_URL . 'js/main.js', array('jquery'), null, 'in_footer');
}

function admin_search_admin()
{
	echo '
<div id="admin-search" class="admin-search">
	<div class="search-input-block">
		<input type="text" name="query" class="search-input" placeholder="Search" @keyup="sendQuery()" v-model="query">
	</div>

	<div class="search-loader" v-if="waiting">
		<span></span>
		<span></span>
		<span></span>
		<span></span>
		<span></span>
	</div>

	<div class="search-results-block" v-if="searchResults != null">

		<div class="search-result" v-for="result in searchResults">
			<div class="title">
				<a href="#" v-html="result.post_title"></a>
			</div>
			<div class="snippet">
				<p v-html="result.post_content"></p>
			</div>
		</div>

	</div>
</div>
';
}



function wcst_admin_search() {
	if ( !empty($_POST['query']) && mb_strlen($_POST['query']) >= 3 ) {
		sleep(3);

		global $wpdb;

		$search_query = trim(mb_strtolower($_POST['query']));

		$search_results = $wpdb->get_results("SELECT ID, post_title, post_content FROM $wpdb->posts WHERE `post_title` LIKE '%".$search_query."%' AND post_type = 'page' AND post_status = 'publish'", ARRAY_A);

		foreach ($search_results as $key => $result) {
			$post_title = preg_replace('/'.$search_query.'/Uis', '<strong class="marker">${0}</strong>', strip_tags($result['post_title']));
			$post_content = preg_replace('/'.$search_query.'/Uis', '<strong class="marker">${0}</strong>', strip_tags($result['post_content']));
			$search_results[$key]['post_title'] = $post_title;
			$search_results[$key]['post_content'] = $post_content;
		}

		echo json_encode($search_results);
		exit();
	}
}
add_action( 'wp_ajax_wcst_admin_search', 'wcst_admin_search' );
