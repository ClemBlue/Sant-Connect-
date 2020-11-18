<?php

session_start();
require('inc/pdo.php');
require('inc/function.php');

// Récupération des données du user depuis la session //
$nom = $_SESSION['user']['nom'];
$prenom = $_SESSION['user']['prenom'];
$email = $_SESSION['user']['email'];
$userID = $_SESSION['user']['id'];
$status = 'actif';

// Sélection des vaccins lié à l'utilisateur par son ID //
$sql = "SELECT * FROM user_vaccin WHERE id_user = :userID";
$query = $pdo->prepare($sql);
$query->bindValue(':userID', $userID,PDO::PARAM_INT);
$query->execute();
$vaccinsUser = $query->fetchAll();

// Selection des vaccins actif //
$sql = "SELECT * FROM vaccins WHERE status = :status";
$query = $pdo->prepare($sql);
$query->bindValue(':status', $status,PDO::PARAM_STR);
$query->execute();
$vaccinsAll = $query->fetchAll();

// Sélection des vaccins liés au user //
$n = -1;
$vaccinsOfUser = array();
if (!empty($vaccinsUser)) {
  foreach ($vaccinsUser as $vaccinUser) {
    $n++;
    $idVaccin = $vaccinUser['id_vaccin'];
    $sql = "SELECT * FROM vaccins WHERE id = :id AND status = :status";
    $query = $pdo->prepare($sql);
    $query->bindValue(':id',$idVaccin,PDO::PARAM_INT);
    $query->bindValue(':status', $status,PDO::PARAM_STR);
    $query->execute();
    $vaccinsOfUser[$n] = $query->fetch();
  }
}

// Sélection des autres vaccins, non liés au user //
if (!empty($vaccinsOfUser)) {
  foreach($vaccinsAll as $vaccinAllID => $vaccinAll) {
      foreach($vaccinsOfUser as $vaccinOfUser) {
       if($vaccinAll['id'] == $vaccinOfUser['id']) {
        unset($vaccinsAll[$vaccinAllID]);
        break;
       }
      }
  }
} else {

}

// Ajout date vaccin //
$errors = array();
$temps = time();
if(!empty($_POST['submitvaccin'])){
  // Faille xss
  $name = cleanXss($_POST['name']);
  $date_vaccin = cleanXss($_POST['date_vaccin']);
  $errors = ValidationText($errors,$name,'name',2,50);
  if(!empty($date_vaccin)){
    echo 'date fournis';
  } else {
    $errors['date_vaccin'] = 'Veuillez renseigner ce champ';
  }
  if (count($errors)==0) {
    $sql = "SELECT id FROM vaccins WHERE name = :name && status = :status";
    $query = $pdo->prepare($sql);
    $query->bindValue(':name',$name,PDO::PARAM_STR);
    $query->bindValue(':status', $status,PDO::PARAM_STR);
    $query->execute();
    $idOfUseresVaccin = $query->fetch();
    debug($idOfUseresVaccin);
    echo $date_vaccin;
    echo $userID;
    if (!empty($idOfUseresVaccin)) {
      $sql = "UPDATE user_vaccin SET date_vaccin = :date_vaccin WHERE id_vaccin = :id_vaccin && id_user = :userID";
      $query = $pdo->prepare($sql);
      $query->bindValue(':date_vaccin',$date_vaccin,PDO::PARAM_STR);
      $query->bindValue(':userID',$userID,PDO::PARAM_INT);
      $query->bindValue(':id_vaccin',$idOfUseresVaccin,PDO::PARAM_INT);
      $query->execute();
      $affected_rows = $query->rowCount();
      echo $affected_rows;
    } else {
      $errors['name'] = 'Mauvais nom de vaccin entré OU vaccin non disponible';
    }
  }
}

// Récup date des vaccins pour puis afficher temps restant //
require('inc/header-front.php');
?>

<p>Bienvenu <?php echo $nom . ' ' . $prenom; ?><p>

<?php
$idTemp = -1;
if (!empty($vaccinUser)) {
  foreach ($vaccinsOfUser as $vaccinOfUser) {
  $idTemp++;
  echo $idTemp;
  echo $vaccinOfUser['name'];
  echo '</br>';
  echo $vaccinOfUser['description'];
  echo '</br>';
  if(!empty($vaccinsUser[$idTemp]['date_vaccin'])){
    $tmpRest = $vaccinsUser[$idTemp]['date_vaccin'];
  } else {
    $tmpRest = 'Non renseigné';
  }
  echo $tmpRest;
  echo '</br>';
  echo '<a href="vaccins.php?id=' . $vaccinOfUser['id'] . '&&type=supp">Supprimer de mes vaccins</a>';
  echo '</br>';
  }
}else {
echo '<p>Vous n\'avez ajouté aucun vaccin</p>';
}
?>
<!-- Ajout date de vaccin -->
<form action="" method="post">
  <label for="name">Nom du Vaccin</label>
  <span class="error"><?php if(!empty($errors['name'])) { echo $errors['name']; } ?></span>
  <input type="text" name="name" value="<?php if(!empty($_POST['name'])) { echo $_POST['name']; } ?>">
  <label for="date_vaccin"></label>
    <span class="error"><?php if(!empty($errors['date_vaccin'])) { echo $errors['date_vaccin']; } ?></span>
  <input type="date" name="date_vaccin" value="">
  <input type="submit" name="submitvaccin" value="Valider">
</form>

<?php
if (!empty($vaccinsAll)) {
  foreach ($vaccinsAll as $vaccinAll) {
    if(!empty($vaccinAll)){
    echo $vaccinAll['name'];
    echo '</br>';
    echo $vaccinAll['description'];
    echo '</br>';
    echo '<a href="vaccins.php?id=' . $vaccinAll['id'] . '&&type=add">Ajouter à mes vaccins</a>';
    echo '</br>';
    }
  }
} else {
  echo '<p>Plus de vaccins à ajouter</p>';
}
?>

<a href="logout.php">Déconexion</a>

<?php
require('inc/footer-front.php');
