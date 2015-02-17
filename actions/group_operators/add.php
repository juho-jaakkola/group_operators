<?php
/**
 * Elgg group operators adding action
 *
 * @package ElggGroupOperators
 */

$mygroup = get_entity(get_input('mygroup'));
$who = get_entity(get_input('who'));

$success = false;
if ($mygroup instanceof ElggGroup && $who instanceof ElggUser && $mygroup->canEdit()) {
	if (!$mygroup->isMember($who)) {
		register_error(elgg_echo('group_operators:error:membership_required'));
		forward(REFERER);
	}

	if (check_entity_relationship($who->guid, 'operator', $mygroup->guid)) {
		register_error(elgg_echo('group_operators:error:already_operator', array($who->name, $group->name)));
	} else {
		add_entity_relationship($who->guid, 'operator', $mygroup->guid);
		system_message(elgg_echo('group_operators:added', array($who->name, $group->name)));
	}
} else {
	register_error(elgg_echo('groups:permissions:error'));
}

forward(REFERER);
