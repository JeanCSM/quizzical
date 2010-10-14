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

class Model_User extends Model_Auth_User {
    
    public static function initialize (Jelly_Meta $meta)
    {   
        // Add fields that we'll use for sorting by first and last name later
        // on to make it easy to create a roster
        $meta->fields(array(
            'first_name' => new Field_String,
            'last_name' => new Field_String
        ));
        
        // Add field to allow us to verify a user to allow us to reset their
        // password if they need it reset for some reason
        $meta->fields(array(
            'reset' => new Field_Password(array(
                'hash_with' => array(Auth::instance(), 'hash_password')
            ))
        ));
        
        // Add field that we'll use to verify them to ensure that they are who
        // they say they are after registration
        $meta->fields(array(
            'activate' => new Field_Password(array(
                'hash_width' => array(Auth::instance(), 'hash_password')
            ))
        ));
        
        // Redefine the last_login timestamp field so that the code works
        // properly with Migrations for Ko3; based off of solution mentioned by
        // Jonathan Geiger at <http://github.com/jonathangeiger/kohana-jelly/issues/issue/107>
        $meta->fields(array(
            'last_login' => new Field_Timestamp(array(
                'format' => 'Y:m:d H:i:s'
            ))
        ));
        
        Model_Auth_User::initialize($meta);
    }
    
    public static function _split_username ($username)
    {
        return preg_split('/\s/', $username, 2);
    }
    
    public static function _compress_username ($username)
    {
        return strtolower(str_replace(' ', '', $username));
    }
    
}
