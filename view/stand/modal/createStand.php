<div class="modal fade add-stand-modal bd-example-modal-lg" id="add_stand" tabindex="-1" role="dialog"
     aria-labelledby="add_stand_Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close"
                        data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="add_stand_Label">
                    <?php echo _('Add Arrowhead') ?>
                </h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body" style="max-height: calc(100vh - 200px);overflow-y: auto;">
                <form class="container1" id="addstand" enctype="multipart/form-data">
                    <div><h3><?php echo _('New Arrowhead') ?></h3></div>
                    <div class="form-group"><label for="standtype"
                                                   class="control-label"><?php echo _('Stand type:'); ?></label>

                        <select name="standtype" id="standtype" class="form-control">
                            <option value="bike_stand">Bike Stand</option>
                            <option value="watercraft_stand">Watercraft Stand</option>
                            <option value="event_stand">Event Stand</option>
                        </select>

                    </div>

                    <div class="form-group"><label for="standname"
                                                   class="control-label"><?php echo _('Arrowhead name:'); ?></label>
                        <input type="text" name="standname" id="standname" class="form-control"/></div>
                    <div class="form-group"><label for="description"
                                                   class="control-label"><?php echo _('Address:'); ?></label>
                        <input type="text" name="description" id="description" class="form-control"/></div>
                    <div class="form-group"><label for="standdescription"
                                                   class="control-label"><?php echo _('Description:'); ?></label> <input
                                type="text" name="standdescription" id="standdescription" class="form-control"/></div>

                    <div class="form-group">
                        <label for="latitude" class="control-label"><?php echo _('Latitude:'); ?></label> <input
                                type="text" name="latitude" id="latitude" class="form-control"/>
                        <label for="longitude" class="control-label"><?php echo _('Longitude:'); ?></label> <input
                                type="text" name="longitude" id="longitude" class="form-control"/>
                    </div>
                    <div class="form-group"><label for="file"
                                                   class="control-label"><?php echo _('Picture:'); ?></label> <input
                                type="file" name="file" id="file"/></div>
                    </br>
                    <button type="button" id="savenewstand"
                            class="btn btn-primary"><?php echo _('Save'); ?></button>
                    </br>
                </form>
            </div>
        </div>
    </div>
</div>

