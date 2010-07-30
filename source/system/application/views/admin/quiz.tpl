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

{extends "../layout.tpl"}

{block "content"}
<div class="grid_12">
	<h2>{$action|ucwords} Quiz</h2>
</div>
<div class="clear">&nbsp;</div>

<div class="grid_5 aside-form">
	{validation_errors('<div class="error">', '</div>')}

	<form method="post" action="{$current_url}">
		<label>Title</label>
		<input type="text" class="text text-aside" name="title"
			   value="{tif isset(quiz) ? $quiz->title}" />
		<br />
		
		<label>Summary</label>
		<textarea name="summary" class="text-aside">
			{tif isset(quiz) ? $quiz->summary}
		</textarea>
		<br />
		
		<label class="checkbox">Public</label>
		<input type="checkbox" class="checkbox" name="published"
			   {tif isset($quiz) && $quiz->published ? 'checked="checked"'} />
		<br />
		
		<label>Max. Tries</label>
		<input type="text" class="text text-small" name="tries"
			   value="{tif isset($quiz) ? unint_tries($quiz->tries)}" />
		<span class="details">
			Leave this blank to give the user unlimited tries.
		</span>
		<br />
		
		<span class="save-or-delete">
		<input type="submit" class="button" value="Save Changes" />
	
		{if isset($quiz)}
		or <a href="{$site_url}/admin/delete/quiz/{$quiz->id}"
			  class="confirm">Delete</a>
		{/if}
		</span>
	</form>
</div>

<div class="grid_7">
	{if isset($questions) && count($questions) > 0 && isset($quiz)}
	<ol id="editor">
		{foreach $questions question}
		<li class="question">
			<a href="{$site_url}/admin/question/delete/{$question->id}/on/{$quiz->id}"
			   class="delete light-button">&times;</a> 
			<a href="{$site_url}/admin/question/edit/{$question->id}/on/{$quiz->id}"
			   class="edit light-button">Edit</a> 
		
			<p class="question-text">{$question->content}</p>
			
			{$answers = get_question_answers($quiz->id, $question->id)}
			
			<ul class="choices">
				{foreach $answers answer}
				<li class="{tif $answer->correct ? 'answer'}">
					{$answer->content}
				</li>
				{/foreach}
			</ul>
		</li>
		{/foreach}
	</ol>
	{elseif count($questions) == 0 && isset($quiz)}
	<div class="message">
		There are no questions in this quiz.  You can add them using the 
		button below.
	</div>
	{elseif !isset($questions) && !isset($quiz)}
	<div class="message">
		In order to add questions to this quiz, you'll need to first 
		fill in the form on the left and click "Save Changes."
	</div>
	{/if}
	
	{if isset($quiz)}
	<a href="{$site_url}/admin/question/create/on/{$quiz->id}"
	   class="button align-left">+ Add Question</a>
	{/if}
</div>
{/block}