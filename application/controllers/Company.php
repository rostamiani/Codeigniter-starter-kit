<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Company extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('company_model');
		$this->load->library('ajax');
		
		$this->auth->just_for('admin');
    }

    // List all your items
    public function index( $offset = 0 )
    {
        $data['companies'] = $this->company_model->get_all();

        $this->twig->display('company_index', $data);
    }

    // Add a new item
    public function add()
    {
		// If the form is posted
		if (! empty($this->input->post())) {
			// Filter input
			$post = elements(['title'], $this->input->post());

			// Check title availablity
			if ($this->company_model->exists('title', $post['title']))
			{
				$data['alert_box'][] = [
					'text' => "این شرکت قبلا ثبت شده است",
					'type' => 'danger'
				];
			}
			else
			{
				// Do insert, If inserted successfull
				if( $this->company_model->insert($post) )
				{
					// If insert wus successfull, add success alert
					add_session_alert('شرکت جدید با موفقیت اضافه شد.', 'success');

					// Redirect to previous page preventing form to post again
					redirect(uri_string(), 'refresh');
				}
				else
				{
					// If error on insert
					// If there is a validation error, alert it
					if (! empty($this->company_model->validation_error)) {
						$data['alert_box'][] = [
							'text' => $this->company_model->validation_error,
							'type' => 'danger'
						];
					}
					else {
						// If there is another error, log the error
						$data['alert_box'][] = [
							'text' => 'خطا در بانک اطلاعاتی. لطفا با مدیر سایت تماس بگیرید.',
							'type' => 'danger'
						];
						log_message('error','Company/ADD: Cannot insert new company. info: '.print_r($post, true));
						
					}
				}
			}
			// Send previews values to the form
			$data['post'] = $post;
		}

		// Create empty data to prevent error when it does not exist
		$data[] = '';

		$this->twig->display('company_add', $data);
    }

    //Update one item
    public function update($id)
    {
		$id = (int)$id;

		// If there is any post
		if (! empty($this->input->post())) {
			
			// Filter input
			$post = elements(['title'], $this->input->post());

			// Update database. If success, alert
			if( $this->company_model->update($id, $post))
			{
				add_session_alert('ویرایش انجام شد.','success');

				// Redirect to index page
				redirect(base_url().'company');
			}
			// If nothing changed
			else{

				$data['alert_box'][] = [
					'text' => 'ویرایش انجام نشد',
					'type' => 'danger'
				];

			}

			// Send post to view
			$data['company'] = $post;
		}
		// Get company info if it's not already available
		if (! isset($data['post'])) {
			$data['company'] = $this->company_model->get($id);
		}

		// Display view
		$this->twig->display('company_update', $data);
    }

    //Delete one item
    public function delete( $id = NULL )
    {

    }

    public function ajax_set($id, $field, $value)
	{
		$id = (int)$id;

		// Validate field
		if ($this->ajax->is_valid($field, ['status'])) {

			// Update the value of the field
			if ($this->company_model->update($id, [$field => $value], true)) {
				
				// Output success
				echo $this->ajax->json(0, 'ویرایش انجام شد.');
			}
			else{
				
				// If failed, send fail
				echo $this->ajax->json(1, $this->company_model->validation_error);
			}
		}
	}
}

/* End of file Company.php */

