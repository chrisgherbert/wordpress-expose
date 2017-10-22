<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
	return view('home');
});

Route::get('/wordpress/users', function($url, $page = null){

	$url = $_GET['url'];
	$page = $_GET['page'] ?? 1;

	$data = [
		'url' => $url,
		'page' => $page,
	];

	try {

		// Set the site data (title, REST urls, etc)
		$data['site']  = new App\Data\WordPress\Site($url);

		// Set the site's users/authors data
		$data['items'] = new App\Data\WordPress\UsersCollection(
			$url,
			$data['site']->get_users_endpoint_url(),
			$page
		);

	} catch (Exception $e) {
		$data['error'] = $e->getMessage();
	}

	return view('users', $data);

});

Route::get('/wordpress/comments', function($url, $page = null){

	$url = $_GET['url'];
	$page = $_GET['page'] ?? 1;

	$data = [
		'url' => $url,
		'page' => $page,
	];

	try {

		// Set the site data (title, REST urls, etc)
		$data['site']  = new App\Data\WordPress\Site($url);

		// Set the site's users/authors data
		$data['items'] = new App\Data\WordPress\CommentsCollection(
			$url,
			$data['site']->get_comments_endpoint_url(),
			$page
		);

	} catch (Exception $e) {
		$data['error'] = $e->getMessage();
	}

	return view('comments', $data);

});

Route::get('/wordpress/{url}/post-comments/{page?}', function($url, $page = null){
	// to do
});

Route::get('/gravater/{$url}', function($url){
	// to do
});