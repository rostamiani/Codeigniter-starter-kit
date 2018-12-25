<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Bime_type extends MY_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('bimetype_model');
    }

    public function index()
    {
        // If there is some submitted values, add it to the database
        $post = $this->input->post();

        if (! empty($post)) {

            // Add new types to the database
            if($this->bimetype_model->insert($post))
            {
                add_session_alert("نوع بیمه {$post['title']} با موفقیت اضافه شد.", "success");
            }
            else {
                // If there is another error, log the error
                add_session_alert($this->bimetype_model->validation_error, "danger");
            }
            redirect(uri_string());
        }

        //  Find all types
        $data['list']  = $this->bimetype_model->get_all();

        // Show all types in the table
        $this->twig->display('bime_type_list', $data);
    }

    

}

/* End of file Bime_type.php */
