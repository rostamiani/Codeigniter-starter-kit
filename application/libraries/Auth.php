<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth
{
	protected $ci;

	public function __construct()
	{
        $this->ci =& get_instance();
	}

	// Check if the user is of specific type
	public function is($user_type)
	{
		$validated = false;
		// Check if the user is loggin in
		if ($this->ci->user->logged_in) 
		{
			// Check if the input is an array of user types
			if (is_array($user_type)) 
			{
				// For an array of user types
				foreach ($user_type as $type) 
				{
					if ($type == $this->ci->user->type_title) 
					{
						$validated = true;
					}
				}
			}
			else 
			{
				// For a single type
				$validated = $user_type == $this->ci->user->type_title;
			}
		}

		return $validated;
	}

	// Limit this page for a specific type of user
	public function for($user_type)
	{
		// If not validated, redirect to main page
		if (! $this->is($user_type)) 
		{
			if ($this->ci->user->logged_in) {
				log_message('error',"Security: User {$this->ci->user->username} tried to access ".uri_string()." as {$this->ci->user->type_title}");
			}
			else
			{
				log_message('error','Security: A guest user tried to access '.uri_string().' page.');

				// Redirect to login page
				redirect(base_url().'/authentication/login','refresh');
			}

			// Show error
			show_error("شما درسترسی لازم برای دیدن این صفجه را ندارید.", 403);
		}
	}


}

/* End of file Auth.php */
/* Location: ./application/libraries/Auth.php */
