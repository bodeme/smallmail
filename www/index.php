<?php

define('BASE_PATH', dirname(dirname(__FILE__)));

include(BASE_PATH . '/config/config.php');
include(BASE_PATH . '/vendor/autoload.php');

$error = '';
$message = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
  if(!isset($_POST['name'])) {
    $_POST['name'] = '';
  }
  if(!isset($_POST['email'])) {
    $_POST['email'] = '';
  }

  $_POST['name'] = trim($_POST['name']);

  if($_POST['name']) {
    $mail = new PHPMailer();
    $mail->setFrom($settings['mailfrom']);
    $mail->addAddress($settings['mailto']);
    $mail->Subject = $settings['subject'];
    $mail->Body = 'Mail von: ' . PHP_EOL . PHP_EOL;
    $mail->Body .= 'IP: ' . $_SERVER['REMOTE_ADDR'] . PHP_EOL;
    $mail->Body .= 'Name: ' . $_POST['name'] . PHP_EOL;
    $mail->Body .= 'Email: ' . $_POST['email'] . PHP_EOL;

    $mail->IsSMTP();
    $mail->Host = $settings['servername'];
    $mail->SMTPDebug = 0;
    $mail->SMTPAuth = true;
    $mail->Username = $settings['username'];
    $mail->Password = $settings['password'];
    $mail->Port = 465;
    $mail->SMTPSecure = "ssl";

    if($mail->send()) {
      $message = 'Message has been sent.';
    } else {
      $error = 'Message was not sent. Mailer error: ' . $mail->ErrorInfo;
    }
  } else {
    $error = 'Please fill in your name.';
  }

  $result = array();
  if($error) {
    $result['ok'] = false;
    $result['msg'] = $error;
  } else {
    $result['ok'] = true;
    $result['msg'] = $message;
  }
  echo json_encode($result);
  exit;
}

?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mail</title>
  <link href="../vendor/twitter/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="wrap">
  <div class="container">
    <h1><?php echo $settings['title']; ?></h1>
    <p>&nbsp;</p>
    <p><?php echo $settings['description']; ?></p>
    <p>&nbsp;</p>
    <div id="alert_info" class="alert alert-info" style="display: none;">
    </div>

    <div id="alert_success" class="alert alert-success" style="display: none;">
    </div>

    <div id="alert_failure" class="alert alert-danger" style="display: none;">
    </div>
    <form id="register">
      <div class="form-group row">
        <label for="inputName" class="col-sm-2 col-form-label">Name</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" id="inputName" placeholder="Name">
        </div>
      </div>

      <div class="form-group row">
        <label for="inputEmail3" class="col-sm-2 col-form-label">Email</label>
        <div class="col-sm-10">
          <input type="email" class="form-control" id="inputEmail3" placeholder="Email">
        </div>
      </div>

      <div class="form-group row">
        <div class="offset-sm-2 col-sm-10">
          <button id="send" type="button" class="btn btn-primary">Register</button>
        </div>
      </div>
    </form>




  </div>
</div>

<script src="../vendor/components/jquery/jquery.min.js"></script>
<script src="../vendor/twitter/bootstrap/dist/js/bootstrap.min.js"></script>

<script>
  $(document).ready(function() {
    $('button#send').on('click', function() {
      var $button = $(this);
      $button.prop('disabled', true);
      $('div#alert_success').hide()
      $('div#alert_failure').hide()
      $('div#alert_info').html('Please wait...');
      $('div#alert_info').show()

      $.post("index.php", {name: $('input#inputName').val(), email: $('input#inputEmail3').val()}, function() {
      }, "json"
      ).done(function(d) {
        console.log(d.ok);
        if(d.ok == true) {
          $('form#register').hide();
          $('div#alert_success').html(d.msg);
          $('div#alert_success').show();
        } else {
          $('div#alert_failure').html(d.msg);
          $('div#alert_failure').show();
        }
      }).fail(function(d) {
        console.log(d);
        $('div#alert_failure').html('Unknown error.');
        $('div#alert_failure').show();
      }).always(function() {
        $button.prop('disabled', false);
        $('div#alert_info').hide()
      });
    });
  });
</script>
</body>
</html>

