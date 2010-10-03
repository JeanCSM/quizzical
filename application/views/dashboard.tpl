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

{extends "layout.tpl"}

{block "content"}
<div class="grid_9">
	<h2>Quizzes to Take</h2>

	{foreach $quizzes quiz}
	<h3><a href="{$site_url}/quiz/take/{$quiz.id}">
		{$quiz.title|escape}
	</a></h3>

	<p>{$quiz.summary|escape|nl2br}</p>

	<div class="take-it-button">
		<a href="{$site_url}/quiz/take/{$quiz.id}">Take It</a>
	</div>

	<p>
		{if isset($quiz.tries)}
		You're allowed to retake this quiz an unlimited number of times.
		{else}
		You've taken it {$quiz.user_tries}
		time{tif $quiz.user_tries != 1 ?: "s"}.

		You're allowed to take it {$quiz.tries}
		time{tif $quiz.user_tries != 1 ?: "s"}
		{/if}
	</p>
	{else}
	<p>There are no quizzes to take right now.</p>
	{/foreach}

	<p class="clear">&nbsp;</p>
</div>

<div class="grid_3">
	<div class="aside">
		<h4>Recent Test Scores</h4>

		<div class="aside-content">
			{foreach $results result}
			<div class="aside-row">
				<a href="{$site_url}/quiz/result/{$result.id}">
					{$result.title} &mdash;
					{percentage($result.correct, $result.total)}%
				</a>
			</div>
			{else}
			<p>You haven't taken any quizzes yet.</p>
			{/foreach}

			{if $results_count}
			<div class="aside-row more">
				<a href="{$site_url}/quiz/results">See Full List</a>
			</div>
			{/if}
		</div>
	</div>
</div>
{/block}
