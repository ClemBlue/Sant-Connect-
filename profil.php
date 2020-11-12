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
if (!empty($vaccinsUser)) {
  foreach ($vaccinsUser as $vaccinUser) {
    $n++;
    $idVaccin = $vaccinUser['id_vaccin'];
    $sql = "SELECT * FROM vaccins WHERE id = :id";
    $query = $pdo->prepare($sql);
    $query->bindValue(':id',$idVaccin,PDO::PARAM_INT);
    $query->execute();
    $vaccinsOfUser[$n] = $query->fetch();
  }
}

// Sélection des autres vaccins, non liés au user //
$m = -1;
foreach ($vaccinsUser as $vaccinUser) {
  $m++;
  $idVaccin = $vaccinUser['id_vaccin'];
  $sql = "SELECT * FROM vaccins WHERE id != :id";
  $query = $pdo->prepare($sql);
  $query->bindValue(':id',$idVaccin,PDO::PARAM_INT);
  $query->execute();
  $notChooseVaccins[$m] = $query->fetch();
}

require('inc/header-front.php');
?>

<p>Bienvenu <?php echo $nom . ' ' . $prenom; ?><p>

<?php
if (!empty($vaccinUser)) {
  foreach ($vaccinsOfUser as $vaccinOfUser) {
  echo $vaccinOfUser['name'];
  echo '</br>';
  echo $vaccinOfUser['description'];
  echo '</br>';
  echo '<a href="vaccins.php?id=<?' . $vaccinOfUser['id'] . '&&type=supp">Supprimer de mes vaccins</a>';
  echo '</br>';
  }
}else {
echo '<p>Vous n\'avez ajouté aucun vaccin</p>';
}

foreach ($notChooseVaccins as $notChooseVaccin) {
  echo $notChooseVaccin['name'];
  echo '</br>';
  echo $notChooseVaccin['description'];
  echo '</br>';
  echo '<a href="vaccins.php?id=<?' . $notChooseVaccin['id'] . '&&type=add">Ahhouter à mes vaccins</a>';
  echo '</br>';
}

/*
?>

<?php foreach ($vaccinsAll as $vaccin): ?>
  <p><?php echo $vaccin['name']; ?></p>
  <p><?php echo $vaccin['description']; ?></p>
  <a href="vaccins.php?id=<?php echo $vaccin['id']; ?>&&type=add">Ajouter à mes vaccins</a>
  </br>
<?php endforeach; */?>

<a href="logout.php">Déconexion</a>

<?php
require('inc/footer-front.php');
