<?php
	action_gatekeeper();
	$mygroup_guid = get_input('mygroup');
	$who_guid = get_input('who');
	$mygroup = get_entity($mygroup_guid);
	$who = get_entity($who_guid);
	if ($mygroup instanceof ElggGroup && ($mygroup->owner_guid == elgg_get_logged_in_user_guid() || elgg_is_admin_logged_in())) {
		if (!check_entity_relationship($mygroup->owner_guid, 'operator', $mygroup_guid)) {
			add_entity_relationship($mygroup->owner_guid, 'operator', $mygroup_guid);
		}
		$mygroup->owner_guid = $who_guid;
		$mygroup->save();
	}
	forward(REFERER);
?>
