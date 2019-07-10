<?php
namespace App\Reports\Managers;

use App\Reports\OrderStatus;
use App\Traits\ReportsTrait;


class ReportsManager
{
    use ReportsTrait;

    public $wpdb;
    public $woocommerce;

    private $start_date;
    private $end_date;

    private $order_items = [];

    private $items_gotten = false;

    function __construct()
    {
        //initialise the application
        global $wpdb;
        global $woocommerce;

        $this->wpdb = $wpdb;
        $this->woocommerce = $woocommerce;

        $this->init_dates();
    }

    private function init_dates()
    {
        if(isset($_GET['start_date']))
        {
            $this->start_date = $_GET['start_date'];
        }
        else {
            $this->start_date = date("Y-m-d");
        }

        if(isset($_GET['end_date']))
        {
            $this->end_date = $_GET['end_date'];
        }
        else {
            $this->end_date = date("Y-m-d");
        }

    }

    public function getOrderStats()
    {
        $query = $this->getStatsQuery();
        $shippingQuery = $this->getStatsShippingQuery();

        $results = $this->wpdb->get_results($query);
        $shippingStats = $this->wpdb->get_results($shippingQuery);

        $response = [
            'total' => count($results),
            'total_cost' => 0,

            'completed' => [
                    'count' => 0,
                    'total' => 0
                ],

            'pending' => [
                'count' => 0,
                'total' => 0
            ],
            'cancelled' =>[
                'count' => 0,
                'total' => 0
            ]
        ];

        $withoutShipping = [
            'total' => count($shippingStats),
            'total_cost' => 0,

            'completed' => [
                    'count' => 0,
                    'total' => 0
                ],

            'pending' => [
                'count' => 0,
                'total' => 0
            ],
            'cancelled' =>[
                'count' => 0,
                'total' => 0
            ]
        ];

        $i = 0;
        foreach($results as $post)
        {
            $shipping = $shippingStats[$i];

            $response['total_cost'] += $post->order_total;
            $withoutShipping['total_cost'] += ($post->order_total - $shipping->shipping);

            if($post->post_status == OrderStatus::COMPLETED)
            {
                $response['completed']['count'] += 1;
                $response['completed']['total'] += $post->order_total;

                $withoutShipping['completed']['total'] += ($post->order_total - $shipping->shipping);
            }

            if($post->post_status == OrderStatus::ON_HOLD || $post->post_status == OrderStatus::PROCESSING)
            {
                $response['pending']['count'] += 1;
                $response['pending']['total'] += $post->order_total;

                $withoutShipping['pending']['total'] += ($post->order_total - $shipping->shipping);
            }

            if($post->post_status == OrderStatus::FAILED || $post->post_status == OrderStatus::CANCELLED)
            {
                $response['cancelled']['count'] += 1;
                $response['cancelled']['total'] += $post->order_total;

                $withoutShipping['cancelled']['total'] += ($post->order_total - $shipping->shipping);
            }

            ++$i;
        }



        return [
            'with_shipping' => $response,
            'without_shipping' => $withoutShipping
        ];
    }

    public function userRegionReport()
    {
        $data = $this->getUsersAndRegionOrders();

        $report = [
            'sellers' => [],
            'regions' => [],
            'towns' => [],
            'payment_methods' => []
        ];

        //initialise the users and the regions
        $regions = get_terms("zone_region", ['hide_empty' => false ]);
        $sellers = get_terms("seller", ['hide_empty' => false ]);
        $towns = get_terms("zone_town", ['hide_empty' => false ]);
        $payment_methods = $this->get_payment_methods();

        foreach ($sellers as $seller) {
            $report['sellers'][$seller->term_id] = [
                'count' => 0,
                'total' => 0
            ];
        }

        foreach ($regions as $region) {
            $report['regions'][$region->term_id] = [
                'count' => 0,
                'total' => 0
            ];
        }

        foreach ($towns as $town) {
            $report['towns'][$town->term_id] = [
                'count' => 0,
                'total' => 0
            ];
        }

        foreach ($payment_methods as $method) {
            $report['payment_methods'][$method->id] = [
                'count' => 0,
                'total' => 0
            ];
        }

        //for now create a non existent key
        $report['towns']['-1'] = [
            'count' => 0,
            'total' => 0
        ];


        //now loop through the results and save the data
        foreach ($data as $key ) {

            $p_method = $key->payment_method;

            $order_amount = $key->total - $key->shipping;

            if($key->order_data != null)
            {
                $order_data = unserialize($key->order_data);

                $region = $order_data['gt_region'];
                $seller = $order_data['gt_seller'];
                $town = isset($order_data['gt_town']) ? $order_data['gt_town'] : '-1';

                $report['sellers'][$seller]['count'] += 1;
                $report['sellers'][$seller]['total'] += $order_amount;

                $report['regions'][$region]['count'] += 1;
                $report['regions'][$region]['total'] += $order_amount;

                $report['towns'][$town]['count'] += 1;
                $report['towns'][$town]['total'] += $order_amount;

            }

            //count the shipping method
            if(array_key_exists($p_method, $report['payment_methods']))
            {
                $report['payment_methods'][$p_method]['count'] += 1;
                $report['payment_methods'][$p_method]['total'] += $order_amount;
            }

        }

        return $report;
    }

