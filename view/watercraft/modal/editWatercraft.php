<div class="modal fade edit-watercraft-modal bd-example-modal-lg" id="edit_watercraft" tabindex="-1" role="dialog"
     aria-labelledby="edit_watercraft_Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close"
                        data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="edit_watercraft_Label">
                    <?php echo _('Edit Watercraft') ?>
                </h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body" style="max-height: calc(100vh - 200px);overflow-y: auto;">
                <form class="container1" id="editwatercraft" enctype="multipart/form-data">
                    <div><h3><?php echo _('Edit Watercraft') ?></h3></div>
                    <input class="form-control" type="hidden" name="bike_type" id="bike_type" value="watercraft"/>

                    <div class="form-group"><label for="currentstand"
                                                   class="control-label"><?php echo _('Stand:'); ?></label> <select
                                name="currentstand" id="currentstand" class="form-control"></select></div>
                    <div class="form-group"><label for="bike_no"><?php echo _('Watercraft No:'); ?></label>
                        <input class="form-control" type="number" min="1" name="bike_no" id="bike_no"/>
                    </div>
                    <div class="form-group"><label for="active"
                                                   class="control-label"><?php echo _('Active:'); ?></label>
                        <select name="active" id="watercraft_status" class="form-control">
                            <option value="Y">Active</option>
                            <option value="N">Inactive</option>
                        </select>
                    </div>
                    <div><img id="fileview" src="" class="rounded mx-auto d-block" width="200px" height="200px"/>
                    </div>
                    <div class="form-group"><label for="file"><?php echo _('Picture:'); ?></label> <input
                                type="file" name="file" id="file"/></div>
                    <div class="form-group"><label for="note"><?php echo _('Notes:'); ?></label> <textarea rows="4"
                                                                                                           cols="40"
                                                                                                           name="note"
                                                                                                           id="note"
                                                                                                           class="form-control"></textarea>
                    </div>
                    <input type="hidden" name="watercraftid" id="watercraftid"/>
                    <br>
                    <button type="button" id="savewatercraft" class="btn btn-primary"><?php echo _('Save'); ?></button>
                    <button type="button" id="deletewatercraft"
                            class="btn btn-danger"><?php echo _('Decomission'); ?></button>
                    </br>
                    </br>
                </form>
            </div>
        </div>
    </div>
</div>

