<?php  
  
  session_start();

  if (isset($_SESSION["userType"])) {
    
    if ($_SESSION["userType"] == 'Admin') {
      
      header("Location: ../login/");
    }

  } else {

    header("Location: ../login/");
  }

  include '../connection/conn.php';

  $userId = $_SESSION["userId"];

  $sql = "SELECT * FROM tbl_users WHERE user_id = '$userId'";
  $result = $conn->query($sql);

  $row = $result->fetch_assoc();

  $fullName = $row['full_name'];
  $_SESSION["userType"] = $row['user_type'];

  $judgeType = $_SESSION["userType"];

?>

<?php 
 
$sql = "SELECT * FROM tbl_config";
$result = $conn->query($sql);

$row = $result->fetch_assoc();

$title = $row['event_title'];
$type = $row['based_type'];

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="shortcut icon" href="../images/header_logo.png" />
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?php echo $title; ?></title>
  <!-- Bootstrap core CSS-->
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom fonts for this template-->
  <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <!-- Page level plugin CSS-->
  <link href="../vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
  <!-- Custom styles for this template-->
  <link href="../css/sb-admin.css" rel="stylesheet">

  <link rel="stylesheet" href="../animator/animate.css">

  <style>
    .slidecontainer {
      width: 100%;
    }

    .slider {
      -webkit-appearance: none;
      width: 100%;
      height: 15px;
      border-radius: 5px;
      background: #d3d3d3;
      outline: none;
      opacity: 0.7;
      -webkit-transition: .2s;
      transition: opacity .2s;
    }

    .slider:hover {
      opacity: 1;
    }

    .slider::-webkit-slider-thumb {
      -webkit-appearance: none;
      appearance: none;
      width: 25px;
      height: 25px;
      border-radius: 50%;
      background: #4CAF50;
      cursor: pointer;
    }

    .slider::-moz-range-thumb {
      width: 25px;
      height: 25px;
      border-radius: 50%;
      background: #4CAF50;
      cursor: pointer;
    }


    /* Style the Image Used to Trigger the Modal */
    .myImg {
        border-radius: 5px;
        cursor: pointer;
        transition: 0.3s;
    }

    .myImg:hover {opacity: 0.7;}

    /* The Modal (background) */
    #myModal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        padding-top: 120px; /* Location of the box */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
    }

    /* Modal Content (Image) */
    #img01 {
      margin: auto;
      display: block;
      width: 80%;
      max-width: 700px;
    }

    /* Caption of Modal Image (Image Text) - Same Width as the Image */
    #caption {
      margin: auto;
      display: block;
      width: 80%;
      max-width: 700px;
      text-align: center;
      color: #ccc;
      padding: 10px 0;
      height: 150px;
    }

    /* Add Animation - Zoom in the Modal */
    /* #img01, #caption {
      -webkit-animation-name: zoom;
      -webkit-animation-duration: 0.5s;
      animation-name: zoom;
      animation-duration: 0.5s;
    } */

    @-webkit-keyframes zoom {
      from {-webkit-transform:scale(0)}
      to {-webkit-transform:scale(1)}
    }

    @keyframes zoom {
      from {transform:scale(0)}
      to {transform:scale(1)}
    }

    /* The Close Button */
    #myClose {
      position: absolute;
      top: 75px;
      right: 20px;
      color: #f1f1f1;
      font-size: 40px;
      font-weight: bold;
      transition: 0.3s;
    }

    #myClose:hover,
    #myClose:focus {
      color: #bbb;
      text-decoration: none;
      cursor: pointer;
    }

    /* 100% Image Width on Smaller Screens */
    @media only screen and (max-width: 700px){
        #img01 {
          width: 90%;
        }
    }
  </style>
  
</head>

