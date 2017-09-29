<?php

// Load WordPress
require_once './wp-load.php';
require_once './wp-admin/includes/taxonomy.php';

// Set the timezone so times are calculated correctly
date_default_timezone_set('Europe/Paris');


function insert_author($user_id,$old_userid) {
	$host = "localhost";
	$dbname = "wp";
	$login_db = "root";
	$pwd_db = "";
	$bdd = new PDO('mysql:host='.$host.';dbname='.$dbname.';charset=utf8', $login_db , $pwd_db);
	$reponse = $bdd->query('insert into wp_transpo_authors values(\''.$user_id.'\',\''.$old_userid.'\')');
	$donnees = $reponse->fetch();
}
try
{
$host = "localhost";
$dbname = "nofrag_old";
$login_db = "root";
$pwd_db = "";
$bdd = new PDO('mysql:host='.$host.';dbname='.$dbname.';charset=utf8', $login_db , $pwd_db);
$reponse = $bdd->query('select * from authors order by author_id;');

while ($donnees = $reponse->fetch()){
	$user_login = $donnees['login'];
	$user_pass = wp_generate_password(16, false);
	$user_email = $donnees['email'];

$user_id = wp_create_user($user_login, $user_pass, $user_email);
	if ($user_id) {
		insert_author($user_id,$donnees['author_id']);

	} else {
		echo "WARNING: Failed to insert user into WordPress\n";
	}
}
echo "ok";
}
	catch (Exception $e)
	{
		
		die('Erreur : ' . $e->getMessage());
	}
?>