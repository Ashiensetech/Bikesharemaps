<?php require_once('user_header.php') ?>
    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
            <h3>Reset password</h3>

            <?php
            $q = $_GET['q'];
            if ($q == null) {
                echo '<div class="alert alert-warning" role="alert">' . 'Invalid request' . '</div>';
            } else {
                global $db;
                $queryParam = trim($_GET['q']);
                $thisTime = date("Y-m-d H:i:s");
                $result = $db->query("SELECT * FROM password_reset_request where hash_code='$queryParam' AND already_used=0 AND expired_at > '$thisTime'");
                if ($result->num_rows == 1) {
                    ?>
                    <div id="messageboard"></div>
                    <form class="reset-password-form">
                        <div class="form-group">
                            <label for="password">New password</label>
                            <input type="password" name="password" id="password" required class="form-control"
                                   placeholder="Password"/>
                        </div>
                        <div class="form-group">
                            <label for="confirm-password">Confirm password</label>
                            <input type="password" name="confirm-password" id="confirm-password" required
                                   class="form-control" placeholder="Confirm password"/>
                        </div>
                        <button type="submit" id="" class="btn btn-primary">Reset Password</button>
                    </form>
                    <?php
                } else {
                    echo '<div class="alert alert-warning" role="alert">' . 'Reset password request link expired.' . '</div>';
                }
            }
            ?>
        </div>
    </div>
    <script type="text/javascript" src="js/reset-password.js"></script>


<?php require_once('user_footer.php') ?>