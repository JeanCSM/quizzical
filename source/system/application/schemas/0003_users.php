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

class Users_schema_0003 {
    var $CI;
    var $forge;
    var $db;

    function __construct () {
        // Grab a reference to CodeIgniter's dbforge and db classes
        $this->CI =& get_instance();
        $this->forge =& $this->CI->dbforge;
        $this->db =& $this->CI->db;
    }

    function up () {
        // Add additional columns to the users table to support the new
		// features that were added in Ion Auth
		$fields = array(
			'salt' => array(
				'type' => 'VARCHAR',
				'constraint' => 40,
				'null' => true
			),
			'remember_code' => array(
				'type' => 'VARCHAR',
				'constraint' => 40,
				'null' => true
			),
			'created_on' => array(
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
				'null' => false,
			),
			'last_login' => array(
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
				'null' => true
			),
			'active' => array(
				'type' => 'TINYINT',
				'constraint' => 1,
				'unsigned' => true,
				'null' => true
			)
		);

        $this->forge->add_column('users', $fields);
    }

    function down () {
		// Drop all of the new columns that we added to the users table
		$this->forge->drop_column('users', 'salt');
		$this->forge->drop_column('users', 'remember_code');
		$this->forge->drop_column('users', 'created_on');
		$this->forge->drop_column('users', 'last_login');
		$this->forge->drop_column('users', 'active');
    }
}
