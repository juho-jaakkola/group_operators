<?php
?>

$(function() {
	$('.elgg-button-delete').filter('[href*="/action/groups/delete"]').toggle(
		elgg.page_owner.owner_guid == elgg.get_logged_in_user_guid() || elgg.is_admin_logged_in()
	);
});