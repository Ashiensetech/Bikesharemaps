<div class="modal fade add-bike-modal bd-example-modal-lg" id="add_bike" tabindex="-1" role="dialog"
     aria-labelledby="add_bike_Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close"
                        data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="add_bike_Label">
                    <?php echo _('Create Bike') ?>
                </h4>
            </div>

            <!-- Modal Body -->
<!--            <div class="modal-body" style="max-height: calc(100vh - 200px);overflow-y: auto;">-->
            <div class="modal-body">
                <form id="addbicycle" enctype="multipart/form-data">
                    <div><h3><?php echo _('New Bike') ?></h3></div>
                    <div class="form-group">
                        <label for="currentstand"
                               class="control-label">
                            <?php echo _('Stand:'); ?></label>
                        <select name="currentstand" id="currentstand" class="form-control">

                        </select>
                    </div>

<!--                    <div class="form-group"><label for="file">--><?php //echo _('Bike No:'); ?><!--</label>-->
<!--                        <input type="text" name="bike_no" id="bike_no"/>-->
<!--                    </div>-->

                    <div class="form-group"><label for="file"><?php echo _('Picture:'); ?></label>
                        <input type="file" name="file" id="file"/>
                    </div>
                    <br>
                    <button type="button" id="savenewbicycle"
                            class="btn btn-primary"><?php echo _('Save'); ?></button>
                    </br>
                    </br>
                </form>
            </div>
        </div>
    </div>
</div>

