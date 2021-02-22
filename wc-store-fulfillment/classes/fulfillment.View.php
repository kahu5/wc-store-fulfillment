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

	function page_refresh() {
		$output ='<div class="wrap" style="float:right">
		<button><span id="refreshIcon" onclick="refreshPage()" class="dashicons dashicons-image-rotate"></span></button>
		</div>';
		echo $output;
	}

	function product_list() {
		$output ='<table><tr><th>Product</th><th>Number of Orders</th></tr>
		<tr><td>test1</td><td>test2</td></tr></table>';
		echo $output;
	}

}
