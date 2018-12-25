<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Bimename extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('bimetype_model');
        $this->load->model('bimename_model');
        
    }
    
    public function index()
    {   
        $data['list'] = $this->bimename_model->get_all();

        $this->twig->display("bimename_list", $data);
    }

    public function add()
    {
        // Save the new bimename
        $post = $this->input->post();

        if(! empty($post))
        {
            // Save bimename to the database
            if($this->bimename_model->insert($post))
            {
                // If success
                add_session_alert("بیمه نامه جدید با موفقیت اضافه شد.", "success");
                redirect(base_url().'bimename','refresh');
            }
            else
            {
                // Display error
                $data['alert_box'][] = [
                    'text' => $this->bimename_model->validation_error,
                    'type' => 'danger'
                ];
            }
        }
        
        // Get the list of types
        $data['bime_types'] = $this->bimetype_model->get_all();

        // Show the input form
        $this->twig->display("bimename_add", $data);
    }

}

/* End of file Bimename.php */