<body class="fixed-nav sticky-footer bg-dark" id="page-top">

  <!-- Navigation-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">

    <img src="../images/header_logo.png" style="width: 36px; height: 36px;"> 
    <a class="navbar-brand ml-2" href="javascript:void(0)"> 
      <span><?php echo $title; ?></span>
    </a>

    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarResponsive">

      <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">

        <li class="nav-item active" data-toggle="tooltip" data-placement="right" title="Candidates">
          <a class="nav-link" onclick="$('#content').load('candidates/candidates.php');">
            <i class="fa fa-fw fa-group"></i>
            <span class="nav-link-text"><?php echo $type; ?>s</span>
          </a>
        </li>
        
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Results">
          <a class="nav-link" onclick="$('#content').load('scores/scores.php');">
            <i class="fa fa-fw fa-star"></i>
            <span class="nav-link-text">My Scores</span>
          </a>
        </li>

      </ul>

      <ul class="navbar-nav sidenav-toggler">
        <li class="nav-item">
          <a class="nav-link text-center" id="sidenavToggler">
            <i class="fa fa-fw fa-angle-left"></i>
          </a>
        </li>
      </ul>

      <ul class="navbar-nav ml-auto">
        
        <li class="nav-item">
          <a class="nav-link">
            <i class="fa fa-fw fa-user"></i>
            <span><?php echo $fullName; ?></span> 
            (<span><?php echo $judgeType; ?></span>)
          </a> 
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="modal" data-target="#logoutModal">
          <i class="fa fa-fw fa-sign-out"></i> Log-out 
          </a> 
        </li>

      </ul>

    </div>

  </nav>

  <div class="content-wrapper">
    <div class="container-fluid" id="content">
    <!-- Page will load here -->
    </div>
  </div>

  <footer class="sticky-footer">
    <div class="container">
      <div class="text-center">
        <small>Developed By: ACLC College of Mandaue</small>
      </div>
    </div>
  </footer>

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fa fa-angle-up"></i>
  </a>

  <?php include '../admin/modal/logout_modal.php'; ?>
  <?php include 'modal/prompt_modal.php'; ?>


  <!-- Bootstrap core JavaScript-->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- Core plugin JavaScript-->
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <!-- Page level plugin JavaScript-->
  <script src="../vendor/chart.js/Chart.min.js"></script>
  <script src="../vendor/datatables/jquery.dataTables.js"></script>
  <script src="../vendor/datatables/dataTables.bootstrap4.js"></script>
  <!-- Custom scripts for all pages-->
  <script src="../js/sb-admin.min.js"></script>

  <script src="../notify/notify.min.js"></script>

  <script src="../animator/animate.js"></script>

  <!-- CRUD Ajax and Validation scripts -->
  <script src="../lib/validate.js"></script>
  <script src="../lib/crud.js"></script>


  <script>
    $('#content').load('candidates/candidates.php');

    $(".navbar-sidenav a").on("click", function() {
      $(".navbar-sidenav").find(".active").removeClass("active");
      $(this).parent().addClass("active");
    });

    function isNumberKey(evt)
    {
       var charCode = (evt.which) ? evt.which : event.keyCode
       if (charCode > 31 && (charCode < 48 || charCode > 57))
          return false;

       return true;
    }
    
    $(document).ready(function(){

      setInterval(function () {
        
        $.post("controller.php", function(data, status){

          if (data == "Opened") {

            $('#promptModal').modal('hide');
            
          } else {

            $('#formContent').html(null);
            $('#formModal').modal('hide');
            
            $('#promptMsg').html(data);
            $('#promptModal').modal('show');

            $('#records').load('candidates/view.php');
            $('#scores').load('scores/view.php');
            
          }
          
        }).fail(function () {

            $('#formContent').html(null);
            $('#formModal').modal('hide');

            $.notify('You have been disconnected from the server !', {

            className: 'error',
            globalPosition: 'top right',
            autoHideDelay: 5000
          });

        });

      }, 2000);

    });

  </script>


</body>

</html>
