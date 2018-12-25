<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authentication extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
	}

	public function login()
	{
		$this->twig->display('login');
	}

	public function ajax_login()
	{
		// Filter input data
		$post = elements(['username','password'], $this->input->post());

		// If the user exists
		if(!!$user_data = $this->user_model->get_by('username',$post['username'])) {

			// If password is correct
			if (password_verify($post['password'], $user_data->password )) {
		
				// Set user sessions
				$this->session->set_userdata( ['user' => $user_data] );
				
				// Return success
				echo '{"code":0, "message":"خوش آمدید"}';
			}
			else
			{
				// return error
				echo '{"code":1, "message":"نام کاربری یا کلمه عبور اشتباه است"}';
				return;
			}
		}
		// On error
		else {
			// return error
			echo '{"code":1, "message":"نام کاربری اشتباه است"}';
		}
	}

	public function logout()
	{
		// Remove user from session
		$this->session->unset_userdata('user');

		// Redirect to the main page
		redirect(base_url().'authentication/login','refresh');
	}
}

/* End of file Authentication.php */
/* Location: ./application/controllers/Authentication.php */