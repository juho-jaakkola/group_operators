<?php
/**
 * Elgg group operators adding action
 *
 * @package ElggGroupOperators
 */

action_gatekeeper();
$mygroup = get_entity(get_input('mygroup'));
$who = get_entity(get_input('who'));

if ($mygroup instanceof ElggGroup && $who instanceof ElggUser && $mygroup->canEdit()) {
	if ($mygroup->isMember($who->guid) && !check_entity_relationship($who->guid, 'operator', $mygroup->guid)) {
		add_entity_relationship($who->guid, 'operator', $mygroup->guid);
	}
}
forward(REFERER);
?>
