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
  $vaccin_sup = $_GET['tag'];

  //select id vaccins
  $sql = "SELECT * FROM vaccins WHERE id =:id_vaccins ";
  $query = $pdo->prepare($sql);
  $query->bindValue(':id_vaccins',$vaccin_id,PDO::PARAM_INT);
  $query->execute();
  $vaccin_ = $query->fetchAll();

  // Update Status
  if (!empty($_GET) && $_GET['tag'] == $vaccin_[0]['id']) {

    $sql = "UPDATE vaccins
            SET status = 'desactive', updated_at = NOW()
            WHERE id = :vaccin_sup";
    $query = $pdo->prepare($sql);
    $query->bindValue(':vaccin_sup',$vaccin_sup,PDO::PARAM_INT);
    $query->execute();
  }

  if(!empty($vaccins)) { // article existe
    if(!empty($_POST['submited'])) { // formulaire soumis
      // FAille XSS
      $name   = trim(strip_tags($_POST['name']));
      $description = trim(strip_tags($_POST['description']));
      // Validation
      $errors = ValidationText($errors,$name,'name',3,50);
      $errors = ValidationText($errors,$description,'description',10,2000);

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


require('inc/header-back.php');?>
<a href="add_vaccin.php">Ajouter un vaccin</a>
<?php


  foreach ($vaccins as $vaccin) {

    if (!empty($vaccin) && $vaccin['status'] == 'actif') {
      echo '<p>Name: '.$vaccin['name'].'</p>';
      echo '<p>Description: '.$vaccin['description'].'</p>';
      echo '<p>Status: '.$vaccin['status'].'</p>';
      echo '<a href="index.php?id='.$vaccin['id'].'&&tag=none">Editer</a>';
      echo '<a href="index.php?id='.$vaccin['id'].'&&tag='.$vaccin['id'].'">Supprimer</a>';
  }
}
?> <div class="Limite">
  <p>--------------------------------</p>
</div> <?php

  foreach ($vaccins as $vaccin) {
    if (!empty($vaccin) && $vaccin['status'] == 'desactive') {
      echo '<p>Name: '.$vaccin['name'].'</p>';
      echo '<p>Description: '.$vaccin['description'].'</p>';
      echo '<p>Status: '.$vaccin['status'].'</p>';
      echo '<a href="index.php?id='.$vaccin['id'].'&&tag=none">Editer</a>';
      echo '<a href="index.php?id='.$vaccin['id'].'&&tag=none">Ajouter</a>';
  }
}
?>

<form class="" action="" method="post">
  <!-- name -->
  <div class="">
    <label for="name">Name</label>
    <input id="name" type="text" name="name" value="<?php if(!empty($_POST['name'])){echo $_POST['name'];} else { echo $vaccin_[0]['name']; } ?>">
    <span class="error"><?php if(!empty($errors['name'])){echo $errors['name'];} ?></span>
  </div>

  <!-- description -->
  <div class="">
    <label for="description">description</label>
    <textarea id="description" name="description"><?php if(!empty($_POST['description'])){echo $_POST['description'];} else {echo $vaccin_[0]['description'];} ?></textarea>
    <span class="error"><?php if(!empty($errors['description'])){echo $errors['description'];} ?></span>
  </div>
  <!-- SUBMIT -->
  <div class="">
    <input type="submit" name="submited" value="Modifier">
  </div>
</form>



<?php
require('inc/footer-back.php');
