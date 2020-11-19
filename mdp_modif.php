<?php

session_start();
require('inc/pdo.php');
require('inc/function.php');
$errors = array();
// comparer les token en $_get pour acceder a la page

if(!empty($_GET['id'])) {

  $token = $_GET['id'];
  $sql = "SELECT token FROM users WHERE token = :token";
  $query = $pdo->prepare($sql);
  $query->bindValue(':token',$token,PDO::PARAM_STR);
  $query->execute();
  $users = $query->fetch();
  if(empty($users)) {
    die('404');
  }
} else {
  die('404');
}


if(!empty($_POST['submited'])) {
  // Faille xss.
  $password1 = cleanXss($_POST['password1']);
  $password2 = cleanXss($_POST['password2']);

  //Validation password
  if(!empty($password1) && !empty($password2)) {
    if($password1 != $password2) {
      $errors['password'] = 'Veuillez renseigner des mot de passe identiques';
    } elseif(mb_strlen($password1) < 6) {
      $errors['password'] = 'Min 6 caractères';
    }
  } else {
    $errors['password'] = 'Veuillez renseigner vos mots de passe';
  }


  if(count($errors) == 0) {
    // hash password
    $hashPassword = password_hash($password1,PASSWORD_DEFAULT);
    $newtoken = generateRandomString(255);
    // generate token
    // INSERT INTO
    $success = true;
    $sql ="UPDATE users SET password = :password, token = :newtoken  WHERE token = :token  ";
    $query = $pdo->prepare($sql);
    $query->bindValue(':password',$hashPassword,PDO::PARAM_STR);
    $query->bindValue(':newtoken',$newtoken,PDO::PARAM_STR);
    $query->bindValue(':token',$token,PDO::PARAM_STR);
    $query->execute();



    header('Location: index.php');
    exit();


  }

}
require('inc/header-front.php');?>

<form class="" action="" method="post">


  <!-- PASSWORD1 -->
    <label for="password1">Mot de passe*</label>
    <span class="error"><?php if(!empty($errors['password'])) { echo $errors['password']; } ?></span>
    <input type="password" name="password1"  class="form-control" value="" />
  <!-- PASSWORD2 -->
    <label for="password2">Confirmation mot de passe*</label>
    <input type="password" name="password2"  class="form-control" value="" />

    <input type="submit" name="submited" value="Valider">

</form>



<?php
require('inc/footer-front.php');
