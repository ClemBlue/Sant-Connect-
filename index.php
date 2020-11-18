<?php

session_start();
require('inc/pdo.php');
require('inc/function.php');

$success = false;
// Inscription //
$errorsIns = array();
if(!empty($_POST['submitinscription'])) {
  // Faille xss.
  $surname    = cleanXss($_POST['surname']);
  $name    = cleanXss($_POST['name']);
  $email1     = cleanXss($_POST['email1']);
  $password1 = cleanXss($_POST['password1']);
  $password2 = cleanXss($_POST['password2']);
  // validation pseudo (3, 50, unique)
  $errorsIns = ValidationText($errorsIns,$surname,'surname',2,50);
  $errorsIns = ValidationText($errorsIns,$name,'name',2,50);

  // validation email (email valide, unique)
  if(!empty($email1)) {
    if (!filter_var($email1, FILTER_VALIDATE_EMAIL)) {
      $errorsIns['email1'] =  'Veuillez renseigner un email valide';
    } else {
      $sql = "SELECT id FROM users WHERE email = :email1";
      $query = $pdo->prepare($sql);
      $query->bindValue(':email1',$email1,PDO::PARAM_STR);
      $query->execute();
      $verifEmail = $query->fetch();
      if(!empty($verifEmail)) {
        $errorsIns['email1'] = 'Cet email existe déjà';
      }
    }
  } else {
    $errorsIns['email1'] = 'Veuillez renseigner un email';
  }
  // password (min 6 , identiques)
  if(!empty($password1) && !empty($password2)) {
    if($password1 != $password2) {
      $errorsIns['password'] = 'Veuillez renseigner des mot de passe identiques';
    } elseif(mb_strlen($password1) < 6) {
      $errorsIns['password'] = 'Min 6 caractères';
    }
  } else {
    $errorsIns['password'] = 'Veuillez renseigner vos mots de passe';
  }


  // if no error
  if(count($errorsIns) == 0) {
    // hash password
    $hashPassword = password_hash($password1,PASSWORD_DEFAULT);
    $token = generateRandomString(255);
    // generate token
    // INSERT INTO
    $sql = "INSERT INTO users (surname,name,email,password,created_at,token,role,status)
                        VALUES(:surname,:name,:email1,:password,NOW(),:token,'user','actif')";
      $query = $pdo->prepare($sql);
      $query->bindValue(':surname',$surname,PDO::PARAM_STR);
      $query->bindValue(':name',$name,PDO::PARAM_STR);
      $query->bindValue(':email1',$email1,PDO::PARAM_STR);
      $query->bindValue(':password',$hashPassword,PDO::PARAM_STR);
      $query->bindValue(':token',$token,PDO::PARAM_STR);
      $query->execute();
      $success = true;
  }
}


// Connexion //
$errors = array();

if(!empty($_POST['submitconnexion'])) {
  $email = cleanXss($_POST['email']);
  $password = cleanXss($_POST['password']);

  if (!empty($email) && !empty($password)) {
    $sql = "SELECT * FROM users WHERE email = :email";
    $query = $pdo->prepare($sql);
    $query->bindValue(':email', $email,PDO::PARAM_STR);
    $query->execute();
    $user =$query->fetch();

    if(!empty($user) && $user['status'] == 'actif'){
      $hashpassword = $user['password'];
      if (password_verify($password, $user['password'])) {
        $_SESSION['user'] = array(
          'id' => $user['id'],
          'email' => $user['email'],
          'nom' => $user['name'],
          'prenom' => $user['surname'],
          'role' => $user['role'],
          'ip' => $_SERVER['REMOTE_ADDR']
        );
        // header('Location: profil.php');
        // exit();

      } else {
        $errors['email'] = 'Error credential';
      }

    } else {
      $errors['email'] = 'Mauvais utilisateur ou compte désactivé par un admin.';
    }
  } else {
    $errors['email'] = 'Veuillez renseigner ce champ';
  }
}
//vérification role admin
$user_role= 'none';
$role= 'none';
if (!empty($_SESSION)) {
  $sql = "SELECT role FROM users WHERE id = :id";
  $query = $pdo->prepare($sql);
  $query->bindValue(':id',$_SESSION['user']['id'],PDO::PARAM_INT);
  $query->execute();
  $user_role = $query->fetch();
  $role = $user_role['role'];
}

