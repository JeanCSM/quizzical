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

class Quizzes_schema_0001 {
    var $forge;
    var $CI;

    function __construct () {
        // Grab a reference to CodeIgniter's dbforge class
        $this->CI =& get_instance();
        $this->forge =& $this->CI->dbforge;
    }

    function up () {
        // Create the quizzes table, which will contain all of the quizzes
        // created by users
        $this->forge->add_field('id int(11) not null auto_increment primary key');
        $this->forge->add_field('title varchar(200)');
        $this->forge->add_field('summary text');
        $this->forge->add_field('tries int(4) default \'-1\'');
        $this->forge->add_field('published tinyint(1) not null default \'0\'');
        $this->forge->create_table('quizzes');

        // Create the questions table, which will contain all of the questions
        // that are attached to the quizzes
        $this->forge->add_field('id mediumint(8) not null auto_increment primary key');
        $this->forge->add_field('quiz mediumint(8)');
        $this->forge->add_field('content text');
        $this->forge->create_table('questions');

        // Create the answers table, which will contain all of the answers
        // that are attached to the questions
        $this->forge->add_field('id mediumint(8) not null auto_increment primary key');
        $this->forge->add_field('quiz mediumint(8) not null');
        $this->forge->add_field('question mediumint(8) not null');
        $this->forge->add_field('content text');
        $this->forge->add_field('correct tinyint(1) default \'0\'');
        $this->forge->create_table('answers');
    }

    function down () {
        // Drop the quizzes, answers, and questions tables
        $this->forge->drop_table('quizzes');
        $this->forge->drop_table('questions');
        $this->forge->drop_table('answers');
    }
}
