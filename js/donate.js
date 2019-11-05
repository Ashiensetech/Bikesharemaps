$(document).ready(function(){
    // scroll to top for processing
    function scrollTo() {
        var hash = '#main';
        var destination = $(hash).offset().top;
        stopAnimatedScroll();
        $('html, body').stop().animate({
            scrollTop: destination
        }, 400, function() { window.location.hash = hash; });
        return false;
    }
    function stopAnimatedScroll(){
        if ( $('*:animated').length > 0 ) {$('*:animated').stop();}
    }
    if(window.addEventListener) {
        document.addEventListener('DOMMouseScroll', stopAnimatedScroll, false);
    }
    document.onmousewheel = stopAnimatedScroll;
    // prevent decimal in donation input
    $('#amt').keypress(function(event){
        preventDot(event);
    });
    function preventDot(event){
        var key = event.charCode ? event.charCode : event.keyCode;
        if (key == 46){
            event.preventDefault();
            return false;
        }
    }
    function showProcessing() {
        scrollTo();
        $('.donate-process').addClass('show').attr('aria-expanded', 'true');
        $('.donate-thanks, .donate-alert').removeClass('show').attr('aria-expanded', 'false');
    }
    function hideProcessing() {
        $('.donate-process').removeClass('show').attr('aria-expanded', 'false');
    }
    var stripe_pk = $('#stripe_pk').val();
    // set up Stripe config, ajax post to charge
    var handler = StripeCheckout.configure({
        key: stripe_pk,
        image: 'img/icon-bike.svg',
        closed: function(){document.getElementById('donateNow').removeAttribute('disabled');},
        token: function(token) {
            $.ajax({
                url: 'donate.php',
                type: 'POST',
                dataType: 'json',
                beforeSend: showProcessing,
                data: {stripeToken: token.id, stripeEmail: token.email, donationAmt: donationAmt},
                success: function(data) {
                    hideProcessing();
                    $('#amt').val('');
                    if (data.error!='') {
                        $('.donate-alert').addClass('show').text(data.error).attr('aria-expanded', 'true');
                    } else {
                        $('.donate-thanks').addClass('show').text(data.success).attr('aria-expanded', 'true');
                        $('#donateFormRow').hide();
                    }
                },
                error: function(data) {
                    $('.donate-alert').show().text(data).attr('aria-expanded', 'true');
                }
            });
        }
    });
    // donate now button, open Checkout
    $('#donateNow').click(function(e) {
        // strip non-numbers from amount and convert to cents
        donationAmt = document.getElementById('amt').value.replace(/\D/g,'') + '00';
        // make sure there is an amount
        if (donationAmt < 1) {
            $('#amt').val('').focus();
            e.preventDefault();
        } else {
            $('#donateNow').attr('disabled', 'disabled');
            // Open Checkout
            handler.open({
                name: 'Online Donation',
                description: 'Donate us',
                amount: donationAmt,
                billingAddress: false
            });
            e.preventDefault();
        }
    });
    // quick-add amount buttons
    $('.btn-amt').click(function() {
        var insert = $.parseJSON($(this).attr('data-amt'));
        $('#amt').val(insert);
    });
    // Close Checkout on page navigation
    $(window).on('popstate', function() {
        handler.close();
    });
});