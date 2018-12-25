<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Ghest extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('ghest_model');
    }
    
    public function index()
    {   
        $data['list'] = $this->ghest_model->get_all();

        $this->twig->display("ghest_list", $data);
    }

    public function add()
    {
        // Save the new bimename
        $post = $this->input->post();

        if(! empty($post))
        {
            // Save bimename to the database
            if($this->ghest_model->insert($post))
            {
                // If success
                add_session_alert("قسط جدید با موفقیت اضافه شد.", "success");
                redirect(base_url().'bimename','refresh');
            }
            else
            {
                // Display error
                $data['alert_box'][] = [
                    'text' => $this->ghest_model->validation_error,
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

/* End of file Ghest.php */
