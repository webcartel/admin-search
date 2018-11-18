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

	<div class="search-block">
		<div class="search-input-block">
			<input type="text" name="query" class="search-input" placeholder="Search" autocomplete="off" autofocus @keyup="sendQuery()" v-model="query">
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
					<a :href="result.post_url" v-html="result.post_title"></a>
					<span class="post_type">{{ result.post_type }}</span>
				</div>
				<div class="snippet">
					<p v-html="result.post_content"></p>
				</div>
			</div>

		</div>
	</div>

	<div class="settimgs-block">
		<div class="select-post-types">
			<div class="post-type" v-for="type in postTypes">
				<span class="post-type-checkbox" :class="{ active: type.is_active }" @click="type.is_active ? type.is_active = false : type.is_active = true"></span>
				<span class="post-type-label">{{ type.name }}</span>
			</div>
		</div>
	</div>

</div>
';
}



function wcst_admin_search_post_types()
{
	$post_types = get_post_types(array('public' => true, '_builtin' => false), 'names', 'or');
	foreach ($post_types as $key => $post_type) {
		$n_post_types[$key]['name'] = $post_type;
		$n_post_types[$key]['is_active'] = true;
	}

	echo json_encode( $n_post_types );
	exit();
}
add_action( 'wp_ajax_wcst_admin_search_post_types', 'wcst_admin_search_post_types' );



function wcst_admin_search() {
	$_POST = json_decode(file_get_contents('php://input'), true);

	if ( !empty($_POST['query']) && mb_strlen($_POST['query']) >= 3 ) {

		foreach ($_POST['post_types'] as $type) {
			if ( $type['is_active'] === true ) {
				$post_type_array[] = $type['name'];
			}
		}
		$post_type = implode("', '", $post_type_array);

		global $wpdb;

		$search_query = mb_strtolower($_POST['query']);

		$search_results = $wpdb->get_results("SELECT ID, post_title, post_content FROM $wpdb->posts WHERE post_title LIKE '%".$search_query."%' AND post_type IN ('".$post_type."') AND post_status = 'publish'", ARRAY_A);

		foreach ($search_results as $key => $result) {
			// title
			$post_title = mb_eregi_replace($search_query, '<strong class="marker">\\0</strong>', $result['post_title'] );
			$search_results[$key]['post_title'] = $post_title;
			// ----
			
			// content
			$post_content_source = mb_eregi_replace('[\r\n]*', '', $result['post_content']);
			$post_content_source = mb_eregi_replace('(<[^>]*>)', '', $post_content_source);

			preg_match_all('/(.{0,40}'.$search_query.'.{0,40})/ui', $post_content_source, $post_content_parts);
			$post_content = '';
			foreach (array_slice($post_content_parts[1], 0, 3, true) as $part) {
				$post_content .= ' ...'.mb_eregi_replace($search_query, '<strong class="marker">\\0</strong>', $part).'... ';
			}

			$search_results[$key]['post_content'] = $post_content;
			// ----

			// edit_url
			$search_results[$key]['post_url'] = get_edit_post_link($result['ID'], '');
			// ----

			$search_results[$key]['post_type'] = get_post_type($result['ID']);
		}

		echo json_encode($search_results);
		exit();
	}
}
add_action( 'wp_ajax_wcst_admin_search', 'wcst_admin_search' );
