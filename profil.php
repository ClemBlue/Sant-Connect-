<?php

require('inc/pdo.php');
require('inc/function.php');

session_start();

$nom = $_SESSION['user']['nom'];
$prenom = $_SESSION['user']['prenom'];
$email = $_SESSION['user']['email'];
$status = 'actif';

$sql = "SELECT * FROM vaccins WHERE status = :status";
$query = $pdo->prepare($sql);
$query->bindValue(':status', $status,PDO::PARAM_STR);
$query->execute();
$vaccinsAll = $query->fetchAll();

require('inc/header-front.php');
?>

<p>Bienvenu <?php echo $nom . ' ' . $prenom; ?><p>

<?php foreach ($vaccinsAll as $vaccin): ?>
  <p><?php echo $vaccin['name']; ?></p>
  <p><?php echo $vaccin['description']; ?></p>
<?php endforeach; ?>

<a href="logout.php">Déconexion</a>

<?php
require('inc/footer-front.php');
