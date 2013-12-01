<?php

$okf_inactive_users = new OKF_Inactive_Users_List();
$okf_inactive_users->prepare_items();

?>
<div class="wrap">
        
    <div id="icon-users" class="icon32"><br/></div>
    <h2>Inactive Users</h2>

    <div style="background:#ECECEC;border:1px solid #CCC;padding:0 10px;margin-top:5px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">
        <p>Use this page to view and delete inactive users.</p>
        <p>Total number of users deleted by this utility: <?php echo $GLOBALS['okf_inactive_users']->get_total_deleted() ?>.</p>
    </div>    

    <form id="mu-site-admins" method="post">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <?php $okf_inactive_users->display(); ?>
    </form>

</div>