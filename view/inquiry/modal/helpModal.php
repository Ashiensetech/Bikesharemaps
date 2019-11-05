<div class="modal fade help-answer-modal bd-example-modal-lg" id="help-answer" tabindex="-1" role="dialog"
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
                    <?php echo _('Question & Answer') ?>
                </h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body" style=" max-height: calc(100vh - 200px);overflow-y: auto;">
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo _('Question:'); ?></label>
                        <span class="help-modal-question"></span>
                    </div>
                    <div class="form-group">
                        <label for="help-modal-answer" class="col-sm-2 control-label"><?php echo _('Answer:'); ?></label>
                        <div class="col-sm-10">
                            <textarea name="help-modal-answer" id="help-modal-answer" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="hidden" name="inquiryid" id="inquiryid" value=""/>
                            <button class="btn btn-danger" data-dismiss="modal" type="button" class="close">Cancel</button>
                            <button type="button" id="save-help-answer" class="btn btn-primary"><?php echo _('Save'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

