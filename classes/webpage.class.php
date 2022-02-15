<?php

// Load in DB information
require_once '../utilities/app_config.php';

class webpage {
	
	public $html_path;
	public $css_path;
	public $css_path2;
	public $html;

	public $bg1;
	public $bg2;
	public $bg3;
	public $txt1;
	public $txt2;

	function __construct($path = '') {
		if (file_exists($path)) {
			// Setup page w/o navigation sidebar (specified within code)
			$this->html_path = $path; // Grab .html template
			$this->css_path = str_replace('.html', '.css', $path); // Grab .css template (works with same folder as html)
			
			// Grab html
			$this->html = file_get_contents($path);

			// Replace .css path that links to .html
			$this->html = str_replace('#css_path#', $this->css_path, $this->html);
		} else {
			// Setup page w/ navigation sidebar
			$this->html_path = '../navigation/navigation.html'; // Grab .html template
			$this->css_path = str_replace('.html', '.css', $this->html_path); // Grab .css template
			
			// Grab html
			$this->html = file_get_contents($this->html_path);

			// Replace .css path that links to .html
			$this->html = str_replace('#css_path#', $this->css_path, $this->html);
		}

		// Set color theme of page
		$this->grabColorTheme();
	}

	function createPage($title = '') {
		$this->html = str_replace('#TITLE#', $title, $this->html);
	}

	function inputHTML ($input = '') {
		$this->html = str_replace('#html_main#', $input, $this->html);
	}

	function inputCSS ($input = '') {
		$this->css_path2 = $input;
		$this->html = str_replace('#css_path2#', $input, $this->html);
	}

	// Converts any html between #?# with created html input from php script
	function convert($inputName = '', $input = '') {
		$this->html = str_replace('#' . $inputName . '#', $input, $this->html);
	}

	function printPage() {
		echo $this->html;
	}

	function grabColorTheme() {
		if (isset($_SESSION['username'])) {
			// DB Information
			$servername = "localhost";
			$username = "root";
			$password = "";
			$dbname = "Taskless";

			// Create connection
			$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

			// Check connection
			if ($conn->connect_error) {
			  die("Connection failed: " . $conn->connect_error);
			}

			// Grab user information for query;
			$username = $_SESSION['username'];

			// Query for theme
			$sql = "SELECT theme FROM users WHERE username = '$username' LIMIT 1";
			$result = $conn->query($sql);

			// Grab user preffered theme
			while($row = $result->fetch_assoc()) {
				$theme = $row['theme'];
			}
			
			// Input variables in css
			$this->convert('theme', $theme);
		}
	}
}

?>