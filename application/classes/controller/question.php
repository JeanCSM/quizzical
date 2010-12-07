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

class Controller_Question extends Controller_Template {
	
	public function before ()
	{
		parent::before();
		
		$this->_vars['nav_selected'] = 'quiz';
		
		if ( ! $this->auth->logged_in())
		{
			$this->deny();
		}
	}
	
	public function action_create ($quiz_id)
	{
		if ( ! Acl::instance()->allowed('quiz editor'))
		{
			$this->deny('errors/access_denied');
		}
	
		$question_object = Jelly::factory('question');
		$question_object->quiz = $quiz_id;
		$this->edit($question_object);
	}
	
	public function action_edit ($question_number)
	{
		if ( ! Acl::instance()->allowed('quiz editor'))
		{
			$this->deny('errors/access_denied');
		}
		
		$question_object = Jelly::select('question')->load($question_number);
		$this->edit($question_object);
	}
	
	public function action_delete ($question_number)
	{
		if ( ! Acl::instance()->allowed('quiz editor'))
		{
			$this->deny('errors/access_denied');
		}
		
		$question_object = Jelly::select('question')->load($question_number);
		
		if ( ! $_POST)
		{
			$this->_vars['message'] =
				"Are you really sure that you want to delete the question, "
			  . "\"$question_object->content,\" from Quizzical?  There's no going"
			  . " back after clicking 'Go Ahead' below.";
			$this->_vars['cancel'] = "quiz/edit/{$question_object->quiz->id}#question-$question_object->id";
			$this->_template = 'admin/confirm';
		}
		else
		{
			$quiz_id = $question_object->quiz->id;
			
			Jelly::delete('answer')
				->where('question_id', '=', $question_number)
				->execute();
			$question_object->delete();
			
			Request::instance()->redirect("quiz/edit/$quiz_id");
		}
	}
	
	public function edit ($question_object)
	{
		// If there's any information that needs to be saved into the database,
		// do that now; display any errors that might arise to the user
		if ($_POST)
		{
			$question_data = Arr::extract($_POST, array('content'));
			$question_object->set($question_data);
			
			$answer_count = (int) $_POST['count'];
			$answer_correct = array_key_exists('correct', $_POST) ?
				(int) $_POST['correct'] : -1;
			
			try
			{
				$question_object->save();
				
				for ($i = 0; $i < $answer_count; $i++)
				{
					$answer_text = $_POST["choice-$i"];
				
					if (!$answer_text)
						continue;
					
					$answer_object = Jelly::factory('answer');
					$answer_object->content = $answer_text;
					$answer_object->correct = ($answer_correct == $i);
					$answer_object->question = $question_object;
					
					if (array_key_exists("choice-$i-id", $_POST))
					{
						$answer_number = $_POST["choice-$i-id"];
						$answer_object->save($answer_number);
					}
					else
					{
						$answer_object->save();
					}
				}
			}
			catch (Validate_Exception $errors)
			{
				$this->_vars['errors'] = $errors->array->errors('default');
			}
		}
		
		// Display the editing page with all of the appropriate data filled in
		$this->_vars['question_object'] = $question_object;
		
		$this->_vars['answers'] = $question_object->answers;
		$this->_vars['count'] = count($this->_vars['answers']);
		
		if ($this->_vars['count'] == 0)
		{
			$this->_vars['answers'] = null;
			$this->_vars['count'] = 4;
		}
		
		$this->_vars['end'] = $this->_vars['count'] - 1;
		
		$this->_template = 'admin/question';
	}
    
}
