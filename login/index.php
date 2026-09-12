<?php  
  
  session_start();

  if (isset($_SESSION["userType"])) {
    
    if ($_SESSION["userType"] == 'Admin') {
      
      header("Location: ../admin/");

    } else if ($_SESSION["userType"] == 'Judge' || $_SESSION["userType"] == 'Chairman') {
      
      header("Location: ../judge/");

    }
    
  }

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="shortcut icon" href="../images/header_logo.png" />
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  
  <title>ACLC House Cup - Intramurals 2019</title>
  <!-- Bootstrap core CSS-->
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom fonts for this template-->
  <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <!-- Custom styles for this template-->
  <link href="../css/sb-admin.css" rel="stylesheet">

  <link rel="stylesheet" href="../animator/animate.css">

  <style>
    .my-span {
      display: inline-block; 
      width: 43px; 
      height: 50px;  
      padding: 14px;
    }
  </style>
</head>

<body class="bg-dark">
  <div class="container">
    <br>
    <center>
      <img src="../images/header_logo.png" class="img-fluid" style="width: 100px">
    </center>
    <div class="card card-login mx-auto mt-5" id="loginContainer">
      <div class="card-header" style="font-size: 20px;">
         Login Portal
      </div>
      <div class="card-body">

        <form>
          <div class="form-group">
            <div class="input-group">
              <span class="input-group-addon">
                <span class="rounded-left border border-right-0 bg-light my-span" id="usn">
                  <i class="fa fa-user" style="font-size: 20px;"></i>
                </span>
              </span>
              <input class="form-control form-control-lg validate" name="usn" type="text" title="Username" 
                    placeholder="Username" autofocus>
            </div>
          </div>
          
          <div class="form-group">
            <div class="input-group ">
              <span class="input-group-addon">
                <span class="rounded-left border border-right-0 bg-light my-span" id="psw">
                  <i class="fa fa-lock" style="font-size: 20px;"></i>
                </span>
              </span>
              <input class="form-control validate form-control-lg" name="psw" type="password" title="Password" 
                    placeholder="Password">
            </div>
          </div>

          <div class="form-group">
            <button class="btn btn-primary btn-block btn btn-lg" type="button" id="btnLogin" onclick="log_in();">
              <i class="fa fa-lock"></i> Login
            </button>
          </div>
        </form>
        <div class="text-center">
          <a class="d-block mt-3" href="https://www.facebook.com/pg/aclcmandaueph/about/?ref=page_internal" target="_blank"><u>Developed by: ACLC COLLEGE of Mandaue</u></a>
        </div>
      </div>
    </div>
  </div>


<!-- Bootstrap core JavaScript-->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Core plugin JavaScript-->
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>


<script src="../notify/notify.min.js"></script>

<script src="../animator/animate.js"></script>
  
<script src="../lib/validate.js"></script>

<script>

function log_in() {

  $isEmpty = validateIsEmpty();
  
  if ($isEmpty == false) {

    $('#btnLogin').attr('disabled', true);

    $.post("login.php",
    
      $("form").serialize()
    ,
    function(data, status){
      
      $('#btnLogin').attr('disabled', false);

      if (data == 'success') {

        location.reload();

      } else {

        $('#loginContainer').notify(data, {

          className: 'error',
          elementPosition: 'bottom right',
          autoHideDelay: 2000
        });

        $('#loginContainer').removeClass('animated flipInY').animateCss('headShake');
        
      }

    }).fail(function () {
        
        $('#btnLogin').attr('disabled', false);

        $.notify('Request Failed ! Please Check your Connection and Try Again', {

        className: 'error',
        globalPosition: 'bottom right',
        autoHideDelay: 5000
      });

    });

  } else {

    $('#loginContainer').removeClass('animated flipInY').animateCss('headShake');
  }
  
}

</script>

</body>

</html>
