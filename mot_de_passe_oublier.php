<?php

session_start();
require('inc/pdo.php');
require('inc/function.php');

//formulaire email, verif email , envoyer if : token , user , email ok ,
//fonction date token : datetime - deltatime =delai limite d'existance de date token,

$token = '';

if(!empty($_POST['submited'])) {
  // Faille xss.

  $email = cleanXss($_POST['email']);

  // validation txt

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
        //email exist
        $id = $verifEmail['id'];
        $sql = "SELECT token FROM users WHERE id = :id";
        $query = $pdo->prepare($sql);
        $query->bindValue(':id',$id,PDO::PARAM_INT);
        $query->execute();
        $token = $query->fetch();

        $sql = "UPDATE users SET datetoken = NOW() WHERE id =$id";
          $query = $pdo->prepare($sql);
          $query->execute();
      }
    }
  } else {
    $errors['email'] = 'Veuillez renseigner un email';
  }

}


require('inc/header-front.php');?>

<form class="" action="" method="post" novalidate>
<!-- EMAIL -->
  <label for="email">E-mail*</label>
  <span class="error"><?php if(!empty($errors['email'])) { echo $errors['email']; } ?></span>
  <input type="email" name="email"  class="form-control" value="<?php if(!empty($_POST['email'])) { echo $_POST['email']; } ?>" />


  <input type="submit" name="submited" value="Valider" />
  <?php if (!empty($verifEmail)): ?>
    <?php echo '<a href="mdp_modif.php?id='.$token['token'].'">Modifier le mot de passe </a>';
    echo ' Le lien est normalement envoyer par email.';

     ?>
  <?php endif; ?>
</form>
<?php
require('inc/footer-front.php');
