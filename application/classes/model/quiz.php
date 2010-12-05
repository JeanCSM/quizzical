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

class Model_Quiz extends Jelly_Model {
    public static function initialize (Jelly_Meta $meta)
    {
        $meta->table('quizzes');
       	$meta->fields(array(
            'id' => new Field_Primary,
            'title' => new Field_String,
            'description' => new Field_Text,
            'tries' => new Field_Integer(array( 'default' => -1 )),
            'published' => new Field_Boolean(array( 'default' => false )),
			'results' => new Field_HasMany(),
			'questions' => new Field_HasMany()
        ));
    }
	
	public static function tries ($type, $quiz_object)
	{
		static $used = array();
		static $total = array();
		
		switch ($type)
		{
			case 'used':
				if ( ! array_key_exists($quiz_object->id, $used))
				{
					$user_object = Auth::instance()->get_user();
					$used[$quiz_object->id] = $quiz_object->get('results')
						->where('user_id', '=', $user_object->id)
						->count();
				}
				
				return $used[$quiz_object->id];
			case 'total':
				if ( ! array_key_exists($quiz_object->id, $total))
				{
					$total[$quiz_object->id] = ($quiz_object->tries == -1) ?
						'&#8734;' : $quiz_object->tries;
				}
				
				return $total[$quiz_object->id];
			case 'allowed':
				if (self::tries('total', $quiz_object) === '&#8734;')
					return true;
				
				$used_num = (int) self::tries('used', $quiz_object);
				$total_num = (int) self::tries('total', $quiz_object);
				
				return  $used_num < $total_num;
		}
	}
}
