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

class Users_model extends Model {
	function __split_name ($name)
	{
		preg_match('/(?P<first_name>\w+) (?P<last_name>[\w ]+)/', $name, $names);
		return $names;
	}
	
	function create ($name, $email, $password, $group_name, $notify = true)
	{
		$names = $this->__split_name($name);

		$meta['first_name'] = $names['first_name'];
		$meta['last_name'] = $names['last_name'];

		if ($notify)
		{
			$this->ion_auth->register($name, $password, $email, $meta, $group_name);
		}
		else
		{
			$this->ion_auth_model->register($name, $password, $email, $meta, $group_name);
		}
	}
	
	function update ($id, $name = false, $email, $group_id = false)
	{
		if ($name != false)
		{
			$names = $this->__split_name($name);
			$changes['first_name'] = $names['first_name'];
			$changes['last_name'] = $names['last_name'];
			$changes['username'] = $name;
		}
		
		$changes['email'] = $email;
		
		if ($group_id != false)
		{
			$changes['group_id'] = $group_id;
		}
		
		$this->ion_auth_model->update_user($id, $changes);
	}
	
	function get_identity_where_id ($id)
	{
		$identity = $this->config->item('identity', 'ion_auth');
		
		$this->db->select($identity);
		$this->db->where('id', $id);
		return $this->db->get('users');
	}
	
}
