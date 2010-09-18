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

class Account extends MY_Controller {
	function login ()
	{
		$this->load->library('form_validation');
		
		// ---
		// Set up validation rules
		// ---
		$this->form_validation->set_rules('email',
			'Email',
			'required|valid_email');
		$this->form_validation->set_rules('password',
			'Password',
			'required|callback_attempt_login[email]');

		// ---
		// Try to log in
		// Note: THIS IS NOT CSRF-VALIDATED
		// ---
		if ( ! $this->form_validation->run('', false))
		{
			$this->dwootemplate->display('account/login.tpl');
		}
		else
		{
			redirect($this->input->post('redirect', true));
		}
	}

	function attempt_login ($password, $email_field)
	{
		$email = $this->input->post($email_field);

		if ($this->ion_auth->login($email, $password))
		{
			return true;
		}
		else
		{
			$this->form_validation->set_message('attempt_login',
				'The email and/or password that you entered were/was incorrect.');
			return false;
		}
	}

	function logout ()
	{
		// Log the user out
		$this->ion_auth->logout();

		// Redirect the user back to the homepage
		redirect();
	}
	
	function forgot ($action = false)
	{
		$this->load->library('form_validation');
		
		switch ($action)
		{
			case 'reset':
				$this->form_validation->set_rules('code', 'Code',
					'required|callback_attempt_reset');
				
				// ---
				// THIS IS NOT CSRF-VALIDATED
				// ---
				if ( ! $this->form_validation->run('', false))
				{
					$this->dwootemplate->display('account/reset.tpl');
				}
				else
				{
					$this->dwootemplate->display('account/confirm_reset.tpl');
				}
				
				break;
			default:
				$this->form_validation->set_rules('email', 'Email',
					'required|valid_email|callback_attempt_validate');
			
				// ---
				// THIS IS NOT CSRF-VALIDATED
				// ---
				if ( ! $this->form_validation->run('', false))
				{
					$this->dwootemplate->display('account/forgot.tpl');
				}
				else
				{
					$this->dwootemplate->display('account/confirm_forgot.tpl');
				}
		}
	}
	
	function attempt_validate ($email)
	{
		if ($this->ion_auth->forgotten_password($email))
		{
			return true;
		}
		else
		{
			$this->form_validation->set_message('attempt_validate',
				'Quizzical could not send the validation code email.  This is '
			  .	'probably because there is <strong>no account</strong> for the '
			  . 'address specified in the Email field.  However, this may also '
			  . ' be caused by a <strong>configuration issue</strong> with this'
			  . ' site.  If you\'re sure that you have the email correct, '
			  . 'contact this site\'s administrator.');
			return false;
		}
	}
	
	function attempt_reset ($code)
	{
		if ($this->ion_auth->forgotten_password_complete($code))
		{
			return true;
		}
		else
		{
			$this->form_validation->set_message('attempt_reset',
				'The password reset code that entered in the Verification Code '
			  . 'field is incorrect.');
			return false;
		}
	}
	
	function details ($id = -1)
	{
		$this->load->model('Users_model');
		$this->load->model('Groups_model');
		$this->load->library('form_validation');
		
		$identity_key = $this->config->item('identity', 'ion_auth');
		
		// ---
		// Try to grab the identity information for the user specified in the
		// URL path; if that profile doesn't exist, display a 404 error page
		// ---
		if ($id == -1)
		{
			$identity = $this->profile->{$identity_key};
			$id = $this->profile->id;
		}
		else
		{
			$identity_result = $this->Users_model->get_identity_where_id($id);
			
			if ($identity_result->num_rows() == 0)
			{
				show_404();
				return;
			}
			
			$identity = $identity->row()->{$identity_key};
		}
		
		// ---
		// Set up validation rules
		// ---
		$this->form_validation->set_rules('name', 'Name', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$this->form_validation->set_rules('group', 'Group', 'integer');
		
		// ---
		// Add form validation rules for the password reset if any of the
		// password editing fields were edited
		// ---
		if ($this->input->post('new') !== false)
		{
			$this->form_validation->set_rules('old', 'Name', 'required');
			$this->form_validation->set_rules('new', 'New', 'required');
			$this->form_validation->set_rules('new-again', 'New (Again)', 'required|matches[new]');
		}
		
		if ($this->form_validation->run() and
			$this->powers->i_can('edit', 'user', $identity))
		{
			$this->Users_model->update($id,
				($this->powers->i_can('edit', 'user_name', $identity)) ?
					$this->input->post('name', true) : false,
				($this->powers->i_can('edit', 'user_email', $identity)) ?
					$this->input->post('email', true) : false,
				($this->powers->i_can('edit', 'user_group', $identity)) ?
					$this->input->post('group', true) : false
			);
			
			// ---
			// If a password change was submitted, update the password in
			// the database; throw an error message if that attempt was
			// unsuccessful
			// ---
			if ($this->input->post('new') !== false)
			{
				if ( ! $this->ion_auth->change_password($identity,
							$this->input->post('old', true),
							$this->input->post('new', true)))
				{
					$this->form_validation->set_message('old',
						'The password could not be reset.  This usually '
					  . 'happens when the Old password field contains an '
					  . 'incorrect password.  Check the spelling of that '
					  . 'field.');
				}
			}
		}
		
		// ---
		// Load the latest profile information and try to display the account
		// page to the user
		// ---
		$profile = $this->ion_auth->profile($identity);
		
		// ---
		// If the user has the appropriate credentials to see the profile page,
		// check to see if any edits were submitted (and then try to apply
		// them), then display the user editing page; otherwise, show an access
		// denied page
		// ---
		if ($this->powers->i_can('view', 'user', $identity))
		{	
			$this->dwootemplate->assign('user', $profile);
			$this->dwootemplate->assign('groups', $this->Groups_model->get()->result());
			$this->dwootemplate->display('account/settings.tpl');
		}
		else
		{
			show_access_denied();
		}
	}
}
