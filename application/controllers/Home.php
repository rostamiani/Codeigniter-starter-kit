<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {

	public function index()
	{
		if ($this->user->logged_in) {
			echo "خوش آمدید {$this->user->username}";
		}
		else {
			echo 'لطفا ابتدا وارد شوید';
		}
	}

}

/* End of file Home.php */
/* Location: ./application/controllers/Home.php */