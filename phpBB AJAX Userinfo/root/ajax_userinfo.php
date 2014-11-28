<?php
/**
*
* @package AJAX userinfo
* @version $Id: ajax_user.php, V1.0.3 2009-01-28 22:55:27 tas2580 $
* @copyright (c) 2007 SEO phpBB http://www.phpbb-seo.de
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

define('IN_PHPBB', true);
$phpbb_root_path = './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin(false);
$auth->acl($user->data);
$user->setup();

if(!$auth->acl_gets('u_viewprofile'))
{
	trigger_error($user->lang['NO_AUTH_OPERATION']);
}

$ajax_userid =  request_var('userid', 0);

// Select some userdata from DB
$sql = 'SELECT username, username_clean, user_regdate, user_posts, user_from, user_lastvisit, user_avatar, user_avatar_type, user_avatar_width, user_avatar_height, user_colour, user_website, user_rank
	FROM ' . USERS_TABLE . '
	WHERE user_id = '. (int) $ajax_userid;
$result = $db->sql_query($sql);
if($row = $db->sql_fetchrow($result))
{
	// Get the Avatar
	$theme_path = generate_board_url()  . "/styles/" . $user->theme['theme_path'] . '/theme';
	$avatar = get_user_avatar($row['user_avatar'], $row['user_avatar_type'], $row['user_avatar_width'], $row['user_avatar_height']);

	// Get rank
	$rank_title = $rank_img = $rank_img_src = '';
	get_user_rank($row['user_rank'], $row['user_posts'], $rank_title, $rank_img, $rank_img_src);

	// Get username with usercolor
	$username = get_username_string('full', $ajax_userid, $row['username'], $row['user_colour'], $row['username_clean']);

	// Send XML File
	header('Content-Type: text/xml; charset=utf-8');
	echo '<' . '?xml version="1.0" encoding="UTF-8"?' . '>';
	echo '<userdata>';
	echo '<username><![CDATA[' . $username . ']]></username>';
	echo '<regdate><![CDATA[' . $user->format_date($row['user_regdate']) . ']]></regdate>';
	echo '<posts><![CDATA[' . $row['user_posts'] . ']]></posts>';
	echo '<from><![CDATA[' . (!empty($row['user_from']) ? $row['user_from'] : $user->lang['NA']) . ']]></from>';
	echo '<lastvisit><![CDATA[' . (!empty($row['user_lastvisit']) ? $user->format_date($row['user_lastvisit']) : $user->lang['NA']) . ']]></lastvisit>';
	echo '<website><![CDATA[' . (!empty($row['user_website']) ? $row['user_website'] : $user->lang['NA']) . ']]></website>';
	echo '<avatar><![CDATA[' . (!empty($avatar) ? $avatar : '<img src="' . $theme_path . '/images/no_avatar.gif" alt="" />') . ']]></avatar>';
	echo '<rank><![CDATA[' . (!empty($rank_title) ? $rank_title : $user->lang['NA']) . ']]></rank>';
	echo '</userdata>';
}
else
{
	trigger_error($user->lang['GENERAL_ERROR']);
}
$db->sql_freeresult($result);
exit;
?>
