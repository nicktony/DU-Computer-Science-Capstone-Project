<?php

	define('PRODUCTION', false);
	
	if(PRODUCTION){
		define('DB_USERNAME', 'tasklessfront');
		define('DB_PASSWORD', 'z{L4s.j_W@&m');
		define('DB_HOST', 'localhost');
		define('DB_NAME', 'taskless');
		
	} else {
		define('DB_USERNAME', 'root');
		define('DB_PASSWORD', '');
		define('DB_HOST', 'localhost');
		define('DB_NAME', 'taskless');
		
		
	}
	
	define('DEBUG_MODE', false);
?>