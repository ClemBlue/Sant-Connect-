<?php
function debug($tableau)
{
  echo '<pre>';
  print_r($tableau);
  echo '</pre>';
}

function cleanXss($value)
{
  return trim(strip_tags($value));
}

function ValidationText($errors,$data,$key,$min,$max)
{
  if(!empty($data)) {
    if(mb_strlen($data) < $min) {
      $errors[$key] = 'Min '.$min.' caractères';
    } elseif(mb_strlen($data) > $max) {
      $errors[$key] = 'Max '.$max.' caractères';
    }
  } else {
    $errors[$key] = 'Veuillez renseigner ce champ';
  }
  return $errors;
}
function dateFR($value){
  return data('d/m/y à H:i',strtotime($value));
}
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