    private function getStatsQuery()
    {
        $sql = "SELECT {$this->wpdb->posts}.*,
                    wp_postmeta.meta_value as order_total
                from {$this->wpdb->posts}
                INNER JOIN `wp_postmeta`
                    ON wp_posts.ID = wp_postmeta.post_id
                    AND wp_postmeta.meta_key = '_order_total'
                where
                    `post_date` >= '$this->start_date'
                    AND `post_status` <> 'auto-draft'
                    AND `post_date` <= '$this->end_date'
                    and `post_type` = 'shop_order'
                ";

        return $sql;
    }

    private function getStatsShippingQuery()
    {
        $sql = "SELECT {$this->wpdb->posts}.*,
                    wp_postmeta.meta_value as shipping
                from {$this->wpdb->posts}
                INNER JOIN `wp_postmeta`
                    ON wp_posts.ID = wp_postmeta.post_id
                    AND wp_postmeta.meta_key = '_order_shipping'
                where
                    `post_date` >= '$this->start_date'
                    AND `post_status` <> 'auto-draft'
                    AND `post_date` <= '$this->end_date'
                    and `post_type` = 'shop_order'
                ";

        return $sql;
    }

    public function getCategoryData($category_id)
    {
        $term_ids    = get_term_children( $category_id, 'product_cat' );
		$term_ids[]  = $category_id;
		$product_ids = get_objects_in_term( $term_ids, 'product_cat' );

        if($this->items_gotten == false)
        {
            $order_items = $this->get_order_report_data(
                array(
                    'data'         => array(
                        '_product_id' => array(
                            'type'            => 'order_item_meta',
                            'order_item_type' => 'line_item',
                            'function'        => '',
                            'name'            => 'product_id',
                        ),
                        '_line_total' => array(
                            'type'            => 'order_item_meta',
                            'order_item_type' => 'line_item',
                            'function'        => 'SUM',
                            'name'            => 'order_item_amount',
                        ),
                        'post_date'   => array(
                            'type'     => 'post_data',
                            'function' => '',
                            'name'     => 'post_date',
                        ),
                    ),
                    'group_by'     => 'ID, product_id, post_date',
                    'query_type'   => 'get_results',
                    'filter_range' => true,
                )
            );

            foreach($order_items as $order_item)
            {
                if(! array_key_exists($order_item->product_id, $this->order_items))
                {
                    $this->order_items[$order_item->product_id] = [
                        'count' => 0,
                        'total' => 0
                    ];
                }

                //now add the items
                $this->order_items[$order_item->product_id]['count'] += 1;
                $this->order_items[$order_item->product_id]['total'] += $order_item->order_item_amount;
            }

            $this->items_gotten = true;
        }



        $total = 0;
        $count = 0;

        //now filter only the products in the required category
        foreach ( $product_ids as $id ) {
            // var_dump($id); die();

            if ( isset( $this->order_items[ $id ] ) ) {
                $count += $this->order_items[ $id ]['count'];
                $total += $this->order_items[ $id ]['total'];
            }
        }

        return [
            'order_count' => $count,
            'order_total' => $total
        ];
    }

