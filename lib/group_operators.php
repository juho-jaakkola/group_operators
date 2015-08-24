<?php
/**
 * Group operators helper functions
 *
 * @package ElggGroupOperators
 */


/**
 * Gives the list of the operators of a group
 *
 * @param ElggGroup $group
 * @return array|null
 */
function get_group_operators($group) {
	if ($group instanceof ElggGroup) {
		$operators = elgg_get_entities_from_relationship(array(
			'types' => 'user',
			'limit' => 0,
			'relationship_guid' => $group->guid,
			'relationship' => 'operator',
			'inverse_relationship' => true,
		));
		$group_owner = get_entity($group->getOwnerGUID());

		if ($group_owner && !in_array($group_owner, $operators)) {
			$operators[$group_owner->guid] = $group_owner;
		}
		return $operators;
	} else {
		return null;
	}
}

/**
 * Get array of group operator GUIDs including the owner
 *
 * @param ElggGroup $group
 * @return array $guids
 */
function get_group_operator_guids($group) {
	$operators = get_group_operators($group);

	$guids = array();
	foreach ($operators as $user) {
		$guids[] = $user->guid;
	}

	return $guids;
}

function elgg_view_group_operators_list($group) {
	$operators = get_group_operators($group);
	$html = '<ul class="elgg-list">';
	foreach ($operators as $guid => $operator) {
		$html .= '<li class="elgg-item">';
		$html .= elgg_view('group_operators/display', array(
			'entity' => $operator,
			'group' => $group
		));
		$html .= '</li>';
	}
	$html .= '</ul>';
	return $html;
}

/**
 * Prepare the manage form variables
 *
 * @param ElggGroup $group
 * @return array
 */
function group_operators_prepare_form_vars($group) {
	// input names => defaults
	$values = array(
		'entity' => $group,
	);

	return $values;
}
