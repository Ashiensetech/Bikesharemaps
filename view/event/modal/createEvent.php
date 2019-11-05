<div class="modal fade add-event-modal bd-example-modal-lg" id="add_event" tabindex="-1" role="dialog"
     aria-labelledby="add_event_Label" aria-hidden="true">
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
                    <?php echo _('Add Event') ?>
                </h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body" style="max-height: calc(100vh - 200px);overflow-y: auto;">
                <form id="addevent" enctype="multipart/form-data">
                    <div><h3><?php echo _('New Event') ?></h3></div>
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

                    <div class="form-group"><label for="event_description"><?php echo _('Description:'); ?></label> <textarea rows="4"
                                                                                                                              cols="40"
                                                                                                                              name="event_description"
                                                                                                                              id="event_description"
                                                                                                                              class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="totalrides"><?php echo _('Maximum Attendees:'); ?></label>
                        <input  type="number" min="1" class="form-control" name="totalrides" id="totalrides" >
                    </div>
                    <div class="form-group">
                        <label for="startdate"><?php echo _('Start Date:'); ?></label>

                        <input  type="text" class="form-control datepicker" name="startdate" id="add_startdate" >
                    </div>

                    <div class="form-group">
                        <label for="enddate"><?php echo _('End Date:'); ?></label>

                        <input  type="text" class="form-control datepicker" name="enddate" id="add_enddate" >
                    </div>

                    <div class="form-group">
                        <label for="rsvpdate"><?php echo _('RSVP Date:'); ?></label>

                        <input  type="text" class="form-control datepicker" name="rsvpdate" id="add_rsvpdate" >
                    </div>


                    <br>
                    <button type="button" id="savenewevent"
                            class="btn btn-primary"><?php echo _('Save'); ?></button>
                    </br>
                    </br>
                </form>
            </div>
        </div>
    </div>
</div>

