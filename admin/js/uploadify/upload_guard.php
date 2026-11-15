<?php
// Shared guard for the admin upload endpoints in this folder (included after WEB-INF/prepend.php).
// These endpoints used to accept uploads from anyone, with any file name, into the web root.

// Admin-only: same session key admin/prepend.php sets on login (SESSION_LOGGED_USER_ATTR)
if (empty($_SESSION['CurrentAdminUser'])) {
	header('HTTP/1.1 403 Forbidden');
	exit('Forbidden');
}

/**
 * Reduce a client-supplied file name to a plain file name that can never run as a script:
 * no directories, no leading dots, and script extensions are refused outright.
 */
function upload_safe_name($name) {
	$name = basename(str_replace('\\', '/', (string)$name));
	$name = ltrim($name, '.');
	if ($name === '' || preg_match('/\.(php\d?|phtml|phar|pht|phps|cgi|pl|py|sh|htaccess)(\.|$)/i', $name)) {
		header('HTTP/1.1 400 Bad Request');
		exit('File type not allowed');
	}
	return $name;
}
