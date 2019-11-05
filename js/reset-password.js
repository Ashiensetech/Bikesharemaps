$(document).ready(function () {
    $(document).on("submit", ".forget-password-form", function (e) {
        e.preventDefault();
        let email = $('#email').val().trim();
        if (email.length > 0) {
            $.ajax({
                url: "command.php?action=reset-password-send-link",
                method: "POST",
                data: {
                    email: email,
                }
            }).done(function (result) {
                var response = JSON.parse(result);
                $('#messageboard').show();
                $('#messageboard').html('<div class="alert alert-success" role="alert">' + response.content + '</div>').fadeOut(3000);
            });
        }
    });

    $(document).on("submit", ".reset-password-form", function (e) {
        e.preventDefault();
        let password = $('#password').val().trim();
        let confirm_password = $('#confirm-password').val().trim();
        if (password.length > 0 && password !== confirm_password) {
            $('#messageboard').show();
            $('#messageboard').html('<div class="alert alert-warning" role="alert">Confirm password does not match</div>').fadeOut(5000);
        } else {
            const hashKey = new URLSearchParams(window.location.search).get('q');
            if(hashKey == null){
                return;
            }
            $.ajax({
                url: "command.php?action=reset-password-form-submit",
                method: "POST",
                data: {
                    password: password,
                    hashKey: hashKey,
                }
            }).done(function (result) {
                var response = JSON.parse(result);
                $('#messageboard').show();
                $('#messageboard').html('<div class="alert alert-success" role="alert">' + response.content + '</div>').fadeOut(5000);
                setTimeout(function () {
                    goToHomePage();
                }, 5000); //will call the function after 5 secs.
            });
        }
    });
});

