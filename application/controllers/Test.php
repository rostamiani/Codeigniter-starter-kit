<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends MY_Controller {

	public function index()
	{
		echo password_hash("1", PASSWORD_BCRYPT);
	}



}

/* End of file Test.php */
/* Location: ./application/controllers/Test.php */