    public function get_order_report_data( $args = array() ) {
		global $wpdb;


		$default_args = array(
			'data'                => array(),
			'where'               => array(),
			'where_meta'          => array(),
			'query_type'          => 'get_row',
			'group_by'            => '',
			'order_by'            => '',
			'limit'               => '',
			'filter_range'        => false,
			'nocache'             => true,
			'debug'               => false,
			'order_types'         => wc_get_order_types( 'reports' ),
			'order_status'        => array( 'completed', 'processing', 'on-hold', 'pending' ),
			'parent_order_status' => false,
		);
		$args         = apply_filters( 'woocommerce_reports_get_order_report_data_args', $args );
		$args         = wp_parse_args( $args, $default_args );

		extract( $args );

		if ( empty( $data ) ) {
			return '';
		}

		$order_status = apply_filters( 'woocommerce_reports_order_statuses', $order_status );

		$query  = array();
		$select = array();

		foreach ( $data as $raw_key => $value ) {
			$key      = sanitize_key( $raw_key );
			$distinct = '';

			if ( isset( $value['distinct'] ) ) {
				$distinct = 'DISTINCT';
			}

			switch ( $value['type'] ) {
				case 'meta':
					$get_key = "meta_{$key}.meta_value";
					break;
				case 'parent_meta':
					$get_key = "parent_meta_{$key}.meta_value";
					break;
				case 'post_data':
					$get_key = "posts.{$key}";
					break;
				case 'order_item_meta':
					$get_key = "order_item_meta_{$key}.meta_value";
					break;
				case 'order_item':
					$get_key = "order_items.{$key}";
					break;
				default:
					break;
			}

			if ( $value['function'] ) {
				$get = "{$value['function']}({$distinct} {$get_key})";
			} else {
				$get = "{$distinct} {$get_key}";
			}

			$select[] = "{$get} as {$value['name']}";
		}

		$query['select'] = 'SELECT ' . implode( ',', $select );
		$query['from']   = "FROM {$wpdb->posts} AS posts";

		// Joins
		$joins = array();

		foreach ( ( $data + $where ) as $raw_key => $value ) {
			$join_type = isset( $value['join_type'] ) ? $value['join_type'] : 'INNER';
			$type      = isset( $value['type'] ) ? $value['type'] : false;
			$key       = sanitize_key( $raw_key );

			switch ( $type ) {
				case 'meta':
					$joins[ "meta_{$key}" ] = "{$join_type} JOIN {$wpdb->postmeta} AS meta_{$key} ON ( posts.ID = meta_{$key}.post_id AND meta_{$key}.meta_key = '{$raw_key}' )";
					break;
				case 'parent_meta':
					$joins[ "parent_meta_{$key}" ] = "{$join_type} JOIN {$wpdb->postmeta} AS parent_meta_{$key} ON (posts.post_parent = parent_meta_{$key}.post_id) AND (parent_meta_{$key}.meta_key = '{$raw_key}')";
					break;
				case 'order_item_meta':
					$joins['order_items'] = "{$join_type} JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON (posts.ID = order_items.order_id)";

					if ( ! empty( $value['order_item_type'] ) ) {
						$joins['order_items'] .= " AND (order_items.order_item_type = '{$value['order_item_type']}')";
					}

					$joins[ "order_item_meta_{$key}" ] = "{$join_type} JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta_{$key} ON " .
														"(order_items.order_item_id = order_item_meta_{$key}.order_item_id) " .
														" AND (order_item_meta_{$key}.meta_key = '{$raw_key}')";
					break;
				case 'order_item':
					$joins['order_items'] = "{$join_type} JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON posts.ID = order_items.order_id";
					break;
			}
		}

		if ( ! empty( $where_meta ) ) {
			foreach ( $where_meta as $value ) {
				if ( ! is_array( $value ) ) {
					continue;
				}
				$join_type = isset( $value['join_type'] ) ? $value['join_type'] : 'INNER';
				$type      = isset( $value['type'] ) ? $value['type'] : false;
				$key       = sanitize_key( is_array( $value['meta_key'] ) ? $value['meta_key'][0] . '_array' : $value['meta_key'] );

				if ( 'order_item_meta' === $type ) {

					$joins['order_items']              = "{$join_type} JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON posts.ID = order_items.order_id";
					$joins[ "order_item_meta_{$key}" ] = "{$join_type} JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta_{$key} ON order_items.order_item_id = order_item_meta_{$key}.order_item_id";

				} else {
					// If we have a where clause for meta, join the postmeta table
					$joins[ "meta_{$key}" ] = "{$join_type} JOIN {$wpdb->postmeta} AS meta_{$key} ON posts.ID = meta_{$key}.post_id";
				}
			}
		}

		if ( ! empty( $parent_order_status ) ) {
			$joins['parent'] = "LEFT JOIN {$wpdb->posts} AS parent ON posts.post_parent = parent.ID";
		}

		$query['join'] = implode( ' ', $joins );

		$query['where'] = "
			WHERE 	posts.post_type 	IN ( '" . implode( "','", $order_types ) . "' )
			";

		if ( ! empty( $order_status ) ) {
			$query['where'] .= "
				AND 	posts.post_status 	IN ( 'wc-" . implode( "','wc-", $order_status ) . "')
			";
		}

		if ( ! empty( $parent_order_status ) ) {
			if ( ! empty( $order_status ) ) {
				$query['where'] .= " AND ( parent.post_status IN ( 'wc-" . implode( "','wc-", $parent_order_status ) . "') OR parent.ID IS NULL ) ";
			} else {
				$query['where'] .= " AND parent.post_status IN ( 'wc-" . implode( "','wc-", $parent_order_status ) . "') ";
			}
		}

		if ( $filter_range ) {
			$query['where'] .= "
				AND 	posts.post_date >= '" . $this->start_date . "'
				AND 	posts.post_date < '" . $this->end_date . "'
			";
		}

		if ( ! empty( $where_meta ) ) {

			$relation = isset( $where_meta['relation'] ) ? $where_meta['relation'] : 'AND';

			$query['where'] .= ' AND (';

			foreach ( $where_meta as $index => $value ) {

				if ( ! is_array( $value ) ) {
					continue;
				}

				$key = sanitize_key( is_array( $value['meta_key'] ) ? $value['meta_key'][0] . '_array' : $value['meta_key'] );

				if ( strtolower( $value['operator'] ) == 'in' || strtolower( $value['operator'] ) == 'not in' ) {

					if ( is_array( $value['meta_value'] ) ) {
						$value['meta_value'] = implode( "','", $value['meta_value'] );
					}

					if ( ! empty( $value['meta_value'] ) ) {
						$where_value = "{$value['operator']} ('{$value['meta_value']}')";
					}
				} else {
					$where_value = "{$value['operator']} '{$value['meta_value']}'";
				}

				if ( ! empty( $where_value ) ) {
					if ( $index > 0 ) {
						$query['where'] .= ' ' . $relation;
					}

					if ( isset( $value['type'] ) && 'order_item_meta' === $value['type'] ) {

						if ( is_array( $value['meta_key'] ) ) {
							$query['where'] .= " ( order_item_meta_{$key}.meta_key   IN ('" . implode( "','", $value['meta_key'] ) . "')";
						} else {
							$query['where'] .= " ( order_item_meta_{$key}.meta_key   = '{$value['meta_key']}'";
						}

						$query['where'] .= " AND order_item_meta_{$key}.meta_value {$where_value} )";
					} else {

						if ( is_array( $value['meta_key'] ) ) {
							$query['where'] .= " ( meta_{$key}.meta_key   IN ('" . implode( "','", $value['meta_key'] ) . "')";
						} else {
							$query['where'] .= " ( meta_{$key}.meta_key   = '{$value['meta_key']}'";
						}

						$query['where'] .= " AND meta_{$key}.meta_value {$where_value} )";
					}
				}
			}

			$query['where'] .= ')';
		}

		if ( ! empty( $where ) ) {

			foreach ( $where as $value ) {

				if ( strtolower( $value['operator'] ) == 'in' || strtolower( $value['operator'] ) == 'not in' ) {

					if ( is_array( $value['value'] ) ) {
						$value['value'] = implode( "','", $value['value'] );
					}

					if ( ! empty( $value['value'] ) ) {
						$where_value = "{$value['operator']} ('{$value['value']}')";
					}
				} else {
					$where_value = "{$value['operator']} '{$value['value']}'";
				}

				if ( ! empty( $where_value ) ) {
					$query['where'] .= " AND {$value['key']} {$where_value}";
				}
			}
		}

		if ( $group_by ) {
			$query['group_by'] = "GROUP BY {$group_by}";
		}

		if ( $order_by ) {
			$query['order_by'] = "ORDER BY {$order_by}";
		}

		if ( $limit ) {
			$query['limit'] = "LIMIT {$limit}";
		}

		$query          = apply_filters( 'woocommerce_reports_get_order_report_query', $query );
		$query          = implode( ' ', $query );
		$query_hash     = md5( $query_type . $query );
		$cached_results = get_transient( strtolower( get_class( $this ) ) );

		if ( $debug ) {
			echo '<pre>';
			wc_print_r( $query );
			echo '</pre>';
		}


		if ( $debug || $nocache || false === $cached_results || ! isset( $cached_results[ $query_hash ] ) ) {
			static $big_selects = false;
			// Enable big selects for reports, just once for this session
			if ( ! $big_selects ) {
				$wpdb->query( 'SET SESSION SQL_BIG_SELECTS=1' );
				$big_selects = true;
			}

			$cached_results[ $query_hash ] = apply_filters( 'woocommerce_reports_get_order_report_data', $wpdb->$query_type( $query ), $data );
			set_transient( strtolower( get_class( $this ) ), $cached_results, DAY_IN_SECONDS );
		}

		$result = $cached_results[ $query_hash ];

		return $result;
	}

}

?>
