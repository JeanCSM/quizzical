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

class powers_model extends Model {

    function get_group_id ($identity_column, $identity) {
        // We're only going to grab the group_id column where the
        // identity matches the identity column
        $this->db->select("group_id");
        $this->db->where($identity_column, $identity);

        // Get the group id from the database
        $result = $this->db->get("users");
        $row = $result->row();
        return $row->group_id;
    }

    function get_group_powers ($group_id) {
        // Grab all of the powers that match the group_id
        $powers = $this->db->get_where("powers", array(
            "group_id" => $group_id
        ));

        // Create an array to hold our final permission set
        $allowed = array();

        // Grab the permissions and add each of them to our array
        foreach ($powers->result() as $power)
            array_push($allowed, $power->name);

        // Return the final permission set
        return $allowed;
    }

    function group_authorize ($group_id, $power) {
        // Insert the power into the DB
        $this->db->insert("powers", array(
            "group_id" => $group_id,
            "name" => $power
        ));
    }

    function group_deauthorize ($group_id, $power) {
        // Delete the authorizations that match the current group_id and
        // power name
        $this->db->delete("group_id", array(
            "group_id" => $group_id,
            "name" => $power
        ));
    }

}
