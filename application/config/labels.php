<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

// Labels for form input

$config['label']['name'] = 'نام';
$config['label']['first_name'] = 'نام';
$config['label']['last_name'] = 'نام خانوادگی';
$config['label']['user_type'] = 
	[
		null=>'میهمان',
		0 => 'کاربر عادی',
		1 => 'مدیر',
		2 => 'مدیر کل'
	];

