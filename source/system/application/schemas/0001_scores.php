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

class Scores_schema_0001 {
    var $CI;
    var $forge;

    function __construct () {
        // Grab a reference to CodeIgniter's dbforge class
        $this->CI =& get_instance();
        $this->forge =& $this->CI->dbforge;
    }

    function up () {
        // Create the scores table, which will contain the overall scores for
        // users, the number of tries on a particular quiz, etc.
        $this->forge->add_field('id mediumint(8) not null auto_increment primary key');
        $this->forge->add_field('quiz mediumint(8) not null');
        $this->forge->add_field('user mediumint(9) not null');
        $this->forge->add_field('correct int(11)');
        $this->forge->add_field('total int(11)');
        $this->forge->add_field('percent int(11)');
        $this->forge->add_field('tries int(11)');
        $this->forge->add_field('last_try datetime');
        $this->forge->create_table('scores');

        // Create the results, which will contain individual score reports for
        // users on specific quizzes
        $this->forge->add_field('id mediumint(8) not null auto_increment primary key');
        $this->forge->add_field('quiz mediumint(8) not null');
        $this->forge->add_field('user mediumint(8) not null');
        $this->forge->add_field('correct smallint(5) not null');
        $this->forge->add_field('total smallint(5) not null');
        $this->forge->add_field('percent smallint(5)');
        $this->forge->add_field('date datetime not null');
        $this->forge->add_field('meta text not null');
        $this->forge->create_table('results');
    }

    function down () {
        // Drop the scores and results tables
        $this->forge->drop_table('scores');
        $this->forge->drop_table('results');
    }
}
