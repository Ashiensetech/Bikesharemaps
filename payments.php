<?php 
require("config.php");
require("db.class.php");
require('actions-web.php');

$db=new Database($dbserver,$dbuser,$dbpassword,$dbname);
$db->connect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title><? echo $systemname; ?> <?php echo _('Payments | Donations '); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script type="text/javascript" src="https://js.braintreegateway.com/web/dropin/1.11.0/js/dropin.min.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/bootstrapValidator.min.js"></script>
<script type="text/javascript" src="js/translations.php"></script>
<script type="text/javascript" src="js/payments.js"></script>
<script type="text/javascript" src="js/logout.js"></script>
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.min.css" />
<link rel="stylesheet" type="text/css" href="css/bootstrapValidator.min.css" />
<script>
</script>
</head>
<body>
    <!-- Fixed navbar -->
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only"><?php echo _('Toggle navigation'); ?></span>
          </button>
          <a class="navbar-brand" href="<?php echo $systemURL; ?>"><?php echo $systemname; ?></a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="<?php echo $systemURL; ?>payments.php"><?php echo _('Payments | Donations'); ?></a></li>
<?php if (isloggedin()): ?>
<!--            <li><a href="command.php?action=logout" id="logout">--><?php //echo _('Log out'); ?><!--</a></li>-->
            <li><a class="user-logout" id="logout"><?php echo _('Log out'); ?></a></li>
<?php endif; ?>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>
<br />
    <div class="container">

      <div class="page-header">
            <h1><?php echo _('Donations'); ?></h1>
      </div>
      <div class="content">
      
      <form id="payments"> 
      <div class="form-group"><label for="amount" class="control-label"><?php echo _('Amount:'); ?></label> <input type="text" name="amount" id="amount" class="form-control" placeholder="50.00"  /></div>
      <div class="form-group"><label for="currency" class="control-label"><?php echo _('Currency:'); ?></label> 
      <select name="currency" id="currency" class="form-control" >
        <option value="USD">USD</option>
        <option value="EUR">EUR</option>
      </select> 
      </div>
      </form>

      <div id="buttons">
        <button class="btn btn-primary" id="view-card">Choose a way to pay</button>
      </div>
      </br>
      <div id="message" class="alert alert-danger"><?php echo _('Please fill in amount'); ?></div>

      <div id="card">
      <div id="dropin-container"></div>
      <button id="card-button" class="btn btn-primary">Donate</button>
      </div>

      </div>
    </div>
</body>
