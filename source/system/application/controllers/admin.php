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
		$this->dwootemplate->assign('quizzes', $this->Quizzes_model->get());
		$this->dwootemplate->assign('selected_section', 'quizzes');
		$this->dwootemplate->display('admin/quizzes.tpl');
	}
	
}
