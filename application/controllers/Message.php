<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Message extends MY_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->library('datatables');
        $this->load->model('user_model');
        $this->load->model('message_model');

        $this->auth->just_for(['admin','manager','user']);
    }
    
    /**
     * Show messages for a specific folder
     * 
     * @param string $folder: The name of folder such as inbox and sent
     */
    public function index($folder='inbox')
    {
        if (! in_array($folder, ['inbox','sent','deleted'])) {
            show_error('آدرس صفحه اشتباه است.',404);
        }

        $this->twig->display('message_index', ['folder'=>$folder]);
    }

    /**
     * Prepare data for Datatables JS plugin
     */
    public function ajax_datatable($folder)
    {
        switch ($folder) {
            case 'inbox':
            $this->datatables->select('concat_ws(" ", user_from.first_name, user_from.last_name) as user_from');
            $this->datatables->where("message_to_user.user_id = {$this->user->id}");
            $this->datatables->where("message_to_user.date_deleted IS NULL");
            $folder_name_is_correct = true;
            break;
            
            case 'sent':
            $this->datatables->select('concat_ws(" ", user_to.first_name, user_to.last_name) as user_to');
            $this->datatables->where("message.user_id = {$this->user->id}");
            $this->datatables->where("message_to_user.date_deleted IS NULL");
            $folder_name_is_correct = true;
            break;
            
            case 'deleted':
            $this->datatables->select('concat_ws(" ", user_from.first_name, user_from.last_name) as user_from');
            $this->datatables->select('concat_ws(" ", user_to.first_name, user_to.last_name) as user_to');
            $this->datatables->where("message_to_user.date_deleted IS NOT NULL");
            $folder_name_is_correct = true;
            break;

            // If the folder name is not valid, log error
            default:
            log_message('error',"Message: folder type '$folder' is not valid. User: '{$this->user->username}'");
            $folder_name_is_correct = false;
            break;
        }

        if($folder_name_is_correct)
        {
            $this->datatables->select('message_to_user.date_sent, message_to_user.date_read, message_to_user.date_deleted');
            $this->datatables->select('message.id, message.title, message.text, message.type');
            $this->datatables->from('message');
            $this->datatables->join('message_to_user','message.id = message_to_user.message_id','left');
            $this->datatables->join('user as user_from','message.user_id = user_from.id','left');
            $this->datatables->join('user as user_to','message_to_user.user_id = user_to.id','left');
            
            echo $this->datatables->generate();
            // log_message('error',$this->datatables->last_query);
        }
    }
    
    /**
     * Show add message form
     */
    public function add()
    {
        // If the form is submitted
        if(! empty($this->input->post()) )
        {
            // Filter input elements
            $post = elements(['title', 'to_user_id', 'text'], $this->input->post() );

            // ---- Prepare message
            // Add sender
            $post['from_user_id'] = $this->user->id;
            
            // Save messageto database
            $this->message_model->add($post);

            // Redirect to this page preventing double submit problem
            redirect(uri_string());
            exit();
        }

        // Get the list of all users
        $data['users'] = $this->user_model->get_all();

        // Show add message form
        $this->twig->display('message_add', $data);
    }

    public function view($id)
    {
        $id = (int)$id;

        $data['message'] = $this->message_model->get_joined($id);

        $this->twig->display('message_view', $data);
    }

}

/* End of file Message.php */
