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

function account_powers () {
    // Make sure that we have access to the Powers_Registry class to allow us
    // to keep track of the cascading powers more easily
    $CI =& get_instance();
    $CI->load->library('Powers_Registry');
    
    // -------------------------------------------------------------------------
    // VIEWING USERS
    // -------------------------------------------------------------------------
    
    // Add power to view a user profile
    $view_user                      = new Power();
    $view_user->action              = 'view';
    $view_user->item                = 'user';
    $view_user->title               = 'View a user\'s profile page.';
    $view_user->applies_to          = 'user';
    
    // Add power to view a user name
    $view_user_name                 = new Power();
    $view_user_name->action         = 'view';
    $view_user_name->item           = 'user_name';
    $view_user_name->title          = 'View a user\'s first and last name.';
    $view_user_name->depends        = $view_user;
    $view_user_name->applies_to     = 'user';
    $CI->powers_registry->register($view_user_name);
    
    // Add power to view a user email
    $view_user_email                = new Power();
    $view_user_email->action        = 'view';
    $view_user_email->item          = 'user_email';
    $view_user_email->title         = 'View a user\'s email address.';
    $view_user_email->depends       = $view_user;
    $view_user_email->applies_to    = 'user';
    $CI->powers_registry->register($view_user_email);
    
    // Add power to view a user ip
    $view_user_ip                   = new Power();
    $view_user_ip->action           = 'view';
    $view_user_ip->item             = 'user_ip';
    $view_user_ip->title            = 'View a user\'s IP address.';
    $view_user_ip->depends          = $view_user;
    $view_user_ip->applies_to       = 'user';
    $CI->powers_registry->register($view_user_ip);
    
    // Add power to view a user group
    $view_user_group                = new Power();
    $view_user_group->action        = 'view';
    $view_user_group->item          = 'user_group';
    $view_user_group->title         = 'View a user\'s permission group.';
    $view_user_group->depends       = $view_user;
    $view_user_group->applies_to    = 'user';
    $CI->powers_registry->register($view_user_group);
    
    // -------------------------------------------------------------------------
    // EDITING & DELETING USERS
    // -------------------------------------------------------------------------
    
    // Add power to edit a user
    $edit_user                      = new Power();
    $edit_user->action              = 'edit';
    $edit_user->item                = 'user';
    $edit_user->title               = 'Edit a user\'s account information.';
    $edit_user->depends             = $view_user;
    $edit_user->applies_to          = 'user';
    $CI->powers_registry->register($edit_user);
    
    // Add power to edit a user name
    $edit_user_name                 = new Power();
    $edit_user_name->action         = 'edit';
    $edit_user_name->item           = 'user_name';
    $edit_user_name->title          = 'Edit a user\'s first and last name.';
    $edit_user_name->depends        = $edit_user;
    $edit_user_name->applies_to     = 'user';
    $CI->powers_registry->register($edit_user_name);
    
    // Add power to view a user email
    $edit_user_email                = new Power();
    $edit_user_email->action        = 'edit';
    $edit_user_email->item          = 'user_email';
    $edit_user_email->title         = 'Edit a user\'s email address.';
    $edit_user_email->depends       = $edit_user;
    $edit_user_email->applies_to    = 'user';
    $CI->powers_registry->register($edit_user_email);
    
    // Add power to edit a user group
    $edit_user_group                = new Power();
    $edit_user_group->action        = 'edit';
    $edit_user_group->item          = 'user_group';
    $edit_user_group->title         = 'Edit a user\'s permission group.';
    $edit_user_group->depends       = $edit_user;
    $edit_user_group->applies_to    = 'user';
    $CI->powers_registry->register($edit_user_group);
    
    // Add power to reset a user's password
    $edit_user_password             = new Power();
    $edit_user_password->action     = 'edit';
    $edit_user_password->item       = 'user_password';
    $edit_user_password->title      = 'Reset a user\'s password.';
    $edit_user_password->depends    = $edit_user;
    $edit_user_password->applies_to = 'user';
    $CI->powers_registry->register($edit_user_password);
    
    // Add power to delete a user
    $delete_user                    = new Power();
    $delete_user->action            = 'delete';
    $delete_user->item              = 'user';
    $delete_user->title             = 'Delete a user account.';
    $delete_user->depends           = $edit_user;
    $delete_user->applies_to        = 'user';
    $CI->powers_registry->register($delete_user);
}
