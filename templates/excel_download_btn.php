<?php
$url =  basename($_SERVER['PHP_SELF']) . '?' . $_SERVER['QUERY_STRING'];
$url .= '&download=true';

?>

<br>
<div class="row">
    <div class="col-md-12">
        <a href="<?php echo $url; ?>" class="btn btn-success">
            <i class="fa fa-download"></i>
            Download Excel
        </a>
    </div>
</div>
