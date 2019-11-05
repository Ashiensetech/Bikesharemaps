<!--Profile edit -->
<div class="row clearfix">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <form class="container " id="profile">
            <h2 id="profiletitle"><?php echo _('Profile'); ?></h2>
            <h4 id="hint"><?php echo _('Click the save button to save changes'); ?></h4>
            <div class="form-group">
                <label for="phonenumber"><?php echo _('Phone number:'); ?></label> <input type="text" name="phonenumber" id="phonenumber" class="form-control" readonly /></div>
            <div class="row clearfix">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label for="username"><?php echo _('Fullname:'); ?></label> <input type="text" name="username" id="username" class="form-control"  /></div>
                    <div class="form-group">
                        <label for="useremail"><?php echo _('Email:'); ?></label> <input type="text" name="useremail" id="useremail" class="form-control"  /></div>
                    <div class="form-group">
                        <label for="userage"><?php echo _('Age:'); ?></label>
                        <select class="form-control" name="userage" id="userage">
                            <option value="18">Under 18</option>
                            <option value="18-29">18-29</option>
                            <option value="30-44">30-44</option>
                            <option value="45-64">45-64</option>
                            <option value="65+">65+</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="usergender"><?php echo _('Gender:'); ?></label>
                        <select class="form-control" name="usergender" id="usergender">
                            <option value="M">Male</option>
                            <option value="F">Female</option>
                            <option value="no-answer">Prefer not to Answer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="userrace"><?php echo _('Race:'); ?></label>
                        <select class="form-control" name="userrace" id="userrace">
                            <option value="white">White</option>
                            <option value="hispanic-or-latino">Hispanic or Latino</option>
                            <option value="black-or-african-american">Black or African American</option>
                            <option value="native-american">Native American</option>
                            <option value="asian-or-pacific">Asian / Pacific Islander</option>
                            <option value="other">Other</option>
                            <option value="no-answer">Prefer not to Answer</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label for="mailingaddress"><?php echo _('Address(Line 1):'); ?></label> <input type="text" name="mailingaddress" id="mailingaddress" class="form-control"  /></div>
                    <div class="form-group">
                        <label for="physicaladdress"><?php echo _('Address(Line 2):'); ?></label> <input type="text" name="physicaladdress" id="physicaladdress" class="form-control"  /></div>
                    <div class="form-group">
                        <label for="city"><?php echo _('City/town:'); ?></label> <input type="text" name="city" id="city" class="form-control"  /></div>
                    <div class="form-group">
                        <label for="state"><?php echo _('State:'); ?></label> <input type="text" name="state" id="state" class="form-control"  /></div>
                    <div class="form-group">
                        <label for="zipcode"><?php echo _('zipcode:'); ?></label> <input type="text" name="zipcode" id="zipcode" class="form-control"  /></div>
                </div>
            </div>


            <button type="submit" id="saveprofile" class="btn btn-primary" ><?php echo _('Save'); ?></button>
            <button id="closeprofile" class="btn btn-primary" ><?php echo _('Close'); ?></button>
        </form>
    </div>
</div>