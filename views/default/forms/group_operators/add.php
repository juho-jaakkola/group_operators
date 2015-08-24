<?php
/**
 * Elgg group operators manage form
 *
 * @package ElggGroupOperators
 */

$group = elgg_extract('entity', $vars);

$label = elgg_echo('group_operators:new');

$userpicker = elgg_view('input/userpicker', array(
	'handler' => "group_operators/search/{$group->guid}",
));

$group_guid = elgg_view('input/hidden', array(
	'name' => 'group_guid',
	'value' => $group->guid,
));

$submit = elgg_view('input/submit', array(
	'value' => elgg_echo('group_operators:new:button'),
));

echo <<<HTML
	<div>
		<label for="who">$label</label>
		$userpicker
		$body
	</div>
	<div>
		$group_guid
		$submit
	</div>
HTML;

