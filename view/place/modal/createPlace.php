<div class="modal fade add-place-modal bd-example-modal-lg" id="add_place_details" tabindex="-1" role="dialog"
     aria-labelledby="add_place_Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close"
                        data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="add_event_Label">
                    <?php echo _('Add Details') ?>
                </h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body" style="max-height: calc(100vh - 200px);overflow-y: auto;">
                <form id="addplace" enctype="multipart/form-data">
                    <div class="form-group"><label for="placename"
                                                   class="control-label"><?php echo _('Name:'); ?></label>
                        <input type="text" name="placename" id="placename" class="form-control" required="required"/></div>

                    <div class="form-group"><label for="place_image"><?php echo _('Picture:'); ?></label>
                        <input type="file" name="place_image" id="place_image" required="required"/>
                    </div>
                    <div class="form-group"><label
                                for="place_description"><?php echo _('Short Description:'); ?></label> <textarea
                                rows="4"
                                cols="40"
                                name="place_description"
                                id="place_description"
                                class="form-control"></textarea>
                    </div>
                    <div class="form-group"><label for="placelat"
                                                   class="control-label"><?php echo _('Latitude:'); ?></label>
                        <input type="text" name="placelat" id="placelat" class="form-control" required="required"/></div>
                    <div class="form-group"><label for="placelon"
                                                   class="control-label"><?php echo _('Longitude:'); ?></label>
                        <input type="text" name="placelon" id="placelon" class="form-control" required="required"/></div>
                    <div class="form-group"><label for="placelink"
                                                   class="control-label" id="placelink-label"><?php echo _('Button Link:'); ?></label>
                        <input type="text" name="placelink" id="placelink" class="form-control" required="required"/></div>
                    <br>
                    <button type="button" id="save-new-place"
                            class="btn btn-primary"><?php echo _('Save'); ?></button>
                    </br>
                </form>
            </div>
        </div>
    </div>
</div>

