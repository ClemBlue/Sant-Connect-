<?php

session_start();
require('inc/pdo.php');
require('inc/function.php');

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
                        VALUES(:surname,:name,:email1,:password,NOW(),:token,'user','active')";
      $query = $pdo->prepare($sql);
      $query->bindValue(':surname',$surname,PDO::PARAM_STR);
      $query->bindValue(':name',$name,PDO::PARAM_STR);
      $query->bindValue(':email1',$email1,PDO::PARAM_STR);
      $query->bindValue(':password',$hashPassword,PDO::PARAM_STR);
      $query->bindValue(':token',$token,PDO::PARAM_STR);
      $query->execute();
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

    if(!empty($user)){
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

      } else {
        $errors['email'] = 'Error credential';
      }

    } else {
      $errors['email'] = 'Error credential';
    }
  } else {
    $errors['email'] = 'Veuillez renseigner ce champ';
  }
}


require('inc/header-front.php');

?>

<!-- Ajout Connexion (Clément Blin) -->
<form action="" method="post">
  <!-- LOGIN -->
  <label for="email">E-mail</label>
  <span class="error"><?php if(!empty($errors['email'])) { echo $errors['email']; } ?></span>
  <input type="email" id="email" name="email" value="<?php if(!empty($_POST['email'])) { echo $_POST['email']; } ?>">

  <!-- PASSWORD -->
  <label for="password">Mot de passe</label>
  <input type="password" name="password" id="password" class="form-control" value="">

  <input type="submit" name="submitconnexion" value="Connexion">
</form>

<h1>Inscription </h1>
<!-- Inscription par julien -->
<form method="post" action=""  >

    <!-- SURNAME -->
      <label for="surname">Prénom*</label>
      <span class="error"><?php if(!empty($errorsIns['surname'])) { echo $errorsIns['surname']; } ?></span>
      <input type="text" name="surname"  class="form-control" value="<?php if(!empty($_POST['surname'])) { echo $_POST['surname']; } ?>" />
    <!-- NAME -->
      <label for="name">Nom*</label>
      <span class="error"><?php if(!empty($errorsIns['name'])) { echo $errorsIns['name']; } ?></span>
      <input type="text" name="name"  class="form-control" value="<?php if(!empty($_POST['name'])) { echo $_POST['name']; } ?>" />
    <!-- EMAIL -->
      <label for="email1">E-mail*</label>
      <span class="error"><?php if(!empty($errorsIns['email1'])) { echo $errorsIns['email1']; } ?></span>
      <input type="email" name="email1"  class="form-control" value="<?php if(!empty($_POST['email1'])) { echo $_POST['email1']; } ?>" />
    <!-- PASSWORD1 -->
      <label for="password1">Mot de passe*</label>
      <span class="error"><?php if(!empty($errorsIns['password'])) { echo $errorsIns['password']; } ?></span>
      <input type="password" name="password1"  class="form-control" value="" />
    <!-- PASSWORD2 -->
      <label for="password2">Confirmation mot de passe*</label>
      <input type="password" name="password2"  class="form-control" value="" />

    <input type="submit" name="submitinscription" value="Je m'inscris" />
</form>
<?php

require('inc/footer-front.php');
