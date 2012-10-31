<?php
/**
 * List owned groups
 *
 * @package ElggGroupOperators
 */

global $CONFIG;

$page_owner = elgg_get_page_owner_entity();

if ($page_owner->guid == elgg_get_logged_in_user_guid()) {
	$title = elgg_echo('groups:owned');
} else {
	$title = elgg_echo('groups:owned:user', array($page_owner->name));
}
elgg_push_breadcrumb($title);

elgg_register_title_button('groups');

$content = elgg_list_entities(array(
	'type' => 'group',
	'joins' => array(
		", {$CONFIG->dbprefix}entity_relationships er",
	),
	'wheres' => array(
		"((e.owner_guid = $page_owner->guid) OR (
			er.relationship = 'operator'
			AND er.guid_one = $page_owner->guid
			AND er.guid_two = e.guid))",
	),
	'full_view' => false,
));
if (!$content) {
	$content = elgg_echo('groups:none');
}

$params = array(
	'content' => $content,
	'title' => $title,
	'filter' => '',
);
$body = elgg_view_layout('content', $params);

echo elgg_view_page($title, $body);
