<?php

require('inc/pdo.php');
require('inc/function.php');

// TABLE users
// id,pseudo (60),email(120),password(255),created_at(datetime), token(255), role (admin, abonne)  varchar (10)
$errors = array();
if(!empty($_POST['submitinscription'])) {
  // Faille xss
  $surname    = cleanXss($_POST['surname']);
  $name    = cleanXss($_POST['name']);
  $email     = cleanXss($_POST['email']);
  $password1 = cleanXss($_POST['password1']);
  $password2 = cleanXss($_POST['password2']);
  // validation pseudo (3, 50, unique)
  $errors = ValidationText($errors,$surname,'surname',2,50);
  $errors = ValidationText($errors,$name,'name',2,50);

  // validation email (email valide, unique)
  if(!empty($email)) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors['email'] =  'Veuillez renseigner un email valide';
    } else {
      $sql = "SELECT id FROM users WHERE email = :email";
      $query = $pdo->prepare($sql);
      $query->bindValue(':email',$email,PDO::PARAM_STR);
      $query->execute();
      $verifEmail = $query->fetch();
      if(!empty($verifEmail)) {
        $errors['email'] = 'Cet email existe déjà';
      }
    }
  } else {
    $errors['email'] = 'Veuillez renseigner un email';
  }
  // password (min 6 , identiques)
  if(!empty($password1) && !empty($password2)) {
    if($password1 != $password2) {
      $errors['password'] = 'Veuillez renseigner des mot de passe identiques';
    } elseif(mb_strlen($password1) < 6) {
      $errors['password'] = 'Min 6 caractères';
    }
  } else {
    $errors['password'] = 'Veuillez renseigner vos mots de passe';
  }

  // if no error
  if(count($errors) == 0) {
    // hash password
    $hashPassword = password_hash($password1,PASSWORD_DEFAULT);
    $token = generateRandomString(255);
    // generate token
    // INSERT INTO
    $sql = "INSERT INTO users (surname,name,email,password,created_at,token,role,status)
                        VALUES(:surname,:name,:email,:password,NOW(),:token,'user','active')";
      $query = $pdo->prepare($sql);
      $query->bindValue(':surname',$surname,PDO::PARAM_STR);
      $query->bindValue(':name',$name,PDO::PARAM_STR);
      $query->bindValue(':email',$email,PDO::PARAM_STR);
      $query->bindValue(':password',$password1,PDO::PARAM_STR);
      $query->bindValue(':token',$token,PDO::PARAM_STR);
      $query->execute();
  }
}






require('inc/header-front.php');







?>
<h1>Inscription </h1>
<!-- Inscription par julien -->
<form method="POST" action=""  novalidate>

    <!-- SURNAME -->
      <label for="surname">Prénom*</label>
      <span class="error"><?php if(!empty($errors['surname'])) { echo $errors['surname']; } ?></span>
      <input type="text" name="surname"  class="form-control" value="<?php if(!empty($_POST['surname'])) { echo $_POST['surname']; } ?>" />
    <!-- NAME -->
      <label for="name">Nom*</label>
      <span class="error"><?php if(!empty($errors['name'])) { echo $errors['name']; } ?></span>
      <input type="text" name="name"  class="form-control" value="<?php if(!empty($_POST['name'])) { echo $_POST['name']; } ?>" />
    <!-- EMAIL -->
      <label for="email">E-mail*</label>
      <span class="error"><?php if(!empty($errors['email'])) { echo $errors['email']; } ?></span>
      <input type="email" name="email"  class="form-control" value="<?php if(!empty($_POST['email'])) { echo $_POST['email']; } ?>" />
    <!-- PASSWORD1 -->
      <label for="password1">Mot de passe*</label>
      <span class="error"><?php if(!empty($errors['password'])) { echo $errors['password']; } ?></span>
      <input type="password" name="password1"  class="form-control" value="" />
    <!-- PASSWORD2 -->
      <label for="password2">Confirmation mot de passe*</label>
      <input type="password" name="password2"  class="form-control" value="" />

    <input type="submit" name="submitinscription" value="Je m'inscris" />
</form>





<?php
require('inc/footer-front.php');
