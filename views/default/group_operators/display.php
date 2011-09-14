<?php
/**
 * Elgg group operator display
 *
 * @uses $vars['entity'] ElggUser operator
 * @uses $vars['size']   Size of the icon
 * @uses $vars['group'] ElggGroup group which the operator have permissons
 */

$entity = elgg_extract('entity', $vars);
$size = elgg_extract('size', $vars, 'tiny');
$group = elgg_extract('group', $vars);

$icon = elgg_view_entity_icon($entity, $size);

$title = "<a href=\"" . $entity->getUrl() . "\" $rel>" . $entity->name . "</a>";

$rm_url = elgg_add_action_tokens_to_url($CONFIG->wwwroot.'action/group_operators/remove?mygroup='.$group->guid.'&who='.$entity->guid);
$mo_url = elgg_add_action_tokens_to_url($CONFIG->wwwroot.'action/group_operators/mkowner?mygroup='.$group->guid.'&who='.$entity->guid);

if($entity->guid != $group->owner_guid){
	$content = "<a href=\"{$rm_url}\">".elgg_echo('group_operators:operators:drop')."</a>";
	if(elgg_get_logged_in_user_guid() == $group->owner_guid){// || elgg_is_admin_logged_in()){
		$content .= " | <a href=\"{$mo_url}\">".elgg_echo('group_operators:owner:make')."</a>";
	}
}
else {
	$content = elgg_echo('group_operators:owner');
}

$params = array(
	'entity' => $entity,
	'title' => $title,
	'content' => $content
);


	$list_body = elgg_view('user/elements/summary', $params);

	echo elgg_view_image_block($icon, $list_body);

