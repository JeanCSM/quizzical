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

class Users_schema_0001 {
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
        // Add the users table, which will contain all of the users that are
        // registered in the application
        $this->forge->add_field('id mediumint(8) unsigned not null auto_increment primary key');
        $this->forge->add_field('group_id mediumint(8) unsigned not null');
        $this->forge->add_field('ip_address char(16) not null');
        $this->forge->add_field('username varchar(300) not null');
        $this->forge->add_field('password varchar(40) not null');
        $this->forge->add_field('email varchar(40) not null');
        $this->forge->add_field('activation_code varchar(40) not null default \'0\'');
        $this->forge->add_field('forgotten_password_code varchar(40) not null default \'0\'');
        $this->forge->create_table('users');

        // Add in the meta table, which will contain additional information
        // about the users in Quizzical
        $this->forge->add_field('id mediumint(8) unsigned not null auto_increment primary key');
        $this->forge->add_field('user_id mediumint(8) unsigned not null');
        $this->forge->add_field('first_name varchar(300)');
        $this->forge->add_field('last_name varchar(300)');
        $this->forge->create_table('meta');

        // Add in the sessions table, which will contain all of the active user
        // sessions and data in the installation
        $this->forge->add_field('session_id varchar(40) not null default \'0\' primary key');
        $this->forge->add_field('ip_address varchar(16) not null default \'0\'');
        $this->forge->add_field('user_agent varchar(50) not null');
        $this->forge->add_field('last_activity int(10) unsigned not null default \'0\'');
        $this->forge->add_field('user_data text not null');
        $this->forge->create_table('sessions');

        // Add in the groups table, which will contain all of the groups for
        // users in the database
        $this->forge->add_field('id tinyint(3) unsigned not null auto_increment primary key');
        $this->forge->add_field('name varchar(20) not null');
        $this->forge->add_field('description varchar(100) not null');
        $this->forge->create_table('groups');

        // Add in the two standard groups that we normally have
        $this->db->insert('groups', array(
            'id' => 1,
            'name' => 'member',
            'description' => 'A basic member who can take published quizzes.'
        ));
        $this->db->insert('groups', array(
            'id' => 2,
            'name' => 'admin',
            'description' => 'An administrator who has full control over the site.'
        ));

        // Add in the powers table, which will contain all of the permissions
        // for the various groups in Quizzical
        $this->forge->add_field('id mediumint(8) not null auto_increment primary key');
        $this->forge->add_field('name varchar(300) not null');
        $this->forge->add_field('group_id smallint(3) not null');
        $this->forge->create_table('powers');

        // Add in the standard privileges granted to administrators
        $this->db->insert('powers', array(
            'group_id' => 2,
            'name' => 'view_admin_section'
        ));
        $this->db->insert('powers', array(
            'group_id' => 2,
            'name' => 'view_user_ip'
        ));
        $this->db->insert('powers', array(
            'group_id' => 2,
            'name' => 'edit_user'
        ));
        $this->db->insert('powers', array(
            'group_id' => 2,
            'name' => 'view_all_users'
        ));
        $this->db->insert('powers', array(
            'group_id' => 2,
            'name' => 'view_scores'
        ));
        $this->db->insert('powers', array(
            'group_id' => 2,
            'name' => 'view_groups'
        ));
        $this->db->insert('powers', array(
            'group_id' => 2,
            'name' => 'view_others_scores'
        ));

        // Register our first user as an administrator
        $offset = rand(0, 21);
        $password = substr(md5(microtime() + rand(0, 5000)), $offset, $offset + 10);
        $this->CI->ion_auth_model->register('admin', $password, 'admin@example.com');

        // Set the admin account to be in the admin group
        $this->db->where('username', 'admin');
        $this->db->update('users', array('group_id' => 2));

        // Pass the information about the administrator account back to the
        // page so that it can be displayed as a message
        $message = "An administrator account with the username set to
            <code>admin</code>, the email set to <code>admin@example.com
			</code>, and the password set to <code>{$password}</code>
			has been created.  <strong>Remember the password and then
			change these values immediately.</strong>";
        array_push($this->CI->messages, $message);
    }

    function down () {
        // Drop all of the user- and permission-related tables
        $this->forge->drop_table('users');
        $this->forge->drop_table('meta');
        $this->forge->drop_table('sessions');
        $this->forge->drop_table('powers');
        $this->forge->drop_table('groups');
    }
}
