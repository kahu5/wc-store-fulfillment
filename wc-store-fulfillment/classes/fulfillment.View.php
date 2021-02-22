<?php

namespace fulfillmentview;

defined('ABSPATH') || exit;

require_once __DIR__ . '/../classes/fulfillment.Model.php';

class Products
{
	function options_page_html() {
		?>
		<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<p>Just testing for now...</p>
	<h3>Current Orders</h3>
	<br>
		</div>
		<?php
	}

}
