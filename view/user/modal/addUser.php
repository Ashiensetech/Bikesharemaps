<div class="modal fade add-user-modal bd-example-modal-lg" id="adduser" tabindex="-1" role="dialog"
     aria-labelledby="addUserLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header clearfix">
                <button type="button" class="close"
                        data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title pull-left" id="addUserLabel">
                    <?php echo _('Add User') ?>
                </h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body" style=" max-height: calc(100vh - 200px);overflow-y: auto;">
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="username" class="col-sm-2 control-label"><?php echo _('Fullname:'); ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="add_username" id="add_username" class="form-control"/>
                        </div>
                    </div>
                    <br>
                    <div class="form-group">
                        <label for="email" class="col-sm-2 control-label"><?php echo _('Email:'); ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="add_email" id="add_email" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="phone" class="col-sm-2 control-label"><?php echo _('Phone:'); ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="add_phone" id="add_phone" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mailingaddress"
                               class="col-sm-2 control-label"><?php echo _('Address (Line 1):'); ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="add_mailingaddress" id="add_mailingaddress" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="physicaladdress"
                               class="col-sm-2 control-label"><?php echo _('Address(Line 2):'); ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="add_physicaladdress" id="add_physicaladdress" class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="city" class="col-sm-2 control-label"><?php echo _('City/Town:'); ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="add_city" id="add_city" class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="state" class="col-sm-2 control-label"><?php echo _('State:'); ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="add_state" id="add_state" class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="zipcode"
                               class="col-sm-2 control-label"><?php echo _('ZIP/ Postal Code:'); ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="add_zipcode" id="add_zipcode" class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group"><label for="active"
                                                   class="col-sm-2 control-label"><?php echo _('Gender:'); ?></label>
                        <div class="col-sm-10">   
                            <select name="add_gender" id="add_gender" class="form-control">
                                <option value="M">Male</option>
                                <option value="F">Female</option>
                                <option value="no-answer">Other</option>
                            </select>
                        </div> 
                    </div>

                    <div class="form-group">
                        <label for="active" class="col-sm-2 control-label"><?php echo _('Age:'); ?></label>
                        <div class="col-sm-10"> 
                            <select name="add_age" id="add_age" class="form-control">
                                <option value="18">18</option>
                                <option value="18-29">18-29</option>
                                <option value="30-44">30-44</option>
                                <option value="45-64">45-64</option>
                                <option value="65+">65+</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="active" class="col-sm-2 control-label"><?php echo _('Race:'); ?></label>
                        <div class="col-sm-10"> 
                            <select name="add_race" id="add_race" class="form-control">
                                <option value="white">white</option>
                                <option value="hispanic-or-latino">hispanic-or-latino</option>
                                <option value="black-or-african-american">black-or-african-american</option>
                                <option value="native-american">native-american</option>
                                <option value="asian-or-pacific">asian-or-pacific</option>
                                <option value="other">other</option>
                                <option value="no-answer">no-answer</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="active" class="col-sm-2 control-label"><?php echo _('User Type:'); ?></label>
                        <div class="col-sm-10"> 
                            <select name="add_privileges" id="add_privileges" class="form-control">
                                <option value="7">Admin</option>
                                <option value="0">User</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="limit" class="col-sm-2 control-label"><?php echo _('Bike limit:'); ?></label>
                        <div class="col-sm-10">
                            <select name="add_limit" id="add_limit" class="form-control">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="30">30</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="hidden" name="userid" id="userid" value=""/>
                            <button class="btn btn-danger" data-dismiss="modal" type="button" class="close">Cancel</button>
                            <button type="button" id="save_new_user" class="btn btn-primary"><?php echo _('Save'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

