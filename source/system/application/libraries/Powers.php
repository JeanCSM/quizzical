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
 * Portions created by the Initial Developer are Copyright (C) 2009
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

class Powers {

	protected $my_powers = false;
	protected $group = false;
	protected $user = false;
	protected $identity = false;
	protected $powers = array();
	protected $CI;

	public function __construct ()
	{
		// Load a reference to our instance of CodeIgniter
		$this->CI =& get_instance();

		// Load the power model
		$this->CI->load->model("powers_model");
	}
	
	public static function __generate_power ($action, $item, $own)
	{
		return ($own) ? "{$action}_own_{$item}" : "{$action}_{$item}";
	}
		// Get information about the user from the database
		$this->user = $this->CI->ion_auth->profile();
		
		// Determine whether the user is logged in or not
		if (!$this->user) {
			// Load information about the powers that users that aren't
            // logged in have
            $this->my_powers = $this->CI->powers_model->get_group_powers(-1);
		} else {
			// Load information about the powers of the current user's group
			$this->my_powers = $this->CI->powers_model->get_group_powers($this->user->group_id);
		}
	}

	public function i_can ($action, $item, $user = null)
	{
		// Give the first user full access to the website
		if (is_object($this->user) && $this->user->id == 1) {
			return true;
		}
		
		if ($user == $this->user) {
			return $this->have_power($action, $item, true) ||
				$this->have_power($action, $item, false);
		}
		else
		{
			return $this->have_power($action, $item, false);
		}
	}
	
	public function have_power ($action, $item, $own=false)
	{
		// Generate the power name from the use
		$power = self::__generate_power($action, $item, $own);
		
		// Determine if the user has the specified power
		$can = array_search($power, $this->my_powers) !== false;

		// Return whether the user has the specified power
		return $can;
	}

	public function register ($action, $item, $own=false)
	{
		// Generate the power name from the use
		$power = self::__generate_power($action, $item, $own);
		
		// If the powers are provided as an array, merge them in;
		// otherwise, push the power onto the end of the array
		if (is_array($power))
		{
			$this->powers = array_merge($this->powers, $power);
		}
		else
		{
			array_push($this->powers, $power);
		}
	}
	
}
