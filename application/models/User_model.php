<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends MY_Model {

	public $_table = 'user';

	protected $validate = [
		[
			'field' => 'username',
			'label' => 'نام کاربری',
			'rules' => 'required'
		],
		[
			'field' => 'mobile_number',
			'label' => 'شماره موبایل',
			'rules' => 'regex_match[/^09\d{9}$/]'
		],
		[
			'field' => 'phone_number',
			'label' => 'شماره موبایل',
			'rules' => 'regex_match[/^\d{8,12}$/]'
		],
		[
			'field' => 'date_birth',
			'label' => 'تاریخ تولد',
			'rules' => 'regex_match[/^\d{4}-\d{1,2}-\d{1,2}$/]'
		]
	];

	public $before_create = ['dates_to_georgian'];
	public $before_update = ['dates_to_georgian'];
	public $after_get = ['dates_to_jalali'];

	public function get_enabled($where = '1=1')
	{
		$this->_database->where($where);
		$this->_database->where('status','0');
		return $this->get_all();
	}

	public function get_joined($id = null)
	{
		if (! is_null($id)) {
			$this->db->where('user.id', $id);
			$this->db->limit(1);
		}
		$this->db->select('user.*, company.title as company_title');
		$this->db->from('user');
		$this->db->join('company', 'user.company_id = company.id', 'left');

		$result =  $this->db->get()->result();
		
		// If an specific id is needed, return the first and only row
		if(! is_null($id) && !empty($result))
		{
			$result = $result[0];
		}
		
		// Convert daets to jalali
		$result = $this->dates_to_jalali($result);
		
		return $result;
	}

	public function get_joined_all()
	{
		return $this->get_joined(null);
	}

	public function remove($user_id)
	{
		$this->db->where('id', $user_id);
		$this->db->limit(1);
		$this->db->update('user', ['status' => 'removed']);

		// Return the number of affected rows
		return $this->db->affected_rows();
	}

	public function exists($field, $value, $exception_id = '0')
	{
		$this->db->select('id');
		$this->db->where($field, $value);
		$this->db->where('id != ', $exception_id);
		$query = $this->db->get('user');

		return $query->num_rows();
	}
}

/* End of file User_model.php */
/* Location: ./application/models/User_model.php */