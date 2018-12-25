<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax
{
    protected $ci;

    public function __construct()
    {
        $this->ci =& get_instance();
    }

    /**
     * Validate input value based on validated values
     */
    public function is_valid($value, $validated_list)
    {
        // If input is invalid
        if (! in_array($value, $validated_list)) {
            
            log_message('error',"Page ".uri_string().": invalid input ajax value: $value");
            return false;
        }
        else
        {
            return true;
        }
    }    

    /**
     * Generate standard Ajax output
     */
    public function json($ResponseCode, $Note="", $HTTP_Code=200)
    {
        http_response_code($HTTP_Code);
        
        return "{\"code\":\"$ResponseCode\", \"note\":\"$Note\"}";
    }
}
/* End of file Ajax.php */
