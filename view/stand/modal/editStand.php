<div class="modal fade edit-stand-modal bd-example-modal-lg" id="edit_stand" tabindex="-1" role="dialog"
     aria-labelledby="edit_stand_Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close"
                        data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="edit_stand_Label">
                    <?php echo _('Edit Arrowhead') ?>
                </h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body" style="max-height: calc(100vh - 200px);overflow-y: auto;">
                <form class="container1" id="editstand" enctype="multipart/form-data">
                    <div><h3><?php echo _('Edit Arrowhead') ?></h3></div>
                    <input type="hidden" name="standid" id="standid"/>
                    <div class="form-group"><label for="type"
                                                   class="control-label"><?php echo _('Type:'); ?></label>
                        <select name="type" id="type" class="form-control">
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
                    <div class="form-group"><label for="active"
                                                   class="control-label"><?php echo _('Active:'); ?></label>
                        <select name="active" id="active" class="form-control">
                            <option value="Y">Active</option>
                            <option value="N">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="latitude" class="control-label"><?php echo _('Latitude:'); ?></label> <input
                                type="text" name="latitude" id="latitude" class="form-control"/>
                        <label for="longitude" class="control-label"><?php echo _('Longitude:'); ?></label> <input
                                type="text" name="longitude" id="longitude" class="form-control"/>
                    </div>
                    <div class="form-group"><label for="standphoto"
                                                   class="control-label"><?php echo _('Picture:'); ?></label> <input
                                type="file" name="file" id="file"/></div>
                    <div><img id="fileview" src="" class="rounded mx-auto d-block" width="200px" height="200px"/>
                    </div>
                    </br>
                    <button type="button" id="savestand" class="btn btn-primary"><?php echo _('Save'); ?></button>
                    <button type="button" id="deletestand"
                            class="btn btn-danger"><?php echo _('Decomission'); ?></button>
                    </br>
                </form>
            </div>
        </div>
    </div>
</div>

