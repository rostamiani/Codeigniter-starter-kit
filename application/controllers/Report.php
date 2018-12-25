<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends MY_Controller {

	public function index()
	{
		$this->twig->display('report');
	}

}

/* End of file Report.php */
/* Location: ./application/controllers/Report.php */