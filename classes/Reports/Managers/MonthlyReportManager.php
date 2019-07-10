<?php
namespace App\Reports\Managers;

use App\Reports\OrderStatus;


class MonthlyReportManager
{

    public $wpdb;

    private $start_date;
    private $end_date;

    private $months = [];

    private $items_gotten = false;

    function __construct()
    {
        //initialise the application
        global $wpdb;

        $this->wpdb = $wpdb;

        $this->init_months();

    }

    public function init_months()
    {
        for($i = 1; $i <= 12; $i++)
        {
            $this->months[$i] = [
                'name' => $this->getMonthName($i),
                'count' => 0,
                'total' => 0
            ];
        }
    }

    private function getMonthName($id)
    {
        $months = [
            '1' => 'Jan', '2' => 'Feb', '3' => "Mar",
            '4' => "Apr", '5' => 'May', '6' => "Jun",
            '7' => 'Jul', '8' => 'Aug', '9' => 'Sep',
            '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'
        ];

        if(array_key_exists($id, $months))
        {
            return $months[$id];
        }

        return '';
    }

    public function get_data()
    {
        $results = $this->wpdb->get_results($this->getOrdersQuery());

        foreach($results as $row)
        {
            $amount = $row->total - $row->shipping;
            $month = $row->month;

            if(array_key_exists($month, $this->months))
            {
                $this->months[$month]['count'] += 1;
                $this->months[$month]['total'] += $amount;
            }
        }


        return $this->months;
    }

    private function getOrdersQuery()
    {
        $cancelled = OrderStatus::CANCELLED;
        $failed = OrderStatus::FAILED;

        $sql = " SELECT
                    wp_posts.ID,
                    wp_posts.post_date,
                    wp_posts.post_status,
                    MONTH(wp_posts.post_date) as month,
                    YEAR(wp_posts.post_date) as year,
                MAX(CASE WHEN (wp_postmeta.meta_key = '_order_total')
                    THEN wp_postmeta.meta_value ELSE NULL END) AS total,
                MAX(CASE WHEN (wp_postmeta.meta_key = '_order_shipping')
                    THEN wp_postmeta.meta_value ELSE NULL END) AS shipping

                FROM `wp_posts`
                LEFT JOIN `wp_postmeta`
                    ON wp_posts.ID = wp_postmeta.post_id
                WHERE
                    wp_posts.post_type = 'shop_order'
                    AND wp_posts.post_status <> 'auto-draft'
                    AND wp_posts.post_status <> '$cancelled'
                    AND wp_posts.post_status <> '$failed'

                GROUP BY wp_posts.ID
                ORDER BY wp_posts.ID
        ";

        return $sql;
    }


}

?>
