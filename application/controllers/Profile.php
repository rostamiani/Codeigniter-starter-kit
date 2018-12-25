<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
		$this->load->model('company_model');

		$this->auth->just_for(['user','admin','manager']);
	}

	public function test()
	{
		var_dump($_POST);
	}

	public function index()
	{
		// If a form is submitted
		if ($this->input->post()) {

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
            // $config['max_width']            = 1024;
            // $config['max_height']           = 768;
            $config['encrypt_name'] = true; // Preventing persian name errors

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
						'text' => $this->upload->display_errors,
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
					'text' => 'این نام کاربری از قبل انتخاب شده است',
					'type' => 'danger'
				];

				$validated = false;
			}

			// If the form is validated
			if ($validated) {

				// Do update
				if ($this->user_model->update($post['id'], $post))
				{
					add_session_alert('ویرایش با موفقیت اعمال شد.','success');
					
					// Update this user's session
					$_SESSION['user'] = (object)array_replace((array)$_SESSION['user'], $post);

					redirect(current_url());
					exit();
				}
				// On updating error, alert and log the error
				else
				{
					$data['alert_box'][] = [
						'text' => 'خطا در ویرایش کاربر. '.$this->user_model->validation_error,
						'type' => 'danger'
					];

					log_message('error','Profile edit error. '.$this->user_model->validation_error.' data:'.print_r($post, true));
					$validated = false;
				}
			}
			$user = $post;			
		}

		// Get this user's data from database if there is not available
		if (!isset($user)) {
			$user = $this->user_model->get_joined($this->user->id);
		}

		// If the user is not available, show an error
		if (empty($user)) {
			log_message('error',"Profile: User {$this->user->id} does not exists.");
			die('شناسه کاربر اشتباه است.');
		}
		
		$data['user'] = $user;
		$data['companies'] = $this->company_model->get_enabled();

		$this->twig->display('profile', $data);
	}

}

/* End of file Profile.php */
/* Location: ./application/controllers/Profile.php */