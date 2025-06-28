<?php 

if (!logged_in()) {
	message("You must be logged in");
	redirect('login');
}

$section = $URL[1] ?? "dashboard";
$action  = $URL[2] ?? null;
$id      = $URL[3] ?? null;

switch ($section) {
	case 'dashboard':
		require page('user/dashboard');
		break;

	case 'playlists':
		require page('user/playlists');
		break;

	case 'profile':
		require page('user/profile');
		break;

	default:
		require page('user/404');
		break;
}
