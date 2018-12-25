<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
		$this->load->model('company_model');
		$this->load->library('datatables');
		$this->load->library('jalali');
		$this->load->library('ajax');
		

		$this->auth->just_for('admin');
	}

	public function index()
	{
		// Get list of all users
		$users = $this->user_model->get_joined_all();

		// Convert birth date to Jalali
		foreach ($users as $key => $user) {
			$users[$key]->date_birth = $this->jalali->datetime_to_jalali($user->date_birth);
		}

		// Show users list
		$this->twig->display('user_index', ['users' => $users]);
	}

	/**
	 * Add a new user to database
	 */
	public function add()
	{
		// If the form is posted
		if (! empty($this->input->post())) {
			// Filter input
			$post = elements(['username', 'company_id', 'password', 'repassword', 'first_name', 'last_name', 'mobile_number', 'phone_number', 'address', 'date_birth','type'], $this->input->post());

			$has_error = false;

			// Check password repeatation
			if ($post['password'] != $post['repassword']) {
				$data['alert_box'][] = [
					'text' => 'دو کلمه عبور معادل نیستند',
					'type' => 'danger'
				];
				$has_error = true;
			}

			// If password is empty, don't change it
			if (is_null($post['password']))
			{
				unset($post['password']);
				unset($post['repassword']);
			}
			// Validate password
			elseif (! preg_match('/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/', $post['password']))
			{
				$data['alert_box'][] = [
					'text' => 'کلمه عبور باید حداقل ۸ حرف و شامل حروف بزرگ، کوچک و یک رقم یا کاراکتر خاص باشد.',
					'type' => 'danger'
				];

				$validated = false;
			}
			else
			{
				// Generate password hash
				$post['password'] = password_hash($post['password'], PASSWORD_BCRYPT);
				unset($post['repassword']);
			}

			// prepare uploaded file
			$config['upload_path']          = './assets/img/user_images';
			$config['allowed_types']        = 'gif|jpg|png';
			$config['max_size']             = 500;
			$config['max_width']            = 1024;
			$config['max_height']           = 768;

			$this->load->library('upload', $config);

			// Do upload if there is a file and there is not any errors yet
			if (! $has_error && ! empty($_FILES['image_file']['name']))
			{
				//  if uploading was successfull, add file name to the fields to update
				if($this->upload->do_upload('image_file'))
				{
					$post['image_file'] = $this->upload->data('file_name');
				}
				// If error on upload, alert error
				else
				{
					$data['alert_box'][] = [
						'text' => $this->upload->display_errors() 
					,'type' => 'danger'
				];
					$has_error = true;
				}
			}
			
			// Check username availablity except this user and there is not any alerts yet
			if (! $has_error && $this->user_model->exists('username', $post['username']))
			{
				$data['alert_box'][] = [
					'text' => "این نام کاربری از قبل انتخاب شده است.",
					'type' => 'danger'
				];
				$has_error = true;
			}

			// Do insert if no error
			if( ! $has_error )
			{
				// If inserted successfull
				if( $this->user_model->insert($post) )
				{
					// If insert wus successfull, add success alert
					$data['alert_box'][] = [
						'text' => 'کاربر جدید با موفقیت اضافه شد.',
						'type' => 'success'
					];

					// Redirect to previous page preventing form to post again
					redirect(uri_string());
				}
				else
				{
					// If error on insert
					// If there is a validation error, alert it
					if (! empty($this->user_model->validation_error)) {
						$data['alert_box'][] = [
							'text' => $this->user_model->validation_error,
							'type' => 'danger'
						];
					}
					else {
						// If there is another error, log the error
						$data['alert_box'][] = [
							'text' => 'خطا در بانک اطلاعاتی. لطفا با مدیر سایت تماس بگیرید.',
							'type' => 'danger'
						];
						log_message('error','User/ADD: Cannot insert new user. info: '.print_r($post, true));
						
					}
				}
			}

			// Send previews values to the form
			$data['post'] = $post;
		}

		$data['companies'] = $this->company_model->get_enabled();

		$this->twig->display('user_add', $data);
	}

	// public function remove($user_id)
	// {

	// 	// Set this user as removed
	// 	if ($this->user_model->remove($user_id))
	// 	{
	// 		$data['alert'] = [
	// 			'type' => 'success',
	// 			'text' => "کاربر با شناسه $user_id با موفقیت حذف شد."
	// 		];
	// 	}
	// 	else
	// 	{
	// 		$data['alert'] = [
	// 			'type' => 'danger',
	// 			'text' => 'خطا در حذف کاربر. '.$this->user_model->validation_error
	// 		];
	// 		log_message('error','User update error. '.$this->user_model->validation_error.' User ID:'.$user_id);
	// 	}

	// 	// Show the alert
	// 	$this->twig->display('user_remove', $data);
	// }

	public function update($user_id)
	{
		$user_id = (int)$user_id;

		// If a form is submitted
		if (! empty($this->input->post())) {

			$validated = true;

			// Filter input elements
			$post = elements(['username', 'company_id', 'first_name', 'last_name', 'mobile_number', 'phone_number', 'address', 'date_birth', 'password', 'repassword', 'id'], $this->input->post());

			// Check password repeation
			if ($post['password'] != $post['repassword']) {
					$data['alert_box'][] = [
						'text' => 'دو کلمه عبور معادل نیستند', 
						'type' => 'danger'
					];
					$validated = false;
			}

			// If password is not available, don't change it
			if (is_null($post['password']))
			{
				unset($post['password']);
				unset($post['repassword']);
			}
			// Validate password
			elseif (! preg_match('/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/', $post['password']))
			{
				$data['alert_box'][] = [
					'text' => 'کلمه عبور باید حداقل ۸ حرف و شامل حروف بزرگ، کوچک و یک رقم یا کاراکتر خاص باشد.',
					'type' => 'danger'
				];

				$validated = false;
			}
			elseif ($validated)
			{
				// Generate password hash
				$post['password'] = password_hash($post['password'], PASSWORD_BCRYPT);
				unset($post['repassword']);
			}

			// prepare uploaded file
			$config['upload_path']          = './assets/img/user_images';
			$config['allowed_types']        = 'gif|jpg|png';
			$config['max_size']             = 500;
			// $config['max_width']            = 1024;
			// $config['max_height']           = 768;
			// 	$config['encrypt_name'] = true; // Preventing persian name errors

			$this->load->library('upload', $config);

			// Do upload if there is a file
			if ($validated && !empty($_FILES['image_file']['name']))
			{
				//  if uploading was successfull, add file name to the fields to update
				if($this->upload->do_upload('image_file'))
				{
					$post['image_file'] = $this->upload->data('file_name');
				}
				
				// If error on upload, alert error
				else
				{
					$data['alert_box'][] = [
						$this->upload->display_errors(), 
						'type' => 'danger'
					];
					$validated = false;
				}
			}

			// Check username availablity except this user 
			// and if validated is not false yet
			if ($validated && $this->user_model->exists('username', $post['username'], $post['id']))
			{
				$data['alert_box'][] = [
					'type' => 'danger',
					'text' =>'این نام کاربری از قبل انتخاب شده است.'
				];
				$validated = false;
			}

			// If the form is validated
			if ($validated) {

				// Do update
				if ($this->user_model->update($post['id'], $post))
				{
					add_session_alert('ویرایش با موفقیت اعمال شد.', 'success');
					redirect(current_url());
					exit();
				}
				// On updating error, alert and log the error
				else
				{
					$data['alert_box'][] = [
						'text' => 'ویرایش انجام نشد'.$this->user_model->validation_error, 
						'type' => 'warning'
					];
					log_message('error','Profile edit error. '.$this->user_model->validation_error.' data:'.print_r($post, true));
				}
			}
			// Save values for re editing
			$user = $post;
		}
		
		// Get this user's data from database if it's not available
		if (! isset($user)) {
			$user = $this->user_model->get_joined($user_id);
		}

		// If the user does not exist, show an error
		if (empty($user)) {
			log_message('error',"user/update: User {$user_id} does not exists.");
			show_error('شناسه کاربر اشتباه است.', 404);
		}
		
		$data['user'] = $user;
		$data['companies'] = $this->company_model->get_enabled();

		$this->twig->display('user_update', $data);
	}

	public function ajax_datatable_list()
	{
		$this->datatables
			// ->select('user.username')
			->select('user.*, company.title as company_title')
			->from('user')
			->join('company', 'user.company_id = company.id', 'inner')
			;

		echo $this->datatables->generate();
	}

	public function ajax_set($id, $field, $value)
	{
		$id = (int)$id;

		// Validate field
		if ($this->ajax->is_valid($field, ['status'])) {

			// Update the value of the field
			if ($this->user_model->update($id, [$field => $value], true)) {
				// Output success
				echo $this->ajax->json(0, 'ویرایش انجام شد.');
			}
			else{
				
				// If failed, send fail
				echo $this->ajax->json(1, $this->user_model->validation_error);
			}
		}
	}

	// // Returns error if username exists.
	// // This format is used in Parsley.js as default.
	// public function ajax_username_exists($username, $except_username = '')
	// {
	// 	// If username is excepted, return true
	// 	if ($username == $except_username) 
	// 	{
	// 		header("HTTP/1.1 404 Not Found");
	// 		return;
	// 	}

	// 	if ($this->user_model->exists('username', $username)) 
	// 	{
	// 		header("HTTP/1.1 200 Ok");
	// 		return;
	// 	}
	// 	else 
	// 	{
	// 		header("HTTP/1.0 404 Not Found");
	// 		return;
	// 	}
	// }
}

/* End of file User.php */
/* Location: ./application/controllers/User.php */