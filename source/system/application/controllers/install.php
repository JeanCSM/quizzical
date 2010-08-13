<?php if ( ! defined('BASEPATH')) exit('Direct access not allowed.');

/* ***** BEGIN LICENSE BLOCK *****
 * Version: MPL 1.1/GPL 2.0/LGPL 2.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is Quizzical.
 *
 * The Initial Developer of the Original Code is Jonathan Wilde.
 * Portions created by the Initial Developer are Copyright (C) 2010
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *
 * Alternatively, the contents of this file may be used under the terms of
 * either the GNU General Public License Version 2 or later (the "GPL"), or
 * the GNU Lesser General Public License Version 2.1 or later (the "LGPL"),
 * in which case the provisions of the GPL or the LGPL are applicable instead
 * of those above. If you wish to allow use of your version of this file only
 * under the terms of either the GPL or the LGPL, and not to allow others to
 * use your version of this file under the terms of the MPL, indicate your
 * decision by deleting the provisions above and replace them with the notice
 * and other provisions required by the GPL or the LGPL. If you do not delete
 * the provisions above, a recipient may use your version of this file under
 * the terms of any one of the MPL, the GPL or the LGPL.
 *
 * ***** END LICENSE BLOCK ***** */

class Install extends Controller {
	function __construct ()
	{
		parent::__construct();

		if ($this->db->table_exists('users'))
		{
			exit('Quizzical is already installed.  Delete the tables '
			   . 'related to Quizzical (or choose a different database prefix)'
			   . 'to reinstall Quizzical.');
		}
	}

	function index () {
		$this->load->library('Dwootemplate');
		$this->load->library('form_validation');
		$this->load->library('Schema');
		$this->load->model('Users_model');
		$this->load->model('Settings_model');
		$this->load->helper('url');

		$this->dwootemplate->assign('current_url', current_url());
		$this->dwootemplate->assign('base_url',
			$this->config->item('base_url'));
		$this->dwootemplate->assign('page_title', 'Install');
		$this->dwootemplate->assign('site_title', 'Quizzical');

		// Determine the minimum and maximum lengths allowed for
		// passwords according to the Ion Auth configuration files
		$pass_min = $this->config->item('min_password_length', 'ion_auth');
		$pass_max = $this->config->item('max_password_length', 'ion_auth');

		// ---
		// Set validation rules
		// ---
		$this->form_validation->set_rules('title',
			'Title',
			'trim|required|xss_clean');
		$this->form_validation->set_rules('name',
			'First &amp; Last Name',
			'trim|required|xss_clean');
		$this->form_validation->set_rules('email',
			'Email',
			'trim|required|valid_email|xss_clean');
		$this->form_validation->set_rules('password',
			'Password',
			"trim|required|matches[password_again]|min_length[$pass_min]|"
		  . "max_length[$pass_max]");
		$this->form_validation->set_rules('password_again',
			'Password (Again)',
			'trim|required');

		if (!$this->form_validation->run())
		{
			// ---
			// The validation didn't pass--display the installer page
			// ---
			$this->dwootemplate->display('install/install.tpl');
		}
		else
		{
			// ---
			// The validation passed--try to install the latest version
			// of the schema into the database
			// ---
			$version = $this->schema->latest();
			$this->schema->migrate($version);

			// ---
			// Add the administrator user account to the database
			// ---
			$this->Users_model->create(
				$this->input->post('name'),
				$this->input->post('email'),
				$this->input->post('password'),
				'admin',
				false
			);

			// ---
			// Add the title setting to the database
			// ---
			$this->Settings_model->create(
				'site_title',
				$this->input->post('title')
			);

			// ---
			// Display a page telling the user that the installation is
			// complete and all they have to do is log in
			// ---
			$this->dwootemplate->display('install/confirm_install.tpl');
		}
	}
}
