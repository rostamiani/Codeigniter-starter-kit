<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
		$this->load->model('company_model');
		$this->load->library('Jalali');
	}

	public function index()
	{
		// Get all companies
		$data['companies'] = $this->company_model->get_enabled();

		$this->twig->display('register', $data);
	}

	public function ajax_register()
	{
		// Filter values
		$post = elements(["username","company_id","password","repassword","first_name","last_name","mobile_number","phone_number","address","date_birth"], $this->input->post());

		// Check password repeation
		if ($post['password'] != $post['repassword']) {
			echo '{"code":2, "message":"دو کلمه عبور یکسان نیستند"}';
			return;
		}

		// Generate password hash
		$post['password'] = password_hash($post['password'], PASSWORD_BCRYPT);
		unset($post['repassword']);

		// Convert date to Jalali
		$post['date_birth'] = $this->jalali->jalali_to_gregorian_str($post['date_birth'], '-');

		// Insert the new user
		$this->user_model->insert($post);

		// If inserted successfully
		if($this->db->affected_rows() > 0) {
			echo '{"code":"0", "message":"کاربر جدید با موفقیت ثبت شد"}';
			return;
		}
		else {
			log_message('error','Registration error. last query: ');
			echo '{"code":"1", "message":"'.preg_replace( "/\r|\n/", "", validation_errors()).'"}';
			return;
		}
	}

}

/* End of file Register.php */
/* Location: ./application/controllers/Register.php */