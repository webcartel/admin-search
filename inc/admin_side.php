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
add_action( 'admin_bar_menu', 'my_admin_bar_menu', 300 );
function my_admin_bar_menu( $wp_admin_bar ) {
	$wp_admin_bar->add_menu( array(
		'id'    => 'menu_id',
		'title' => 'Search',
		'href'  => '#',
	) );
}

// Регистрация скриптов и стилей админки
function admin_search_admin_css()
{
	wp_enqueue_style( 'admin_search-admin-font', 'https://fonts.googleapis.com/css?family=Roboto:400,500&amp;subset=cyrillic' );
	wp_enqueue_style( 'admin_search-admin-css', WCST_ADMIN_SEARCH_PLUGIN_DIR_URL . 'css/app-admin.css' );
	// wp_enqueue_style( 'admin_searchusercss', WCST_ADMIN_SEARCH_PLUGIN_DIR_URL . 'css/main.css' );
}

function admin_search_admin_js()
{ 
	wp_enqueue_script('admin_search-admin-axios', 'https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.js', array(), null, 'in_footer');
	wp_enqueue_script('admin_search-admin-vue', 'https://cdn.jsdelivr.net/npm/vue/dist/vue.js', array(), null, 'in_footer');
	wp_enqueue_script('admin_search-admin-app', WCST_ADMIN_SEARCH_PLUGIN_DIR_URL . 'js/app-admin.js', array('admin_search-admin-vue'), null, 'in_footer');
	// wp_enqueue_script('admin_searchuserjs', WCST_ADMIN_SEARCH_PLUGIN_DIR_URL . 'js/main.js', array('jquery'), null, 'in_footer');
}

function admin_search_admin()
{
	echo '
<div id="admin-search-admin" class="admin-search-admin">
	<div class="search-input-block">
		<input type="text" name="query" class="">
	</div>
</div>
';
}



function wcst_admin_search() {
	if ( !empty($_POST['query']) ) {



		echo json_encode($data);
		exit();
	}
}
add_action( 'wp_ajax_wcst_admin_search', 'wcst_admin_search' );
