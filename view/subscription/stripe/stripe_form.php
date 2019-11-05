<?php
//Stripe Form
?>
<div class="row">
    <div class="col-md-4 text-center">
        <div id="monthly-subscription-notice" style="margin-top: 25px;">
            <div class="notice-content"></div>
            <p style="display: none;" class="stripe-unsubscribe-monthly" id="stripe-unsubscribe-monthly">
                <button type="button" data- data-userid="<?php echo $_COOKIE['loguserid']?>" data-subtype="monthly" class="btn btn-danger stripe-unsubscription-button stripe-unsubscription-button-monthly" style="border-radius: 5px !important;">Cancel Subscription</button>
            </p>
        </div>
        <div class="stripe-checkout-wrap" id="monthly-subscription-form">
            <!-- Live key instead of test key while going live:
                 e.x. - pk_live_XXXXXX
            -->
            <form class="subscription_form" action="stripe_return.php" method="POST">
                <img src="images/monthly.png" class="btn_subscription" style="cursor: pointer; width: 260px; margin-top: 30px;">
                <input name="plan" type="hidden" value="monthly" />
                <input type="hidden" name="user_id" id="user_id_opt1" value="">
                <script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                        data-key="<?php echo $stripe['publishable_key']; ?>"
                        data-name="<?php echo $systemname; ?>"
                        data-image="images/monthly.png"
                        data-description="Access for a month"
                        data-amount="1000"
                        data-locale="auto"
                        data-label="Sign Me Up!"></script>
            </form>
        </div>
    </div>

    <div class="col-md-4 text-center">
        <div id="annual-subscription-notice" style="margin-top: 25px;">
            <div class="notice-content"></div>
            <p style="display: none;" class="stripe-unsubscribe-yearly" id="stripe-unsubscribe-yearly">
                <button type="button" data- data-userid="<?php echo $_COOKIE['loguserid']?>" data-subtype="annually" class="btn btn-danger stripe-unsubscription-button stripe-unsubscription-button-annually" style="border-radius: 5px !important;">Unsubscribe</button>
            </p>
        </div>
        <div class="stripe-checkout-wrap" id="annual-subscription-form">
            <!-- Live key instead of test key while going live:
                 e.x. - pk_live_XXXXXX
            -->
            <form class="subscription_form" action="stripe_return.php" method="POST">
                <img src="images/annually.png" class="btn_subscription" style="cursor: pointer; width: 290px;">
                <input name="plan" type="hidden" value="annually" />
                <input type="hidden" name="user_id" id="user_id_opt2" value="">
                <script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                        data-key="<?php echo $stripe['publishable_key']; ?>"
                        data-name="<?php echo $systemname; ?>"
                        data-image="images/annually.png"
                        data-description="Access for a year"
                        data-amount="9600"
                        data-locale="auto"
                        data-label="Sign Me Up!"></script>
            </form>
        </div>
    </div>

    <div class="col-md-4 text-center">
        <div id="family-weekend-notice" style="margin-top: 25px;">
            <div class="notice-content"></div>
        </div>
        <div class="stripe-checkout-wrap" id="family-weekend-form" >
            <!-- Live key instead of test key while going live:
                 e.x. - pk_live_XXXXXX
            -->
            <form class="subscription_form" action="stripe_return.php" method="POST">
                <img src="images/family_weekend.png" class="btn_subscription" style="cursor: pointer; width: 260px; margin-top: 30px;">
                <input name="plan" type="hidden" value="family_weekend" />
                <input type="hidden" name="user_id" id="user_id_opt3" value="">
                <script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                        data-key="<?php echo $stripe['publishable_key']; ?>"
                        data-name="<?php echo $systemname; ?>"
                        data-image="images/family_weekend.png"
                        data-description="Access for 2 days"
                        data-amount="2500"
                        data-locale="auto"
                        data-label="Sign Me Up!"></script>
            </form>
        </div>
    </div>
</div>

<script>
    $(function(){
        $('body .subscription_form').find('.stripe-button-el').hide();
        $('body').on('click','.btn_subscription',function(){
           $(this).closest('.subscription_form').find('.stripe-button-el').click();
        });
    });
</script>