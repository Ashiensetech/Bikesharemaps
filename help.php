<?php require_once('user_header.php')?>
    <div id="flash"></div>
<div class="row">
    <form class="container" id="report">
        <h2 class="" id="reporttitle"><span><?php echo _('Online help'); ?></span></h2>
        <h4 id="hint"><?php echo _('How can we help you?'); ?></h4>
        <div class="form-group">
            <textarea cols="30" rows="5" name="inquiry" id="inquiry" class="form-control" ></textarea> </div>
        <?php if(!isloggedin()): ?>
            <div class="form-group">
                <label for="phonenumber"><?php echo _('Phone number:'); ?></label> <input type="text" name="phonenumber" id="phonenumber" class="form-control"  placeholder="<?php echo _('5551234567') ; ?>"/></div>
            <div class="form-group">
                <label for="useremail"><?php echo _('Email:'); ?></label> <input type="text" name="useremail" id="useremail" class="form-control"  placeholder="<?php echo _('example@domain.com') ?>" /></div>
        <?php endif; ?>

        <button type="submit" id="sendinquiry" class="btn btn-primary" ><?php echo _('Send report'); ?></button>
<!--        <a href="--><?php //echo $systemrules; ?><!--"  class="btn" >--><?php //echo _('Liability'); ?><!--</a>-->
        <a href="user_agreement.php"  class="btn" ><?php echo _('Liability'); ?></a>
    </form>
    <div class="user-help-data"></div>
</div>
    <script type="text/javascript" src="js/help.js"></script>
<?php require_once('user_footer.php')?>