require('inc/header-front.php');

?>


<!-- Ajout Connexion (Clément Blin) -->
<div class="wrapacceuil">
 <div class="details">
  <p>HealthBook est un carnet de vaccination électronique personnalisé qui vous permet de faire <br>un suivi de vos vaccinations obligatoires en toute simplicité et d'être alerté en temps réel lorsque<br> vous devez faire un rappel.<br><br>Pour cela, rien de plus simple: il vous suffit de créer votre compte ou de vous y connecter en <br> remplissant les formulaires ci-contre et de renseigner les dates de vos dernières injections.<br>Ensuite, HealthBook s'occupe du reste!</p>
 </div>
  <div class="form">
   <div class="formconnexion">
     <div class="formtitle">
      <h2>Connexion</h2>
     </div>
<form action="" method="post">
<!-- LOGIN -->
  <div class="loginputs">
    <span class="error"><?php if(!empty($errors['email'])) { echo $errors['email']; } ?></span>
    <input type="email" id="email" name="email" value="<?php if(!empty($_POST['email'])) { echo $_POST['email']; } ?>" placeholder="E-mail">
  </div>
  <!-- PASSWORD -->
  <div class="loginputs">
    <input type="password" name="password" id="password" class="form-control" value="" placeholder="Mot de passe">
  </div>
  <div class="formbtn">
    <input type="submit" name="submitconnexion" value="Valider">
    <a href="mot_de_passe_oublier.php">Mot de passe oublié</a>
  </div>
</form>
<?php if ($role == 'admin'): ?>
  <a href="admin/index.php?id=1&&tag">Back</a>
<?php endif; ?>
</div>

<!-- Inscription par julien -->
<div class="forminscription">
  <div class="formtitle">
    <h2>Inscription</h2>
  </div>
<form method="post" action=""  >

<!-- SURNAME -->
    <div class="signupinputs">
      <span class="error"><?php if(!empty($errorsIns['surname'])) { echo $errorsIns['surname']; } ?></span>
      <input id="prénom" type="text" name="surname"  class="form-control" value="<?php if(!empty($_POST['surname'])) { echo $_POST['surname']; } ?>" placeholder="Prénom" />
    </div>
      <!-- NAME -->
    <div class="signupinputs">
      <span class="error"><?php if(!empty($errorsIns['name'])) { echo $errorsIns['name']; } ?></span>
      <input type="text" name="name"  class="form-control" value="<?php if(!empty($_POST['name'])) { echo $_POST['name']; } ?>" placeholder="Nom" />
    </div>
      <!-- EMAIL -->
    <div class="signupinputs">
      <span class="error"><?php if(!empty($errorsIns['email1'])) { echo $errorsIns['email1']; } ?></span>
      <input type="email" name="email1"  class="form-control" value="<?php if(!empty($_POST['email1'])) { echo $_POST['email1']; } ?>" placeholder="E-mail" />
    </div>
      <!-- PASSWORD1 -->
    <div class="signupinputs">
      <span class="error"><?php if(!empty($errorsIns['password'])) { echo $errorsIns['password']; } ?></span>
      <input type="password" name="password1"  class="form-control" value="" placeholder="Mot de passe" />
    </div>
      <!-- PASSWORD2 -->
    <div class="signupinputs">
      <input type="password" name="password2"  class="form-control" value="" placeholder="Confirmation mot de passe" />
    </div>
    <div class="formbtn">
    <input id="boutonsignup" type="submit" name="submitinscription" value="Valider" />
    </div>
   </form>
  </div>
 </div>
</div>

<?php if ($success == true): ?>
  <p>Inscription réussie, veuillez vous connecter</p>
<?php endif; ?>


</div>

<?php
require('inc/footer-front.php');
