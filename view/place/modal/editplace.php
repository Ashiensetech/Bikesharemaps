<div class="modal fade edit-place-modal bd-example-modal-lg" id="edit_place" tabindex="-1" role="dialog"
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
                    <?php echo _('Edit Details') ?>
                </h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body" style="max-height: calc(100vh - 200px);overflow-y: auto;">
                <form class="container1" id="editplace" enctype="multipart/form-data">
                    <input type="hidden" name="placeid" id="placeid"/>
                    <div class="form-group"><label for="placename"
                                                   class="control-label"><?php echo _('Name:'); ?></label>
                        <input type="text" name="placename" id="placename" class="form-control"/></div>
                    <div class="form-group"><label for="place_description"
                                                   class="control-label"><?php echo _('Short Description:'); ?></label>
                        <textarea
                            rows="4"
                            cols="40"
                            name="place_description"
                            id="place_description"
                            class="form-control"></textarea></div>
                    <div class="form-group">
                        <label for="placelat" class="control-label"><?php echo _('Latitude:'); ?></label> <input
                            type="text" name="placelat" id="placelat" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label for="placelon" class="control-label"><?php echo _('Longitude:'); ?></label> <input
                            type="text" name="placelon" id="placelon" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label for="placelink" class="control-label" id="placelink-label"><?php echo _('Button Link:'); ?></label> <input
                            type="text" name="placelink" id="placelink" class="form-control"/>
                    </div>
                    <div class="form-group"><label for="place_image"
                                                   class="control-label"><?php echo _('Picture:'); ?></label> <input
                            type="file" name="place_image" id="place_image"/></div>
                    <div><img id="fileview" src="" class="rounded mx-auto d-block" width="200px" height="200px"/>
                    </div>
                    </br>
                    <button type="button" id="save-place" class="btn btn-primary"><?php echo _('Save'); ?></button>
                    <button type="button" id="delete-place"
                            class="btn btn-danger"><?php echo _('Decomission'); ?></button>
                    </br>
                </form>
            </div>
        </div>
    </div>
</div>

