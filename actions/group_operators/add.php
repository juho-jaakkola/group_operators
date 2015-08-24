<?php
/**
 * Elgg group operators adding action
 *
 * @package ElggGroupOperators
 */

$group = get_entity(get_input('group_guid'));
$members = get_input('members');

if (!$group instanceof ElggGroup) {
	register_error(elgg_echo('groups:notfound'));
	forward(REFERER);
}

if (empty($members)) {
	register_error(elgg_echo('group_operators:error:no_users'));
	forward(REFERER);
}

if (!$group->canEdit()) {
	register_error(elgg_echo('groups:permissions:error'));
	forward(REFERER);
}

foreach ($members as $member_guid) {
	$user = get_user($member_guid);

	if (!$group->isMember($user)) {
		register_error(elgg_echo('group_operators:error:membership_required'));
		continue;
	}

	if (check_entity_relationship($user->guid, 'operator', $group->guid)) {
		register_error(elgg_echo('group_operators:error:already_operator', array($user->name, $group->name)));
	} else {
		add_entity_relationship($user->guid, 'operator', $group->guid);
		system_message(elgg_echo('group_operators:added', array($user->name, $group->name)));
	}
}

forward(REFERER);
