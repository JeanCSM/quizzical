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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
	{if $page_title}
	<title>{$page_title} ~ {$site_title}</title>
	{else}
	<title>{$site_title}</title>
	{/if}

	{block "styles"}
	<link rel="stylesheet" type="text/css"
		 href="{$base_url}assets/styles/reset.css" />
	<link rel="stylesheet" type="text/css"
		 href="{$base_url}assets/styles/grid.css" />
	<link rel="stylesheet" type="text/css"
		 href="{$base_url}assets/styles/core.css" />
	<link rel="stylesheet" type="text/css"
		 href="{$base_url}assets/styles/application.css" />
	{/block}
</head>

<body>
	<div class="header">
		<div class="container_12">
			<div class="grid_12">
				<h1><a href="{$site_url}">{$site_title}</a></h1>

				{block "nav"}
				<ul class="user-bar">
					{if allowed_to('view', 'admin_section')}
					<li><a href="{$site_url}/admin">Admin</a></li>
					{/if}

					{if $is_logged_in}
					<li><a href="{$site_url}/account/details">Account</a></li>
					<li><a href="{$site_url}/account/logout">Log Out</a></li>
					{else}
					<li><a href="{$site_url}/account/login">Log In</a></li>
					{/if}
				</ul>
				{/block}

				<div class="clear">&nbsp;</div>
			</div>
			<div class="clear">&nbsp;</div>
		</div>
	</div>

	{block "subnav"}{/block}

	<div class="content container_12">
		{block "content"}{/block}

		<div class="clear">&nbsp;</div>
	</div>

	<div class="footer container_12">
		<div class="grid_12">
			Powered by Quizzical v{$version}.
		</div>
		<div class="clear">&nbsp;</div>
	</div>

	{block "scripts"}{/block}
</body>
</html>
