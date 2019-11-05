<div class="modal fade user-change-password-modal bd-example-modal-lg" id="user-change-password-modal" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header clearfix">
                <button type="button" class="close"
                        data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title pull-left" id="myModalLabel">
                    <?php echo _('Change Password') ?>
                </h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body" style=" max-height: calc(100vh - 200px);overflow-y: auto;">
                <form class="form-horizontal" role="form" id="change-password-modal" method="post">
                    <div class="form-group">
                        <label for="new_password" class="col-sm-2 control-label"><?php echo _('New Password:'); ?></label>
                        <div class="col-sm-10">
                            <input type="password" name="new_password" id="new_password" class="form-control"/>
                        </div>
                    </div>
                    <br>
                    <div class="form-group">
                        <label for="re_new_password" class="col-sm-2 control-label"><?php echo _('Retype New Password:'); ?></label>
                        <div class="col-sm-10">
                            <input type="password" name="re_new_password" id="re_new_password" class="form-control"/>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="hidden" name="change-password-userid" id="change-password-userid" value=""/>
                            <button class="btn btn-danger" data-dismiss="modal" type="button" class="close">Cancel</button>
                            <button type="submit" name="action" id="change-user-password" class="btn btn-primary"><?php echo _('Save'); ?></button>
                            <button type="submit" name="action" id="change-user-password-send" class="btn btn-success"><?php echo _('Save & Send'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

