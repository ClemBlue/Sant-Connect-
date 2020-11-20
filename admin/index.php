<?php
session_start();
require('../inc/pdo.php');
require('../inc/function.php');
$errors = array();
if(!empty($_SESSION['user']['role']) && $_SESSION['user']['role'] == 'admin') {
  // verifier que le role existe est bien admin dans bdd
  $user_id = $_SESSION['user']['id'];
  $sql = "SELECT * FROM vaccins";
  $query = $pdo->prepare($sql);
  $query->execute();
  $vaccins = $query->fetchAll();

  $vaccin_id = $_GET['id'];
  $vaccin_tag = $_GET['tag'];

  //select id vaccins
  $sql = "SELECT * FROM vaccins WHERE id =:id_vaccins ";
  $query = $pdo->prepare($sql);
  $query->bindValue(':id_vaccins',$vaccin_id,PDO::PARAM_INT);
  $query->execute();
  $vaccin_placeholder = $query->fetch();

  // Update Status desactivé
  if (!empty($_GET) && $_GET['tag'] == 'sup') {

    $sql = "UPDATE vaccins
            SET status = 'desactive', updated_at = NOW()
            WHERE id = :vaccin_tag";
    $query = $pdo->prepare($sql);
    $query->bindValue(':vaccin_tag',$vaccin_id,PDO::PARAM_INT);
    $query->execute();
  }
  // Update Status actif
  if (!empty($_GET) && $_GET['tag'] == 'add') {
    $sql = "UPDATE vaccins
            SET status = 'actif', updated_at = NOW()
            WHERE id = :vaccin_add";
    $query = $pdo->prepare($sql);
    $query->bindValue(':vaccin_add',$vaccin_id,PDO::PARAM_INT);
    $query->execute();

  }

  if(!empty($vaccins)) {
    if(!empty($_POST['submited-edit'])) { // formulaire soumis
      // FAille XSS
      $name   = trim(strip_tags($_POST['name']));
      $description = trim(strip_tags($_POST['description']));
      // Validation
      $errors = ValidationText($errors,$name,'name',3,50);
      $errors = ValidationText($errors,$description,'description',10,2000);
      //UPdate un vaccin
      if(count($errors) == 0) {
        $sql = "UPDATE vaccins
                SET name = :name,description=:description, updated_at = NOW()
                WHERE id = :id_vaccins";
        $query = $pdo->prepare($sql);
        $query->bindValue(':name',$name,PDO::PARAM_STR);
        $query->bindValue(':description',$description,PDO::PARAM_STR);
        $query->bindValue(':id_vaccins',$vaccin_id,PDO::PARAM_INT);
        $query->execute();
      }
    }
  } else {
    // header('Location: 404.php');
    die('12');
  }
} else {
  header('Location: ../index.php');
  die();
}
// Traitement du formulaire
$errors = array();
$success = false;
// est-ce que le formulaire est soumis ?
if(!empty($_POST['submited_add'])) {
  // Faille XSS

  $title  = cleanXss($_POST['title']);
  $content   = cleanXss($_POST['content']);

  $errors = ValidationText($errors,$title,'title',3,50);
  $errors = ValidationText($errors,$content,'content',15,500);


  if(count($errors) == 0) {
    $success = true;
    $sql = "INSERT INTO vaccins (name,description,delay,created_at,updated_at,status)
            VALUES (:name,:description,NOW(),NOW(),NOW(),'actif')";
    $query = $pdo->prepare($sql);
    $query->bindValue(':name',$title,PDO::PARAM_STR);
    $query->bindValue(':description',$content,PDO::PARAM_STR);
    $query->execute();

    // header('Location: index.php?id=1&&tag=none');
    // exit();

  }

}
// STATS Vaccins

// Vaccins actif
if (!empty($vaccins)) {
  $sql = "SELECT COUNT(*) FROM vaccins WHERE status = 'actif'";
  $query = $pdo->prepare($sql);
  $query->execute();
  $nb_vaccins_actif = $query->fetch();
}
// Vaccins Desactive
if (!empty($vaccins)) {
  $sql = "SELECT COUNT(*) FROM vaccins WHERE status = 'desactive'";
  $query = $pdo->prepare($sql);
  $query->execute();
  $nb_vaccins_desactive = $query->fetch();
}

$nb_total_vaccins = $nb_vaccins_actif['COUNT(*)'] + $nb_vaccins_desactive['COUNT(*)'];

$poucentage_active = ($nb_vaccins_actif['COUNT(*)'] / $nb_total_vaccins) * 100;

// Stats Users
$sql = "SELECT * FROM users";
$query = $pdo->prepare($sql);
$query->execute();
$users = $query->fetchAll();
// Nombre d'utilisateur
  if (!empty($users)) {
    $sql = "SELECT COUNT(*) FROM users";
    $query = $pdo->prepare($sql);
    $query->execute();
    $nb_users = $query->fetch();
  }
// Nombre d'utilisateur qui ont prit un vaccins
$sql = "SELECT COUNT(id_user) FROM user_vaccin GROUP BY id_vaccin";
$query = $pdo->prepare($sql);
$query->execute();
$nb_users_vaccins = $query->fetch();
// Nombre de vaccins pris par tous les utilisateur
$sql = "SELECT COUNT(id_user) FROM user_vaccin";
$query = $pdo->prepare($sql);
$query->execute();
$nb_allvaccins_for_users = $query->fetch();
if ($nb_users_vaccins==0){
    $nb_users_vaccins['COUNT(id_user)']=1;
    $moyenne_devaccins_paruser = $nb_allvaccins_for_users['COUNT(id_user)']/$nb_users_vaccins['COUNT(id_user)'];
}else{
    $moyenne_devaccins_paruser = $nb_allvaccins_for_users['COUNT(id_user)']/$nb_users_vaccins['COUNT(id_user)'];
}


