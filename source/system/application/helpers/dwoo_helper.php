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

function Dwoo_Plugin_percentage (Dwoo $dwoo, $correct, $total) {
	return round(($correct / $total) * 100);
}

function Dwoo_Plugin_allowed_to (Dwoo $dwoo, $action, $item, $user=null) {
	$CI =& get_instance();
	return $CI->powers->i_can($action, $item, $user);
}

function Dwoo_Plugin_get_question_answers (Dwoo $dwoo, $quiz_id, $question_id) {
	return array();
}

function Dwoo_Plugin_unint_tries (Dwoo $dwoo, $raw) {
	return ($raw == -1) ? '' : $raw;
}

function generate_nonce ($form, $time) {
	$CI =& get_instance();
	return md5($CI->config->item('encryption_key') .
		   $time . $form . $CI->input->ip_address());
}

function validate_nonce ($nonce, $form, $time) {
	return (time() - $time) < 1800 &&
		   generate_nonce($form, $time) == $nonce;
}

function valid_nonce ($nonce, $form, $time_field) {
	$CI =& get_instance();
	$time = $CI->input->post($time_field);
	
	if (!validate_nonce($nonce, $form, $time)) {
		$CI->form_validation->set_message('valid_nonce',
			'The access validation token was not valid.  Did a potentially
			malicious user redirect you here?');
		return false;
	} else {
		return true;
	}
}

function nonce_fields ($form) {
	$time = time();
	$nonce = generate_nonce($form, $time);
	
	return "<input type=\"hidden\" name=\"token\" value=\"$nonce\" />" .
		   "<input type=\"hidden\" name=\"time\" value=\"$time\" />";
}

function show_access_denied () {
	$CI =& get_instance();
	
	$CI->dwootemplate->assign('redirect', current_url());
	
	if ($CI->ion_auth->logged_in()) {
		$CI->dwootemplate->display('errors/access_denied.tpl');
	} else {
		$CI->dwootemplate->display('errors/access_denied_anonymous.tpl');
	}
	
	// We're going to use an undocumented method to stop all CodeIgniter
	// execution and flush the output buffer
	$CI->output->_display();
	exit();
}
