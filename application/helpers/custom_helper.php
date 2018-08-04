<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('add_session_alert'))
{
	/**
	 * Add alert to next page refresh
	 */
	function add_session_alert($text, $type='primary')
	{
		$_SESSION['alert_box'][] = [
            'text' => $text,
            'type' => $type
        ];
	}
}

if(! function_exists('num_to_fa'))	
{
	/**
	 * Converts all numbers to persian numbers in the input text
	 */
	function num_to_fa($text){
		$persian_digits = array('۰','۱','۲','۳','۴','۵','۶','۷','۸','۹');
		$english_digits = array('0','1','2','3','4','5','6','7','8','9');
		$text = str_replace($english_digits, $persian_digits, $text);
		return $text;
	}
}