require('inc/header-back.php');
require('inc/navbar.php');


?>
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                                aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo ucfirst(strtolower($_SESSION['user']['nom'])) . ' ' . ucfirst(strtolower($_SESSION['user']['prenom'])); ?></span>
                                <img class="img-profile rounded-circle"
                                    src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="../profil.php">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="../index.php" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Users(Nombre Total)</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $nb_users['COUNT(*)']; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Vaccins(Nombre Total) </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $nb_vaccins_desactive['COUNT(*)'] + $nb_vaccins_actif['COUNT(*)']; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-tint fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Vaccins disponibles
                                            </div>
                                            <div class="row no-gutters align-items-center">
                                                <div class="col-auto">
                                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $poucentage_active . "%"; ?></div>
                                                </div>
                                                <div class="col">
                                                    <div class="progress progress-sm mr-2">
                                                        <div class="progress-bar bg-info" role="progressbar"
                                                            style="width:<?php echo $poucentage_active.'%'; ?>" aria-valuenow="50" aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Requests Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Nombre moyen de vaccins par utilisateur</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo floor($moyenne_devaccins_paruser); ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-medkit fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Begin Page Content -->
                    <div class="container-fluid">

                        <!-- Page Heading -->
                        <h1 class="h3 mb-2 text-gray-800">Tables</h1>


                        <!-- DataTales Example -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">BDD Vaccins</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Description</th>
                                                <th>Created_at</th>
                                                <th>Delay</th>
                                                <th>Update_at</th>
                                                <th>Status</th>
                                                <th>fonction</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                              <th>Name</th>
                                              <th>Description</th>
                                              <th>Created_at</th>
                                              <th>Delay</th>
                                              <th>Update_at</th>
                                              <th>Status</th>
                                              <th>fonction</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                          <?php foreach ($vaccins as $vaccin) {?>
                                                  <tr>
                                                    <td><?php echo $vaccin['name'];?></td>
                                                    <td><?php echo $vaccin['description'];?></td>
                                                    <td><?php echo $vaccin['created_at'];?></td>
                                                    <td><?php echo date('H:i',$vaccin['delay']);?></td>
                                                    <td><?php echo $vaccin['updated_at'];?></td>
                                                    <td><?php echo $vaccin['status'];?></td><?php
                                                    if (!empty($vaccin) && $vaccin['status'] == 'actif') {?>
                                                      <td><?php echo '<a href="index.php?id='.$vaccin['id'].'&&tag=none">Editer</a>';
                                                                echo '<a href="index.php?id='.$vaccin['id'].'&&tag=sup">Désactiver</a>';?> </td><?php
                                                    }elseif (!empty($vaccin) && $vaccin['status'] == 'desactive') {?>
                                                      <td><?php echo '<a href="index.php?id='.$vaccin['id'].'&&tag=none">Editer</a>';
                                                                echo '<a href="index.php?id='.$vaccin['id'].'&&tag=add">Activer</a>';?></td><?php
                                                      }

                                                      ?>
                                                  </tr><?php
                                                }?>


                                        </tbody>
                                    </table>

                                    </div>
                                    <!-- Form -->
                                    <div class="form-edit-add">
                                      <!-- Edit Vaccins -->
                                      <form class="form-edit" action="" method="post">
                                        <!-- name -->
                                          <label for="name">Modification d'un vaccin</label>
                                          <label for="name">Name</label>
                                          <span class="errorse"><?php if(!empty($errors['name'])){echo $errors['name'];} ?></span>
                                          <input id="name-edit" type="text" name="name-edit" value="<?php if(!empty($_POST['name'])){echo $_POST['name'];} else { echo $vaccin_placeholder['name']; } ?>">

                                        <!-- description -->
                                          <label for="description">Content</label>
                                          <span class="errorse"><?php if(!empty($errors['description'])){echo $errors['description'];} ?></span>
                                          <textarea id="description" rows="8" cols="80" name="description"><?php if(!empty($_POST['description'])){echo $_POST['description'];} else {echo $vaccin_placeholder['description'];} ?></textarea>

                                        <!-- SUBMIT -->
                                          <input type="submit" name="submited_edit" value="Modifier">

                                      </form>
                                      <!-- ADD Vaccins -->
                                      <form class="form-add" action="" method="post" >
                                        <label for="title">Ajout d'un vaccin</label>
                                        <div class="errorses">
                                          <label for="title">Name</label>
                                          <span class="errorse"><?php if(!empty($errors['title'])) { echo $errors['title']; } ?></span>
                                        </div>
                                        <input type="text" id="title" name="title" value="<?php if(!empty($_POST['title'])) { echo $_POST['title']; } ?>">
                                        <div class="errorses">
                                          <label for="content">Content</label>
                                          <span class="errorse"><?php if(!empty($errors['content'])) { echo $errors['content']; } ?></span>
                                        </div>
                                        <textarea name="content" rows="8" cols="80"><?php if(!empty($_POST['content'])) { echo $_POST['content']; } ?></textarea>

                                        <input type="submit" name="submited_add" value="Envoyer">
                                      </form>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- /.container-fluid -->

                </div>
                <!-- End of Main Content -->

                </div>
                <!-- /.container-fluid -->
                <?php require('inc/footer-back.php');?>
            </div>
            <!-- End of Main Content -->


    </div>
    <!-- End of Page Wrapper -->
    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>
<?php
