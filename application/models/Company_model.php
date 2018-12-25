<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Company_model extends MY_Model {

	public $_table = 'company';

	public $validation = [
		
	];

	public function get_enabled($where = '1=1')
	{
		$this->_database->where($where);
		$this->_database->where('status','0');
		return $this->get_all();
	}

	public function exists($field, $value, $exception_id = '0')
	{
		$this->db->select('id');
		$this->db->where($field, $value);
		$this->db->where('id != ', $exception_id);
		$query = $this->db->get('company');

		return $query->num_rows();
	}
}

/* End of file Company_model.php */
/* Location: ./application/models/Company_model.php */