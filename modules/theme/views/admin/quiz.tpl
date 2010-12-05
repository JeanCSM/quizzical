{* ***** BEGIN LICENSE BLOCK *****
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
 * Portions created by the Initial Developer are Copyright (C) 2009 - 2010
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
 * ***** END LICENSE BLOCK ***** *}

{extends "layout"}

{block "content"}
{errors($errors)}

{if isset($quiz_object->id)}
<form method="post" action="{URL::site("quiz/edit/$quiz_object->id")}">
{else}
<form method="post" action="{URL::site('quiz/create')}">
{/if}
	<div class="row">
		<div class="cell width-3:4 position-0">
			<div class="field">
				<label>Title</label>
				<input type="text" name="title" class="title"
					   value="{$quiz_object->title|escape}" />
			</div>
			
			<div class="field">
				<label>Description</label>
				<textarea name="description">{$quiz_object->description|escape}</textarea>
			</div>
			
			<h3>Questions</h3>
			{if $quiz_object->questions}<ol>{/if}
			{foreach $quiz_object->questions question_object}
				<li>
					<a class="micro question-action"
					   href="{URL::site("question/delete/$question_object->id")}">
						Delete
					</a>
					
					<a class="micro question-action"
					   href="{URL::site("question/edit/$question_object->id")}">
						Edit
					</a>
					
					<a name="question-{$question_object->id}"></a>
					<p class="question">{$question_object->content|escape|nl2br}</p>
					
					{foreach $question_object->answers answer_object}
					<p class="choice {tif $answer_object->correct ? 'correct'}">
						{$answer_object->content}
					</p>
					{else}
					<p class="no-choices">
						This question has no answer choices.  You can
						<a href="{URL::site("question/edit/$question_object->id")}">add some</a>
						to it.</p>
					{/foreach}
				</li>
			{else}
				{if isset($quiz_object->id)}
					<p class="message">
						There aren't any questions in this quiz.  Why not add
						one with the button below?
					</p>
				{else}
					<p class="message">
						Before you can add questions to this quiz, you'll need
						to fill in the "Title" field and save the quiz.
					</p>
				{/if}
			{/foreach}
			{if $quiz_object->questions}</ol>{/if}
			
			{if isset($quiz_object->id)}
			<div class="toolbar">
				<a href="{URL::site('question/create/$quiz_object->id')}"
				   class="button partial-width">Add Question</a>
			</div>
			{/if}
		</div>
		
		<div class="cell width-1:4 position-3:4">
			<div class="field">
				<input type="submit" class="button" value="Save" />
			</div>
			
			<div class="field inline-field">
				<span class="checkbox-field">
					<input type="checkbox" name="published"
						   {tif $quiz_object->published ? 'checked="checked"'} />
				</span>
				<label>Published</label>
			</div>
			
			<div class="field inline-field">
				<input name="tries" value="{$quiz_object->tries|escape}" />
				<label>Maximum Allowed Tries</label>
			</div>
			
			{if isset($quiz_object->id)}
			<div class="field alternate">
				<a href="{URL::site('quiz/delete/$quiz_object->id')}">Delete</a>
			</div>
			{/if}
		</div>
	</div>
</form>


{/block}