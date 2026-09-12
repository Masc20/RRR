<?php  
  
  session_start();

  if (isset($_SESSION["userType"])) {
    
    if ($_SESSION["userType"] == 'Judge' || $_SESSION["userType"] == 'Chairman') {
      
      header("Location: ../login/");
    }

  } else {

    header("Location: ../login/");
  }

?>

<?php  

  include '../connection/conn.php';
  
  $sql = "SELECT * FROM tbl_config";
  $result = $conn->query($sql);

  $row = $result->fetch_assoc();
  
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="shortcut icon" href="../images/header_logo.png" />
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title> <?php echo $row['event_title']; ?></title>
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
  /* Make the image fully responsive */
  .carousel-inner img {
      width: 100%;
      height: 100%;
  }
  </style>

</head>

<body class="fixed-nav sticky-footer bg-dark" id="page-top">

  <!-- Navigation-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">

    <img src="../images/header_logo.png" style="width: 36px; height: 36px;">
    <a class="ml-2 navbar-brand" href=""> <?php echo $row['event_title']; ?></a>

    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarResponsive">

      <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">
        <li class="nav-item active" data-toggle="tooltip" data-placement="right" title="Dashboard">
          <a class="nav-link" onclick="$('#content').load('dashboard/dashboard.php');">
            <i class="fa fa-fw fa-dashboard"></i>
            <span class="nav-link-text">Dashboard</span>
          </a>
        </li>
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Candidates" id="cand">
          <a class="nav-link" onclick="$('#content').load('candidates/candidates.php');">
            <i class="fa fa-fw fa-group"></i>
            <span class="nav-link-text"><?php echo $row['based_type']; ?>s Entry</span>
          </a>
        </li>
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Score Criteria" id="cat">
          <a class="nav-link" onclick="$('#content').load('category/category.php');">
            <i class="fa fa-fw fa-star"></i>
            <span class="nav-link-text">Categories</span>
          </a>
        </li>
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Score Criteria" id="crit">
          <a class="nav-link" onclick="$('#content').load('criteria/criteria.php');">
            <i class="fa fa-fw fa-edit"></i>
            <span class="nav-link-text">Score Criteria</span>
          </a>
        </li>
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Judges Account" id="jud">
          <a class="nav-link" onclick="$('#content').load('accounts/accounts.php');">
            <i class="fa fa-fw fa-address-card"></i>
            <span class="nav-link-text">Judges Account</span>
          </a>
        </li>
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Results">
          <a class="nav-link" onclick="$('#content').load('results/results.php');">
            <i class="fa fa-fw fa-bar-chart"></i>
            <span class="nav-link-text">Results</span>
          </a>
        </li>

        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Settings">
          <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseComponents" 
            data-parent="#exampleAccordion">
            <i class="fa fa-fw fa-cogs"></i>
            <span class="nav-link-text">Settings</span>
          </a>
          <ul class="sidenav-second-level collapse" id="collapseComponents">
            <li class="nav-item">
              <a class="nav-link" data-toggle="modal" data-target="#formModal"
                onclick="$('#myform').load('config/custom_form.php', function () {
                          update('config', false);
                        });
                      ">
              Configure</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-toggle="modal" data-target="#formModal" 
                onclick="
                        $('#myform').load('reset/custom_form.php', function () {
                          update('reset', false);
                        });
                      ">
                Reset Scores</a>
            </li>
          </ul>
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

        <li class="nav-item dropdown">
          
          <a class="nav-link dropdown-toggle mr-lg-2" id="alertsDropdown"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
            onclick="$('#contrMenu').load('controller/custom_form.php');">
            <i class="fa fa-fw fa-cog" style="font-size: 18px;"></i>
            <span class="d-lg-none">
              Controller
            </span>
            <span class="indicator text-warning d-none d-lg-block">
              <i class="fa fa-fw fa-circle" style="font-size: 12px;"></i>
            </span>
          </a>

          <div class="dropdown-menu" aria-labelledby="alertsDropdown" style="width: 300px;" id="contrMenu">
            <div class="container">Loading . . .</div>
          </div>
        </li>
        
        <li class="nav-item">
          <a class="nav-link text-white">
            <i class="fa fa-fw fa-user"></i> Administrator | 
          </a> 
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="modal" data-target="#logoutModal">
            Log-out <i class="fa fa-fw fa-sign-out"></i>
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
        <small>Copyright © ACLC COLLEGE of Mandaue 2024</small>
      </div>
    </div>
  </footer>

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fa fa-angle-up"></i>
  </a>

  <?php include 'modal/logout_modal.php'; ?>
  <?php include 'modal/form_modal.php'; ?>
  <?php include 'modal/deletion_modal.php'; ?>


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

  $('#content').load('dashboard/dashboard.php');

  $(".navbar-sidenav a").on("click", function() {
    $(".navbar-sidenav").find(".active").removeClass("active");
    $(this).parent().addClass("active");
  });

  $('.dropdown-menu').on('click', function(event) {
      event.stopPropagation();
  });
  
  function handleChange(input) {
    if (input.value < 1 && input.value != "") input.value = 1;
  }

  function isNumberKey(evt)
  {
     var charCode = (evt.which) ? evt.which : event.keyCode
     if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;

     return true;
  }
    
  $(document).ready(function(){

    setInterval(function() {
      $('#OnlineJud').load('dashboard/online_judge.php');
    }, 2000);

  });
  
</script>

</body>

</html>
