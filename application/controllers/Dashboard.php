<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

	public function __construct()
	{
		parent::__construct();

		// Check autentication
		$this->auth->just_for(['admin','manager']);
	}

	public function index()
	{
		// Get dashboard info
		$data = [];

		// Show dashboard
		$this->twig->display('dashboard', $data);
	}

}

/* End of file Dashboard.php */
/* Location: ./application/controllers/Dashboard.php */