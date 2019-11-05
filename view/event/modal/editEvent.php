<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<div class="modal fade edit-event-modal bd-example-modal-lg" id="edit_event" tabindex="-1" role="dialog"
     aria-labelledby="edit_event_Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close"
                        data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="edit_event_Label">
                    <?php echo _('Edit Event') ?>
                </h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body" style="max-height: calc(100vh - 200px);overflow-y: auto;">
                <form class="container1" id="editevent" enctype="multipart/form-data">
                    <div><h3><?php echo _('Edit Event') ?></h3></div>
                    <input class="form-control" type="hidden" name="bike_type" id="bike_type" value="event"/>
                    <div class="form-group"><label for="currentstand"
                                                   class="control-label"><?php echo _('Stand:'); ?></label> <select
                                name="currentstand" id="currentstand" class="form-control"></select></div>

                    <div class="form-group"><label for="event_num"><?php echo _('Event No:'); ?></label>
                        <input class="form-control" type="number" min="1" name="event_num" id="event_num"/>
                    </div>

                    <div class="form-group"><label for="is_active"
                                                   class="control-label"><?php echo _('Active:'); ?></label>
                        <select name="is_active" id="is_active" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div><img id="fileview" src="" class="rounded mx-auto d-block" width="200px" height="200px"/>
                    </div>

                    <div class="form-group"><label for="file"><?php echo _('Picture:'); ?></label> <input
                                type="file" name="file" id="file"/></div>

                    <div class="form-group"><label for="event_description"><?php echo _('Description:'); ?></label> <textarea rows="4"
                                                                                                           cols="40"
                                                                                                           name="event_description"
                                                                                                           id="event_description"
                                                                                                           class="form-control"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="totalrides"><?php echo _('Maximum Attendees:'); ?></label>
                        <input  type="number" min="1" class="form-control" name="edittotalrides" id="edittotalrides" >
                    </div>

                    <div class="form-group">
                        <label for="startdate"><?php echo _('Start Date:'); ?></label>

                        <input  type="text" class="form-control datepicker" name="startdate" id="startdate" >
                    </div>

                    <div class="form-group">
                        <label for="enddate"><?php echo _('End Date:'); ?></label>

                        <input  type="text" class="form-control datepicker" name="enddate" id="enddate" >
                    </div>

                    <div class="form-group">
                        <label for="rsvpdate"><?php echo _('RSVP Date:'); ?></label>

                        <input  type="text" class="form-control datepicker" name="rsvpdate" id="rsvpdate" >
                    </div>
                    <input type="hidden" name="eventid" id="eventid"/>
                    <br>
                    <button type="button" id="saveevent" class="btn btn-primary"><?php echo _('Save'); ?></button>
                    <button type="button" id="deleteevent"
                            class="btn btn-danger"><?php echo _('Decomission'); ?></button>
                    </br>
                    </br>
                </form>
            </div>
        </div>
    </div>
</div>

