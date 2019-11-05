<input type="hidden" name="systemURL" id="userid_stripe_form" value="<?php echo $_COOKIE['loguserid']; ?>">
<script>
    var userid_stripe_form = document.getElementById('userid_stripe_form').value;
    $("#subscription #user_id_opt1").val(userid_stripe_form);
    $("#subscription #user_id_opt2").val(userid_stripe_form);
    $("#subscription #user_id_opt3").val(userid_stripe_form);
</script>