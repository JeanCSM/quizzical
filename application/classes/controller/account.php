<?php defined('APPPATH') or die ('No outside access allowed.');

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

class Controller_Account extends Controller_Template {
    
    function action_index ()
    {
        if ( ! $this->auth->logged_in())
		{
			// If the user is not logged in, display the uniform welcome page
			$this->_template = 'home';
		}
		else
		{
			// If the user is authenticated, display their personalized
			// dashboard of quizzes to take
		}
    }
	
	function action_login ()
	{
		// If the user is already logged in, send them over to their account 
		if ($this->auth->logged_in())
		{
			Request::instance()->redirect('account/details');
			return;
		}
		
		// Set up the login form so that we can render it later on
		$this->_template = 'account/login';
		
		// If some sort of login information was submitted, try to validate it
		if ($_POST)
		{
			if ($this->auth->login($_POST['email'], $_POST['password']) and
				$this->auth->logged_in())
			{
				Request::instance()->redirect();
				return;
			}
			else
			{
				$this->_vars['errors'][] = 
						'The email and/or password that you entered was '
					  . 'incorrect. Here are some possible issues to check for:'
					  . '<ul><li>If you have multiple email addresses, did you '
					  . 'type in the correct one?</li>'
					  . '<li>Do you have CAPS LOCK engaged on your keyboard?'
					  . '</li></ul>';
			}
		}
	}
	
	function action_logout ()
	{
		// If the user is logged in, log them out
		if ($this->auth->logged_in())
		{
			$this->auth->logout();
		}
		
		// Send the user over to the homepage now that they're logged out
		Request::instance()->redirect('/');
	}
    
}
