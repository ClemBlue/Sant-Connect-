<?php
session_start();
include('../inc/pdo.php');
include('../inc/function.php');

// Traitement du formulaire
$errors = array();
$success = false;
// est-ce que le formulaire est soumis ?
if(!empty($_POST['submited'])) {
  // Faille XSS

  $name  = cleanXss($_POST['name']);
  $description   = cleanXss($_POST['description']);

  $errors = ValidationText($errors,$name,'name',3,50);
  $errors = ValidationText($errors,$description,'description',15,500);


  if(count($errors) == 0) {
    $success = true;
    $sql = "INSERT INTO vaccins (name,description,delay,created_at,status)
            VALUES (:name,:description,NOW(),NOW(),'actif')";
    $query = $pdo->prepare($sql);
    $query->bindValue(':name',$name,PDO::PARAM_STR);
    $query->bindValue(':description',$description,PDO::PARAM_STR);
    $query->execute();

    header('Location: index.php?id=1&&tag=none');
    exit();

  }

}

include('inc/header-back.php'); ?>
<a href="index.php?id=1&&tag=none">Acceuil back</a>
<h1>Ajout d'un vaccin</h1>

<form action="" method="post" >


  <label for="name">Titre</label>
  <input type="text" id="name" name="name" value="<?php if(!empty($_POST['name'])) { echo $_POST['name']; } ?>">
  <span class="error"><?php if(!empty($errors['name'])) { echo $errors['name']; } ?></span>

  <label for="cotent">Contenu</label>
  <textarea name="description" rows="8" cols="80"><?php if(!empty($_POST['description'])) { echo $_POST['description']; } ?></textarea>
  <span class="error"><?php if(!empty($errors['description'])) { echo $errors['description']; } ?></span>

  <input type="submit" name="submited" value="Envoyer">
</form>

<?php include('inc/footer-back.php');
