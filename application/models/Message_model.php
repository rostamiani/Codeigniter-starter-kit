<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Message_model extends MY_Model {

	public $_table = "message";

	protected $validate = [
		[
			'field' => 'user_id',
			'label' => 'فرستنده',
			'rules' => 'required',
		],
		[
			'field' => 'title',
			'label' => 'عنوان',
			'rules' => 'required',
		],
		[
			'field' => 'text',
			'label' => 'متن',
			'rules' => 'required',
		]
	];

	public $before_create = ['dates_to_georgian'];
	public $before_update = ['dates_to_georgian'];
	public $after_get = ['dates_to_jalali'];
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('message_to_user_model');
	}
	

	public function add($message)
	{
		$this->db->trans_start();

		// Add to message table
		$fields = [
			'user_id' => $message['from_user_id'],
			'title' => $message['title'],
			'text' => $message['text']
		];

		// Insert new message and get the inserted id
		if ($message_id = $this->insert($fields))
		{
			// Add recievers to message_to table
			foreach($message['to_user_id'] as $user_id)
			{
				$fields = [
					'message_id' => $message_id,
					'user_id' => $user_id
				];
				$this->message_to_user_model->insert($fields);
			}
		}

		$this->db->trans_complete();

		// If error, log it
		if ($this->db->trans_status() === FALSE) {
			log_message('error','Message: adding message failed. data: '.print_r($message,true));
			add_session_alert('خطا در ارسال پیام. با مدیریت تماس بگیرید.', 'danger');
		}
		else {
			add_session_alert('پیام ارسال شد.', 'success');
		}
	}

	public function get_joined($id)
	{
		$this->db->select('message.id, message.title, message.text, message.date_created');
		$this->db->select('user.first_name as first_name_from, user.last_name as last_name_from');
		$this->db->join('user','user.id = message.user_id', 'right');
		$this->db->where('message.id', $id);

		$result = $this->db->get('message',1)->result();

		if (empty($result)) {
			return $result;
		}
		else {
			// Convert dates to jalali
			return $this->dates_to_jalali($result[0]);
		}
	}
}

/* End of file Message_model.php */
/* Location: ./application/models/Message_model.php */