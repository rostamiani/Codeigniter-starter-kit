<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bimetype_model extends MY_Model {

    public $_table = "bimetype";

	// Temporary
    protected $validate = [
		[
			'field' => 'title',
			'label' => 'عنوان',
			'rules' => 'required'
		]
	];

	

}

/* End of file Bime_type_model.php */
