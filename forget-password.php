<?php require_once('user_header.php') ?>
    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
            <h3>Forget password</h3>
            <form class="forget-password-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required class="form-control" placeholder="example@domain.com"/>
                </div>
                <button type="submit" id="" class="btn btn-primary">Send Link</button>
            </form>
            <div id="messageboard"></div>
        </div>
    </div>
    <script type="text/javascript" src="js/reset-password.js"></script>
<?php require_once('user_footer.php') ?>