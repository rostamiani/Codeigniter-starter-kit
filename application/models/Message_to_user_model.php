<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Message_to_user_model extends MY_Model {

	public $_table = "message_to_user";

	protected $validate = [
		[
			'field' => 'user_id',
			'label' => 'گیرنده',
			'rules' => 'required',
		],
	];

	public $before_create = ['dates_to_georgian'];
	public $before_update = ['dates_to_georgian'];
	public $after_get = ['dates_to_jalali'];
}

/* End of file Message_to_user_model.php */
/* Location: ./application/models/Message_to_user_model.php */