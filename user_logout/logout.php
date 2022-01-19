<?php

// Start saved session
session_start();

// Remove all session variables
session_unset();

//remove the cookie information for the session too
if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
			$params["path"], $params["domain"],
			$params["secure"], $params["httponly"]
		);
	}

// Destroy the session
session_destroy();

// Redirect user to login screen
header("Location: ../user_login/login.php");

?>