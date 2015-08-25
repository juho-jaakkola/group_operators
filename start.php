<?php
/**
 * Elgg group operators plugin
 *
 * @package ElggGroupOperators
 */

elgg_register_event_handler('init', 'system', 'group_operators_init');

/**
 * Group operators plugin initialization functions.
 */
function group_operators_init() {
	// register a library of helper functions
	elgg_register_library('elgg:group_operators', elgg_get_plugins_path() . 'group_operators/lib/group_operators.php');

	// Register a page handler, so we can have nice URLs
	elgg_register_page_handler('group_operators', 'group_operators_page_handler');

	elgg_register_plugin_hook_handler('register', 'menu:entity', 'group_operators_entity_menu_setup');
	elgg_register_plugin_hook_handler('register', 'menu:page', 'group_operators_page_menu_setup');

	// Register actions
	$action_path = elgg_get_plugins_path() . 'group_operators/actions/group_operators';
	elgg_register_action("group_operators/add", "$action_path/add.php");
	elgg_register_action("group_operators/remove", "$action_path/remove.php");

	// Register plugin hooks
	elgg_register_plugin_hook_handler('permissions_check', 'group', 'group_operators_permissions_hook');
	elgg_register_plugin_hook_handler('container_permissions_check', 'group', 'group_operators_container_permissions_hook');
}

/**
 * Dispatches group operators pages.
 * URLs take the form of
 *  Edit operators:       group_operators/manage/<group-guid>
 *  List operated groups: group_operators/owner/<username>
 *  Search members:       group_operators/search/<guid>
 *
 * @param array $page
 * @return bool
 */
function group_operators_page_handler($page) {

	if (isset($page[0])) {
		$dir = elgg_get_plugins_path() . 'group_operators/pages/group_operators';

		$page_type = $page[0];
		switch($page_type) {
			case 'manage':
				set_input('group_guid', $page[1]);
				include "$dir/manage.php";
				return true;
			case 'owner':
				elgg_set_context('groups');
				include "$dir/owner.php";
				return true;
			case 'search':
				elgg_load_library('elgg:group_operators');

				$search = new \GroupOperators\LiveSearch;

				header("Content-Type: application/json");
				echo json_encode($search->find($page[1]));
				return true;
		}
	}

	return false;
}

/**
 * Add items to the page menu on group's profile page
 *
 * @param string $hook
 * @param string $type
 * @param array  $menu
 * @param array  $params
 * @return array $menu
 */
function group_operators_page_menu_setup($hook, $type, $menu, $params) {
	if (!elgg_is_logged_in()) {
		return $menu;
	}

	// Overwrite link to default "My groups" listing with a link
	// to a list of groups where user is also an operator.
	if (elgg_is_menu_item_registered('page', 'groups:owned')) {
		$user = elgg_get_logged_in_user_entity();
		$url =  "group_operators/owner/$user->username";
		$item = new ElggMenuItem('groups:owned', elgg_echo('groups:owned'), $url);
		elgg_register_menu_item('page', $item);
	}

	if (!elgg_in_context('groups')) {
		return $menu;
	}

	$page_owner = elgg_get_page_owner_entity();

	if (!$page_owner instanceof ElggGroup) {
		return $menu;
	}

	if (!$page_owner->canEdit()) {
		return $menu;
	}

	// Link to group operator management page
	$menu[] = ElggMenuItem::factory(array(
		'name' => 'manage',
		'text' => elgg_echo('group_operators:manage'),
		'href' => "group_operators/manage/{$page_owner->guid}",
	));

	return $menu;
}

function group_operators_permissions_hook($hook, $entity_type, $returnvalue, $params) {
	$params = array('container'=>$params['entity'], 'user'=>$params['user']);
	return group_operators_container_permissions_hook($hook, $entity_type, $returnvalue, $params);
}

function group_operators_container_permissions_hook($hook, $entity_type, $returnvalue, $params) {
	if ($params['user'] && $params['container']) {
		$container_guid = $params['container']->getGUID();
		$user_guid = $params['user']->getGUID();
		if (check_entity_relationship($user_guid, 'operator', $container_guid))
			return true;
	}
	return $returnvalue;
}

/**
 * Add links/info to entity menu particular to group operator entities
 */
function group_operators_entity_menu_setup($hook, $entity_type, $returnvalue, $params) {

	if (elgg_in_context('widgets')) {
		return $returnvalue;
	}

	$entity = $params['entity'];
	$handler = elgg_extract('handler', $params, false);
	if ($handler != 'group_operators') {
		return $returnvalue;
	}

	foreach ($returnvalue as $index => $item) {
		if (in_array($item->getName(), array('access', 'likes', 'edit', 'delete'))) {
			unset($returnvalue[$index]);
		}
	}

	$group = elgg_get_page_owner_entity();

	if ($entity->guid != $group->owner_guid) {
		$options = array(
			'name' => 'drop_privileges',
			'text' => elgg_echo('group_operators:operators:drop'),
			'href' => 'action/group_operators/remove?'.http_build_query(array(
																			'mygroup' => $group->guid,
																			'who' => $entity->guid,
																		)),
			'priority' => 300,
			'is_action' => true
		);
		$returnvalue[] = ElggMenuItem::factory($options);
	} else {
		$options = array(
			'name' => 'change_owner',
			'text' => elgg_echo('group_operators:owner'),
			'href' => false,
		);

		$returnvalue[] = ElggMenuItem::factory($options);
	}

	return $returnvalue;
}

