<?php
namespace App\Google;


class GAAnalytics
{

    public function register()
    {
        add_action('wp_head', [$this, 'register_ga_tag'], 1);
    }

    public function register_ga_tag()
    {
        ?>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <!-- <script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script> -->

        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-110656135-1"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());

          gtag('config', 'UA-110656135-1');
          gtag('config', 'UA-110656135-1', { 'send_page_view': true });
        </script>

        <meta name="p:domain_verify" content="d8660fde4b5fcd6ba43e9bfc5360d43b"/>
        <?php
    }
}


 ?>
