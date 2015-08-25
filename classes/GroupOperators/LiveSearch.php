<?php

namespace GroupOperators;

use ElggGroup;

class LiveSearch {
	/**
	 * Autocomplete endpoint
	 *
	 * /group_operators/search/<group_guid>
	 *
	 * @param int $group_guid
	 * @return array
	 */
	public function find($group_guid) {
		// Return results only to logged in users
		if (!elgg_is_logged_in()) {
			exit;
		}

		// This is a part of user's name
		$term = sanitise_string(get_input('term'));

		// Replace mysql vars with escaped strings
		$term = str_replace(array('_', '%'), array('\_', '\%'), $term);

		$group = get_entity($group_guid);

		if (!$group instanceof \ElggGroup) {
			elgg_log("Group operators: not able to find group $group_guid");
			return array();
		}

		// Get list of existing group operators
		$operator_guids = \get_group_operator_guids($group);
		$operator_guids = implode(', ', $operator_guids);

		$dbprefix = elgg_get_config('dbprefix');

		// Get members who are not operators
		$members = $group->getMembers(array(
			'limit' => false,
			'joins' => array(
				"JOIN {$dbprefix}users_entity ue ON ue.guid = e.guid",
			),
			'wheres' => array(
				"ue.guid NOT IN ($operator_guids)",
				"(name LIKE '%{$term}%' OR username LIKE '%{$term}%')",
			),
		));

		if (empty($members)) {
			return array();
		}

		$results = array();
		foreach ($members as $user) {
			$output = elgg_view_list_item($user, array(
				'use_hover' => false,
				'class' => 'elgg-autocomplete-item',
				'full_view' => false,
				'href' => false,
				'title' => $user->name, // Default title would be a link
			));

			$icon = elgg_view_entity_icon($user, 'tiny', array(
				'use_hover' => false,
			));

			$result = array(
				'type' => 'user',
				'name' => $user->name,
				'desc' => $user->username,
				'guid' => $user->guid,
				'label' => $output,
				'value' => $entity->username,
				'icon' => $icon,
				'url' => $user->getURL(),
				'html' => elgg_view('input/userpicker/item', array(
					'entity' => $user,
					'input_name' => 'members',
				)),
			);

			$results[$user->name . rand(1, 100)] = $result;
		}

		ksort($results);

		return array_values($results);
	}
}
