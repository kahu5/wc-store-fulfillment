<?php

namespace fulfillmentview;

defined('ABSPATH') || exit;

require_once __DIR__ . '/fulfillment.Model.php';

class Products
{
	function options_page_html() {
		?>
		<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		</div>
		<?php
	}

	function page_refresh() {
		$output ='<div class="wrap" style="float:right">
		<button><span id="refreshIcon" onclick="refreshPage()" class="dashicons dashicons-image-rotate"></span></button>
		</div>';
		echo $output;
	}

	function product_table_header() {
		$output ='<table class="striped" style="border: 1px #999 solid;">
		<tr><th>Product</th><th>Type of Product</th><th>Attributes</th><th>Orders</th>
		</tr>';
		echo $output;
	}

	function order_table_header() {
		$output ='<table class="striped" style="border: 2px #999 solid;">
		<tr><th>ID</th><th>Type of Product</th><th>Attributes</th><th>Orders</th>
		</tr>';
		echo $output;
	}

    function prod_order_table_header() {
		$output ='<table class="striped" style="border: 2px #999 solid;" width=95%>
		<tr><th>Main Product</th><th>Any Variations</th><th>Orders</th><th>Attributes</th><th>Calculations</th>
		</tr>';
		echo $output;
	}

	function table_end() {
		$output ='</table>';
		echo $output;
	}

    	    /**
     * Displays woocommerce products in a list.
     */
    public static function displayFulfillmentProducts($data, $orders)
    {
        $output = "";
        $output .= "Total Orders:" . wc_processing_order_count() . "<br>";
        foreach ($data as $d) {
            $prod = $d->prod;
            //echo var_dump($d);
            $output .= "<tr>";
            $output .= "<td style='border: 1px #999 solid;'>";
            $output .= "ID " . $d->ID . " ";
            $output .= "<strong>" . $d->post_title . "</strong> <small>(" . ucwords($d->p_type) . ")</small><br>";
            $pid = $d->ID;
            if ($pid != 0) {
                $orderCount = 0;
                $p = get_post($pid);
                //$orders = \fulfillmentmodel\Products::getOrdersWithProducts($pid);
                $aOrders = [];

                foreach ($orders as $o) {
                    $order = wc_get_order($o);
                    $data = $order->get_data();
                    $user = $order->get_user();
                    $aOrders[] = ["id" => $order->get_id(), "order_date" => $data['date_created']->date('m/d/Y h:i'), "user_id" => $user->id, "user" => $user->display_name, "email" => $user->user_email];
                    $output .= "Product#" . $p->ID . " " . $p->post_title . "<br>";
                    foreach ($aOrders as $order) {
                    $output .= "Order#" .$order['id'] . " " . $order['user'] . " " . $order['order_date'];
                    //$orderDate = new \DateTime($order['order_date']);
                    //$orderDate = $order['order_date'];
                    //$output .= $orderDate->format('m/d/Y');
                    //$output .= " <small>" . $orderDate->format('h:i') . "</small>";
                    $output .= "<br>";
                    }
                    $orderCount++;
                }
                $output .= "" . $orderCount . " Orders<br>";
                //self::displayPublishedProductDetails($p, $aOrders);
            }

            //$output .= "<img style='vertical-align:middle;margin:0px 50px' width='75px' src='" . $v['image']['thumb_src'] . "' >";
            $output .= "</td>";
            $output .= "<td>";
            $v = "";
            if ($d->variations) {
                    foreach($d->variations as $v) {
                        //echo var_dump($v);

                        if ($v['attributes']['attribute_pa_ground']) {
                            $output .= " ID " . $v['variation_id'] . " " . $v['attributes']['attribute_pa_ground'] . " g<br>";
                            $pid = $v['variation_id'];
                            $output .= $pid . "(pid) ";
                            $pid = wp_get_post_parent_id($pid);
                            $output .= $pid . "(Ppid) ";
                            if ($pid != 0) {
                                $p = get_post($pid);
                                //$orders = \fulfillmentmodel\Products::getOrdersWithProducts($pid);
                                $aOrders = [];
                                foreach ($orders as $o) {
                                    $order = wc_get_order($o);
                                    $data = $order->get_data();
                                    $user = $order->get_user();
                                    $aOrders[] = ["id" => $order->get_id(), "order_date" => $data['date_created']->date('m/d/Y h:i'), "user_id" => $user->id, "user" => $user->display_name, "email" => $user->user_email];
                                    $output .= "Product#" . $p->ID . " " . $p->post_title . "<br>";
                                    foreach ($aOrders as $order) {
                                    $output .= "Order#" .$order['id'] . " " . $order['user'] . " " . $order['order_date'];
                                    $output .= $pid->get_attribute('pa_ground');
                                    $output .= "<br>";
                                    }
                                    $orderCount++;
                                }
                            }
                        } else if ($v['attributes']['attribute_pa_roast']) {
                            $output .= " ID " . $v['variation_id'] . " " . $v['attributes']['attribute_pa_roast'] . " r<br>";

                        } else if ($v['attributes']['attribute_pa_weight']) {
                            $output .= " ID " . $v['variation_id'] . " " . $v['attributes']['attribute_pa_weight'] . " w<br>";

                        } else if ($v['attributes']['attribute_farm-to-cup-options']) {
                            $output .= " ID " . $v['variation_id'] . " " . $v['attributes']['attribute_farm-to-cup-options'] . " f<br>";

                        } else {
                            $output .= "a<br>";
                        }
                    }
                }
                $output .= "</td>";
            $output .= "<td>{order}</td>";
            $output .= "<td style='text-align:center'>{attributes} </td>";
			$output .= "<td style='text-align:center'>{QTY} " . $orderCount . " orders</td>";
			$output .= "</tr>";
        }

        echo $output;
    }

