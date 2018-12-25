<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Bimename_model extends MY_Model {

    public $_table = "bimename";

    protected $validate = [
        [
			'field' => 'fullname',
			'label' => 'نام و نام خانوادگی',
			'rules' => 'required'
        ],
        [
			'field' => 'codemelli',
			'label' => 'کد ملی',
			'rules' => 'required'
        ],
        [
			'field' => 'mobile',
			'label' => 'شماره تلفن همراه',
			'rules' => 'required'
        ],
        [
			'field' => 'price',
			'label' => 'مبلغ بیمه',
			'rules' => 'required'
        ],
    ];

}

/* End of file Bimename_model.php */
