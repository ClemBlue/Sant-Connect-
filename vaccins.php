<?php
session_start();
require('inc/pdo.php');
require('inc/function.php');

if (!empty($_GET['id']) && is_numeric($_GET['id']) && !empty($_GET['type'])) {
  $id = $_GET['id'];
  $type = $_GET['type'];

  $sql = "SELECT * FROM vaccins WHERE id = :id";
  $query = $pdo->prepare($sql);
  $query->bindValue(':id',$id,PDO::PARAM_INT);
  $query->execute();
  $vaccin = $query->fetch();
  if (!empty($vaccin)) {
    if ($type == 'add') {
      $sql = 'INSERT INTO user_vaccin (id_vaccin, id_user) VALUES (:vaccin,:user)';
      $query = $pdo->prepare($sql);
      $query->bindValue(':vaccin',$id,PDO::PARAM_INT);
      $query->bindValue(':user',$_SESSION['user']['id'],PDO::PARAM_INT);
      $query->execute();
      header('Location: profil.php');
      exit();
    } else {
      die('404');
    }
  } else {
    die('404');
  }
} else {
  die('404');
}