    public static function displayFulfillmentOrders($customer_orders, $orderLimit)
    {
        $output = "";
		//$output = "<tr><td colspan='3' style='text-align: center;'><h3>Current Orders</h3></td></tr>";


        foreach($customer_orders as $r) {

            $order = wc_get_order( $r->ID );
            $items = $order->get_items();

            foreach($items as $item) {
                $expirationStyle = '';
                $item_name = $item['name'];
                $item_id = $item['product_id'];
				$i = 1;
				if ($i == "1"){
					//$metaItem = $item_meta_data->get_data();
					//var_dump($order);
				}
				$i++;
				$product = wc_get_product($item_id);
				//$productCreated = $product->get_date_created();
				//$regularPrice = get_post_meta($product->ID, '_regular_price', true);

                $itemStatusCheck = $a[$item_id];
                if ($itemStatusCheck != NULL) {
                    $status = $objectStatusPackage[$itemStatusCheck];
                }

                switch ($r->post_status) {
                    case "wc-processing":
                        //echo var_dump($product);
                        $output .= "" . $item_id . " " . $product->get_type() . " - " . $item_name . "<br> " . $product->get_date_created() . "<br>" . $product->get_default_attributes() . "<br>";
                        break;
                    case "wc-active":
                        //$output .= "<tr><td>$item_name</td>";
                        //$output .= "</tr>";
                        break;
					case "wc-completed":
							//$output .= "<tr><td>$item_name</td>";
							//$output .= "</tr>";
						break;
                    default:
                        //$output .= "<tr><td>Name: $r->order_item_name</td>";
                }


            }
        }

        echo $output;
    }

	    /**
     * Displays woocommerce products in a list.
     */
    public static function displayPublishedProducts($data,$orders)
    {
        $output = "";
        foreach ($data as $d) {
            $prod = $d->prod;
            $prodArray = [];
            $output .= "<tr>";
                $output .= "<td style='border: 2px #999 solid;'>";
                //$output .= "<img width='75px' src='" . $v['image']['thumb_src'] . "' >";
                $output .= count($orders) . "" . $d->ID . " <strong>"  . $d->post_title . "</strong> <small>(" . ucwords($d->p_type) . ")</small><br>";
                array_push($prodArray,$d->ID);
            if ($d->variations) {
                foreach($d->variations as $v) {
                    array_push($prodArray,$v['variation_id']);
                    if ($v['attributes']['attribute_pa_ground']) {
                        $output .= "-" . $v['variation_id'] . " <i>" . $v['attributes']['attribute_pa_ground'] . "</i> g";
                    } else if ($v['attributes']['attribute_pa_roast']) {
                        $output .= "-" . $v['variation_id'] . " " . $v['attributes']['attribute_pa_roast'] . " r";
                    } else if ($v['attributes']['attribute_pa_weight']) {
                        $output .= "-" . $v['variation_id'] . " " . $v['attributes']['attribute_pa_weight'] . " w";
                    } else if ($v['attributes']['attribute_farm-to-cup-options']) {
                        $output .= "-" . $v['variation_id'] . " " . $v['attributes']['attribute_farm-to-cup-options'] . " f";
                    } else {
                        $output .= "-" . $v['variation_id'] . " a";
                    }
                    $output .= "<br>";
                }
            }
            $output .= "</td>";
            $output .= "<td style='border: 2px #999 solid;'>{type}<br></td>";

			$output .= "<td style='border: 2px #999 solid;'text-align:center'>";

            foreach($prodArray as $pa) {
                $orderOutput = "";
                $output .= self::displayUserOrderbyProduct($pa, $orders);
            }
            $output .= "</td>";
            $output .= "<td style='border: 2px #999 solid;'>{qty}<br>" . count($prodArray) . "</td>";
			$output .= "</tr>";
        }

        echo $output;
    }

