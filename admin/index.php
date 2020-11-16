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
  $vaccin_ = $query->fetchAll();

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
    echo ' bv';
    $sql = "UPDATE vaccins
            SET status = 'actif', updated_at = NOW()
            WHERE id = :vaccin_add";
    $query = $pdo->prepare($sql);
    $query->bindValue(':vaccin_add',$vaccin_id,PDO::PARAM_INT);
    $query->execute();

  }

  if(!empty($vaccins)) {
    if(!empty($_POST['submited'])) { // formulaire soumis
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


require('inc/header-back.php');?>
<a href="add_vaccin.php">Ajouter un vaccin</a>
<?php

  // Afficher les
  foreach ($vaccins as $vaccin) {

    if (!empty($vaccin) && $vaccin['status'] == 'actif') {
      echo '<p>Name: '.$vaccin['name'].'</p>';
      echo '<p>Description: '.$vaccin['description'].'</p>';
      echo '<p>Status: '.$vaccin['status'].'</p>';
      echo '<a href="index.php?id='.$vaccin['id'].'&&tag=none">Editer</a>';
      echo '<a href="index.php?id='.$vaccin['id'].'&&tag=sup">Supprimer</a>';
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
      echo '<a href="index.php?id='.$vaccin['id'].'&&tag=add">Ajouter</a>';
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
