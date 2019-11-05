var authorization='sandbox_rys7wk5q_z7r5vq23b8jb852c';
var amount='';
var currency='';
$(document).ready(function(){
//hide payment options
$('#card').hide();
$('#paypal-credit').hide();
$('#paypal').hide();
$('#message').hide();
//event listeners
$("#view-card").click(function() {
    getAmount();
    getCurrency();
    if(amount===''){
        $('#message').show();
    }else{
    var button = document.querySelector('#card-button');
    braintree.dropin.create({
      authorization: authorization,
      container: '#dropin-container',
      paypal: {
        flow: 'checkout',
        amount: amount,
        currency: currency
      },
      paypalCredit: {
        flow: 'checkout',
        amount: amount,
        currency: currency
      }
    }, function (createErr, instance) {
      button.addEventListener('click', function () {
        instance.requestPaymentMethod(function (requestPaymentMethodErr, payload) {
          // Submit payload.nonce to your server
        });
      });
    });
    $("#card").show();
    $('#message').hide();
    }
    return false;
});
function getCurrency()
{ 
    currency=$('#currency').val();
}
function getAmount()
{
    amount=$('#amount').val();
}
function auth()
{
   $.ajax({
   url: "command.php?action=braintree"
   }).done(function(jsonresponse) {
      jsonobject=$.parseJSON(jsonresponse);
      authorization=jsonobject["authorization"];
      //handleresponse("userconsole",jsonobject);
      //setTimeout(userlist, 2000);
   });
}
});