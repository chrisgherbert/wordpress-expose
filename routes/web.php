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

Route::get('/wordpress/users', function(){

	$url = $_GET['url'] ?? '';
	$page = $_GET['page'] ?? 1;

	$data = [
		'url' => $url,
		'page' => $page,
		'form' => 'users'
	];

	if (!$url){
		return view('users', $data);
	}

	try {

		// Set the site data (title, REST urls, etc)
		$data['site']  = App\Data\WordPress\Site::get_cached($url);

		// Set the site's users/authors data
		$data['items'] = App\Data\WordPress\UsersCollection::get_cached(
			$url,
			$data['site']->get_users_endpoint_url(),
			$page
		);

	} catch (Exception $e) {
		$data['error'] = $e->getMessage();
	}

	return view('users', $data);

});

Route::get('/wordpress/comments', function(){

	$url = $_GET['url'] ?? '';
	$page = $_GET['page'] ?? 1;

	$data = [
		'url' => $url,
		'page' => $page,
		'form' => 'comments'
	];

	if (!$url){
		return view('comments', $data);
	}

	try {

		// Set the site data (title, REST urls, etc)
		$data['site']  = App\Data\WordPress\Site::get_cached($url);

		// Set the site's users/authors data
		$data['items'] = App\Data\WordPress\CommentsCollection::get_cached(
			$url,
			$data['site']->get_comments_endpoint_url(),
			$page
		);

	} catch (Exception $e) {
		$data['error'] = $e->getMessage();
	}

	return view('comments', $data);

});


Route::get('/gravatar', function(){

	$url = $_GET['url'] ?? '';

	$data = [
		'url' => $url,
		'form' => 'gravatar'
	];

	if (!$url){
		return view('gravatar', $data);
	}

	try {

		$gravatar = new App\Data\Gravatar($url);

		$data['hash'] = $gravatar->get_hash();
		$data['email'] = $gravatar->get_email();

	} catch (Exception $e) {
		$data['error'] = $e->getMessage();
	}

	return view('gravatar', $data);

});
