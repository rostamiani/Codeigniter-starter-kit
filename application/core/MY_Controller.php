<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	// Stores logged in user's info
	public $user;

	// User types
	public $user_type =[
		null => 'guest',
		'0' => 'user',
		'1' => 'manager',
		'2' => 'admin'
	];

	public function __construct()
	{
		parent::__construct();

		// load user info if exists
		$this->user = new stdClass;
		if (isset($_SESSION['user'])) {
			$this->user = $_SESSION['user'];

			// Set user as loggin in
			$this->user->logged_in = true;
			$this->user->type_title = $this->user_type[$this->user->type];
		}
		else {

			// Set user as not loggin in
			$this->user->logged_in = false;
		}

		/*     Add Global Variables to Twig Template Engine     */

		// Add user info to all templates
		$this->twig->addGlobal('this_user', $this->user);

		// Add current uri string
		$this->twig->addGlobal('uri', uri_string());
		
		// Add labels to all templates
		$this->twig->addGlobal('label', $this->config->config['label']);
	}

}

/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */