<?php
require_once('user_header.php');
require_once('vendor/autoload.php');
require_once('third_party_config/stripe/config_stripe.php');
?>

<?php if(isloggedin()): ?>
    <div id="messageboard"></div>
    <div role="tabpanel" id="menu-tabs">

        <!-- Nav tabs -->
        <ul class="nav nav-tabs nav-tab-style" role="tablist">
            <li role="presentation" class="active"><a href="#profile-wrap" aria-controls="profile-wrap" role="tab"
                                                      data-toggle="tab"><span class="glyphicon glyphicon-user"
                                                                              aria-hidden="true"></span> <?php echo _('Profile'); ?>
                </a></li>
            <li role="presentation"><a href="#subscription" aria-controls="subscription" role="tab" data-toggle="tab"><span
                            class="glyphicon glyphicon-envelope"
                            aria-hidden="true"></span> <?php echo _('Subscription'); ?></a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="profile-wrap">
                <?php include('./view/profile/edit_profile.php'); ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="subscription">
                <?php include('./view/subscription/stripe/stripe_form.php'); ?>
                <?php include('./view/profile/subscription.php'); ?>
            </div>

        </div>
    </div>
<?php endif; ?>

<script type="text/javascript" src="js/profile.js"></script>
<?php require_once('user_footer.php') ?>

<input type="hidden" value="<?php echo $_COOKIE['loguserid'];?>" id="loguserid">