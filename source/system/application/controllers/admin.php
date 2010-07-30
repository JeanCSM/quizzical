<?php

class Admin extends MY_Controller {
	
	private $sections = array();
	
	public function __construct () {
		parent::__construct();
		
		// them view any part of the admin interface
		if (!$this->powers->i_can('view', 'admin_section')) {
			show_access_denied();
		}
		
		// Get the current subsection that the user is on right now; check to
		// make sure that they are actually allowed to access it
		$subsection = $this->uri->segment(2);
		
		if ($subsection && !$this->powers->i_can('admin', $subsection)) {
			show_access_denied();
		}
		
		// Push all of the initial items into our subnavigation list; push that
		// information on to the template engine
		$this->__subnav();
	}
	
	private function __subnav () {
		$options = array('quizzes', 'scores', 'users', 'groups', 'updates', 'options');
		
		// Assemble a list of options that we're actually allowed to view
		foreach ($options as $option) {
			if ($this->powers->i_can('admin', $option)) {
				$this->sections[ucwords($option)] = $option;
			}
		}
		
		// Push the information on to the template engine
		$this->dwootemplate->assign('sections', $this->sections);
	}
	
	function index () {
		// Get the first section that the user has access to and display that
		// if there are any sections that are registered; this avoids the use
		// of a redirect and allows the admin root to display something other
		// than the quizzes list page if the user doesn't have permission to
		// administer that
		if (count($this->sections) > 0) {
			$section = reset($this->sections);
			$this->{$section}();
		} else {
			$this->dwootemplate->display('errors/access_denied_no_subs.tpl');
		}
	}
	
	function quizzes () {
		$this->load->model('Quizzes_model');
		
		// Load all of the quizzes in the database and display the quizzes page
		$this->dwootemplate->assign('quizzes', $this->Quizzes_model->get()->result());
		$this->dwootemplate->assign('selected_section', 'quizzes');
		$this->dwootemplate->display('admin/quizzes.tpl');
	}
	
	function quiz ($action) {
		$this->load->model('Quizzes_model');
		$this->load->model('Questions_model');
		$this->load->library('form_validation');
		
		// Try to determine the id of the quiz being edited/deleted
		$id = $this->uri->segment(4);
		
		// Make sure to set the selected section of the admin subnavigation to
		// the "Quizzes" section
		$this->dwootemplate->assign('selected_section', 'quizzes');
		
		// Set the form name for use with nonces
		$form_name = "quiz_{$action}";
		$this->dwootemplate->assign('form_name', $form_name);
		
		switch ($action) {
			case 'edit':
				$this->form_validation->set_rules('title', 'Title', 'required');
				$this->form_validation->set_rules('tries', 'Max Tries', 'integer');
				
				// If validation passed, push the changes into the database
				if ($this->form_validation->run()) {
					$this->Quizzes_model->update(
						$id,
						$this->input->post('title', true),
						$this->input->post('summary', true),
						$this->input->post('published', true) != false,
						$this->input->post('tries', true)
					);
				}
				
				// Retrieve existing data regarding the quiz
				$this->dwootemplate->assign('quiz',
					$this->Quizzes_model->get_where_id($id)->row());
				$this->dwootemplate->assign('questions',
					$this->Questions_model->get_where_quiz($id)->result());
				
				// Display the quiz editing form so the user can make changes
				$this->dwootemplate->assign('action', 'edit');
				$this->dwootemplate->display('admin/quiz.tpl');
				
				break;
			case 'create':
				$this->form_validation->set_rules('title', 'Title', 'required');
				$this->form_validation->set_rules('tries', 'Max Tries', 'integer');
				
				if ($this->form_validation->run()) {
					// If validation passed, push the new quiz into the database
					// and redirect the user over to a page to continue adding
					// questions to that quiz
					$this->Quizzes_model->create(
						$this->input->post('title', true),
						$this->input->post('summary', true),
						$this->input->post('published', true) != false,
						$this->input->post('tries', true)
					);
					
					redirect('admin/quiz/edit/'. $this->db->insert_id());
					return;
				} else {
					// Since nothing was submitted, display an empty form that
					// allows a user to create a new quiz
					$this->dwootemplate->assign('action', 'create');
					$this->dwootemplate->display('admin/quiz.tpl');
				}
				break;
			case 'delete':
				$this->form_validation->set_rules('token',
					'Validation Token',
					"required|valid_nonce[time][{$form_name}]"
				);
				
				if ($this->form_validation->run()) {
					// Delete the quiz from the database and redirect back to
					// the quizzes page
					$this->Quizzes_model->delete($id);
					redirect('admin/quizzes');
				} else {
					// Retrieve existing data regarding the quiz and create
					// a confirmation message for the quiz deletion
					$quiz = $this->Quizzes_model->get_where_id($id)->row();
					$message = "Are you sure that you would like to delete the
					quiz, {$quiz->title}?  This cannot be undone.";
					
					// Display a confirmation page with that message
					$this->dwootemplate->assign('message', $message);
					$this->dwootemplate->display('admin/confirm.tpl');
				}
				break;
		}
	}
	
	function question ($action) {
		$id = $this->uri->segment(4);
		
		switch ($action) {
			case 'create':
			case 'update':
				break;
			case 'delete':
				break;
		}
	}
	
}
