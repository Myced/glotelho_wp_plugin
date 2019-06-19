<div class="wrap">
    <h3>Glotelho Settings</h3>

    <br>

    <?php settings_errors(); ?>

    <form class="" action="options.php" method="post">
        <?php
            settings_fields("gt_admin_settings");
            do_settings_sections('gt_settings');
            // submit_button();
        ?>
    </form>

</div>

<div class="content">
    <div class="row">
        <div class="col-md-12">
            There are no settings at the moment
        </div>
    </div>
</div>
