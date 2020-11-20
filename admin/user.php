<?php
session_start();
require('../inc/pdo.php');
require('../inc/function.php');
$errors = array();
  // verifier que le role existe est bien admin dans bdd
if(!empty($_SESSION['user']['role']) && $_SESSION['user']['role'] == 'admin') {
  $sql = "SELECT * FROM users";
  $query = $pdo->prepare($sql);
  $query->execute();
  $users = $query->fetchAll();
  $user_id = $_GET['id'];
  $user_tag = $_GET['tag'];

  //select id user
  $sql = "SELECT * FROM users WHERE id =:user_id ";
  $query = $pdo->prepare($sql);
  $query->bindValue(':user_id',$user_id,PDO::PARAM_INT);
  $query->execute();
  $user_placeholder = $query->fetch();

  // Update Status desactivé
  if (!empty($_GET) && $_GET['tag'] == 'sup') {

    $sql = "UPDATE users
            SET status = 'desactive'
            WHERE id = :user_tag";
    $query = $pdo->prepare($sql);
    $query->bindValue(':user_tag',$user_id,PDO::PARAM_INT);
    $query->execute();
  }
  // Update Status actif
  if (!empty($_GET) && $_GET['tag'] == 'add') {

    $sql = "UPDATE users
            SET status = 'actif'
            WHERE id = :user_add";
    $query = $pdo->prepare($sql);
    $query->bindValue(':user_add',$user_id,PDO::PARAM_INT);
    $query->execute();
  }

  if(!empty($users)) {
    if(!empty($_POST['submited'])) { // formulaire soumis
      // FAille XSS
      $name   = trim(strip_tags($_POST['name']));
      $description = trim(strip_tags($_POST['description']));
      // Validation
      $errors = ValidationText($errors,$name,'name',3,50);
      $errors = ValidationText($errors,$description,'description',10,2000);
      //UPdate un user
      if(count($errors) == 0) {
        $sql = "UPDATE users
                SET name = :name,surname=:surname
                WHERE id = :id_users";
        $query = $pdo->prepare($sql);
        $query->bindValue(':name',$name,PDO::PARAM_STR);
        $query->bindValue(':surname',$description,PDO::PARAM_STR);
        $query->bindValue(':id_users',$user_id,PDO::PARAM_INT);
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


                    <!-- Begin Page Content -->
                    <div class="container-fluid">

                        <!-- Page Heading -->
                        <h1 class="h3 mb-2 text-gray-800">Tables User</h1>

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
                                                <th>surname</th>
                                                <th>Email</th>
                                                <th>Created_at</th>
                                                <th>Role</th>
                                                <th>Status</th>
                                                <th>Gestion</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                              <th>Name</th>
                                              <th>surname</th>
                                              <th>Email</th>
                                              <th>Created_at</th>
                                              <th>Role</th>
                                              <th>Status</th>
                                              <th>Gestion</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                          <?php foreach ($users as $user) {?>
                                                  <tr>
                                                    <td><?php echo $user['name'];?></td>
                                                    <td><?php echo $user['surname'];?></td>
                                                    <td><?php echo $user['email'];?></td>
                                                    <td><?php echo $user['created_at'];?></td>
                                                    <td><?php echo $user['role'];?></td>
                                                    <td><?php echo $user['status'];?></td><?php
                                                    if (!empty($user) && $user['status'] == 'actif') {?>
                                                      <td><?php echo '<a href="user.php?id='.$user['id'].'&&tag=none">Editer</a>';
                                                                echo '<a href="user.php?id='.$user['id'].'&&tag=sup">Désactiver</a>';?> </td><?php
                                                    }elseif (!empty($user) && $user['status'] == 'desactive') {?>
                                                      <td><?php echo '<a href="user.php?id='.$user['id'].'&&tag=none">Editer</a>';
                                                                echo '<a href="user.php?id='.$user['id'].'&&tag=add">Activer</a>';?></td><?php
                                                      }

                                                      ?>
                                                  </tr><?php
                                                }?>


                                        </tbody>
                                    </table>

                                </div>
                                <!-- Edit Vaccins -->
                                <div class="form-users">

                                  <form class="form-user" action="" method="post">
                                    <!-- name -->
                                      <label for="">Modifier un utilisateur</label>
                                      <label for="name">Name</label>
                                      <span class="errorse"><?php if(!empty($errors['name'])){echo $errors['name'];} ?></span>
                                      <input id="name-edit" type="text" name="name-edit" value="<?php if(!empty($_POST['name'])){echo $_POST['name'];} else { echo $user_placeholder['name']; } ?>">

                                    <!-- description -->
                                      <label for="surname-edit">Surname</label>
                                      <span class="errorse"><?php if(!empty($errors['description'])){echo $errors['description'];} ?></span>
                                      <input id="surname-edit" type="text" name="surname-edit" value="<?php if(!empty($_POST['description'])){echo $_POST['description'];} else { echo $user_placeholder['surname']; } ?>">

                                    <!-- SUBMIT -->
                                      <input type="submit" name="submited" value="Modifier">

                                  </form>
                            </div>
                        </div>

                    </div>
                    <!-- /.container-fluid -->

                </div>
                <!-- End of Main Content -->

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

        <?php require('inc/footer-back.php');?>
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