    public static function displayPublishedProductDetails($p, $o)
    {
//
        $output .= "Product#" . $p->ID . $p->post_title . "<br>";
        foreach ($o as $order) {
            $output .= "Order#" .$order['id'] . " " . $order['user'] . " ";
            $orderDate = $order['order_date'];
            //$orderDate = new DateTime($orderDate);
            //$output .= $orderDate->format('m-d-Y');
            $output .= $orderDate;
            $output .= "<br>";
        }
        echo $output;
    }

    public static function displayPublishedProductDetail2($p, $o)
    {
        $output = "";
        $output .= "<tr><td>";
        $output .= "Product#" . $p->ID . $p->post_title . "<br>";
        $output .= "</td><td>";
        $output .= "<td>";
        foreach ($o as $order) {
            $output .= "Order#" .$order['id'] . " " . $order['user'] . " ";
            $orderDate = $order['order_date'];
            //$orderDate = new DateTime($orderDate);
            //$output .= $orderDate->format('m-d-Y');
            $output .= $orderDate;
            $output .= "<br>";
        }
        $output .= "</td>";
        $output .= "<td> Attributes</td>";
        $output .= "<td>".count($o)." Orders</td>";
        $output .= "</tr>";
        echo $output;
    }

	public static function displayUserOrders($customer_orders, $orderLimit)
    {
        $output = "";
		//$output = "<tr><td colspan='3' style='text-align: center;'><h3>Current Orders</h3></td></tr>";


        foreach($customer_orders as $r) {

            $order = wc_get_order( $r->ID );
            $items = $order->get_items();

            foreach($items as $item) {

                $output .= "<pre>\n";
                //$output .= print_r($item);
                $output .= "</pre><br>";

                $expirationStyle = '';
                $item_name = $item['name'];
                $item_id = $item['product_id'];
                $variation_id = $item['variation_id'];
                $quantity = $item['quantity'];

                $output .= "Order#" .$order['id'] . " " . $order['user'] . " ";
                $output .= "Prod ID $item_id, Var ID $variation_id, QTY $quantity<br>";
                $orderDate = $order['order_date'];

				$i = 1;
				if ($i == "1"){
					//$metaItem = $item_meta_data->get_data();
					//var_dump($order);
				}
				$i++;
				$product = wc_get_product($item_id);
				//$productCreated = $product->get_date_created();
				//$regularPrice = get_post_meta($product->ID, '_regular_price', true);

                $itemStatusCheck = $a[$item_id];
                if ($itemStatusCheck != NULL) {
                    $status = $objectStatusPackage[$itemStatusCheck];
                }

                switch ($r->post_status) {
                    case "wc-processing":

                        $output .= "<tr><td>" . $item_id . " <small>" . $product->get_date_created() . "</small><br>";
                        //$output .= $product->get_image();
                        $outputAttr = $product->get_default_attributes();
                      //  echo "<pre>\n";
                       // echo print_r($outputAttr);
                       // echo "</pre><br><hr>";
                        $output .= "</td><td></td><td>#</td></tr>";
                        break;
                    case "wc-active":
                        //$output .= "<tr><td>$item_name</td>";
                        //$output .= "</tr>";
                        break;
					case "wc-completed":
							//$output .= "<tr><td>$item_name</td>";
							//$output .= "</tr>";
						break;
                    default:
                        //$output .= "<tr><td>Name: $r->order_item_name</td>";
                }


            }
        }

        echo $output;
    }

     function displayUserOrderbyProduct($prod_id, $customer_orders)
    {
		       // $orderOutput .= "<h3>Current Orders</h3>";
					 			$orderOutput .= '' . count($prod_id) . ' orders<br>';
								$orderOutput .= $prod_id . "<br>";
                $orderOutput .= count($customer_orders);

                foreach($customer_orders as $r) {
                $order = wc_get_order( $r->ID );
                $items = $order->get_items();

                foreach($items as $item) {

                    $quantity = $item['quantity'];
                    $item_name = $item['name'];
                    $item_id = $item['product_id'];
                    $variation_id = $item['variation_id'];

                    //$orderOutput .= "Order#" .$order['id'] . " " . $order['user'] . "ZZZS ";

                    if ($prod_id == $item_id || $prod_id == $variation_id){
                        $product = wc_get_product($item_id);
                        $itemStatusCheck = $a[$item_id];
                        if ($itemStatusCheck != NULL) {
                            $status = $objectStatusPackage[$itemStatusCheck];
                        }

                        switch ($r->post_status) {
                            case "wc-processing":
                                $orderOutput .= "Order#" .$order['id'] . " " . $order['user'] . " ";
                                $orderOutput .= "Prod ID $item_id, Var ID $variation_id, QTY $quantity<br>";
                                $orderDate = $order['order_date'];
                                break;
                            case "wc-active":
                            case "wc-completed":
                                break;
                            default:

                        }

                    }
                    }
                }
        return $orderOutput;
    }

}
