<?php if (!defined('BASEPATH')) exit('Direct access not allowed.');

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

class Account extends MY_Controller {

	function login () {
		$this->load->library('form_validation');

		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'required|callback_attempt_login[email]');

		if (!$this->form_validation->run()) {
			$this->dwootemplate->display('account/login.tpl');
		} else {
			redirect();
		}
	}

	function attempt_login ($password, $email_field) {
		$email = $this->input->post($email_field);

		if ($this->ion_auth->login($email, $password)) {
			return true;
		} else {
			$this->form_validation->set_message('attempt_login',
				'The email and/or password that you entered were/was incorrect.');
			return false;
		}
	}

	function logout () {
		// Log the user out
		$this->ion_auth->logout();

		// Redirect the user back to the homepage
		redirect();
	}
	
	function details ($id = -1) {
		$this->load->model('Users_model');
		$this->load->model('Groups_model');
		$this->load->library('form_validation');
		
		// Define our configuration for the form_validation library in CI
		$this->form_validation->set_rules('name', 'Name', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$this->form_validation->set_rules('group', 'Group', 'integer');
		
		// Try to grab the profile information for the user specified in the
		// URL path; if that profile doesn't exist, display a 404 error page
		if ($id == -1) {
			$profile = $this->profile;
			$id = $this->profile->id;
		} else {
			$identity = $this->Users_model->get_identity_where_id($id);
			
			if (!$id) {
				show_404();
				return;
			}
			
			$profile = $this->ion_auth->profile($identity);
		}
		
		// If the user has the appropriate credentials to see the profile page,
		// check to see if any edits were submitted (and then try to apply
		// them), then display the user editing page; otherwise, show an access
		// denied page
		if (($this->powers->i_can('view_all_users') &&
			$this->powers->i_can('edit_user')) ||
			$id == $this->profile->id) {
			
			if ($this->form_validation->run()) {
				$this->Users_model->update(
					$id,
					$this->input->post('name', true),
					$this->input->post('email', true),
					($id != $this->profile->id &&
					 $this->powers->i_can('edit_user_group')) ?
						$this->input->post('group', true) : false
				);
			}
			
			$this->dwootemplate->assign('user', $profile);
			$this->dwootemplate->assign('groups', $this->Groups_model->get()->result());
			$this->dwootemplate->display('account/settings.tpl');
		} else if ($this->profile) {
			show_error('Your account doesn\'t have the appropriate permissions
				to view that user\'s profile page.  If you think that you should
				have access, please contact the administrator.', 403);
		} else {
			show_error('You need to be logged in in order to be allowed to see
				this page on the website.  This page also requires special user
				account permissions--even if you log in you may not be able to
				view this page.', 403);
		}
	}

}
