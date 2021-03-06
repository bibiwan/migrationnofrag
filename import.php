<?php

// Load WordPress
require_once './wp-load.php';
require_once './wp-admin/includes/taxonomy.php';
ini_set('memory_limit', '2048M');

// Set the timezone so times are calculated correctly

date_default_timezone_set('Europe/Paris');

function traitement_youtube($content){
	$re = '/(\<div.+youtube\.com\/watch\?v=(\w+).+\<\/div\>)/';
	//$replacement = "<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/$2\" frameborder=\"0\" allowfullscreen></iframe>";
	$replacement = "http://www.youtube.com/watch?v=$2";
	$content = preg_replace($re, $replacement, $content);
	
	//preg_match_all($re, $content, $matches, PREG_SET_ORDER, 0);
	return $content;
}


function insert_news($news_id,$old_newsid) {
	$host = "localhost";
	$dbname = "wp";
	$login_db = "root";
	$pwd_db = "";
	
	$bdd = new PDO('mysql:host='.$host.';dbname='.$dbname.';charset=utf8', $login_db , $pwd_db);
	$reponse = $bdd->query('insert into wp_transpo_news values(\''.$news_id.'\','.$old_newsid.');');
	$donnees = $reponse->fetch();
}

function update_news($news_id,$old_newsid) {
	$host = "localhost";
	$dbname = "wp";
	$login_db = "root";
	$pwd_db = "";
	
	$bdd = new PDO('mysql:host='.$host.';dbname='.$dbname.';charset=utf8', $login_db , $pwd_db);
	$reponse = $bdd->query('update wp_posts set ID = '.$old_newsid.' where ID = '.$news_id.';');
	$donnees = $reponse->fetch();
	return $old_newsid;
}

function get_user($oldid) {
	$host = "localhost";
	$dbname = "wp";
	$login_db = "root";
	$pwd_db = "";
	
	$bdd = new PDO('mysql:host='.$host.';dbname='.$dbname.';charset=utf8', $login_db , $pwd_db);
	$reponse = $bdd->query('select * from wp_transpo_authors where author_id=\''.$oldid.'\';');
	$donnees = $reponse->fetch();
	return $donnees['id'];
}

function get_game($id) {
	$host = "localhost";
	$dbname = "nofrag_old";
	$login_db = "root";
	$pwd_db = "";
	
	$bdd = new PDO('mysql:host='.$host.';dbname='.$dbname.';charset=utf8', $login_db , $pwd_db);
	$reponse = $bdd->query('select * from games where id=\''.$id.'\';');
	$donnees = $reponse->fetch();
	return $donnees['name'];
}

$host = "localhost";
$dbname = "nofrag_old";
$login_db = "root";
$pwd_db = "";
$bdd = new PDO('mysql:host='.$host.';dbname='.$dbname.';charset=utf8', $login_db , $pwd_db);
$reponse = $bdd->query('select * from news order by news_id desc;');
$i = 0;
while ($donnees = $reponse->fetch()){

	$content = traitement_youtube($donnees['content']);
	// Create post
	$id = wp_insert_post(array(
		'post_title'    => $donnees['title'],
		'post_content'  => $content,
		'post_date'     => $donnees['date'],
		'post_author'   => get_user($donnees['author_id']),
		'post_type'     => "post",
		'post_status'   => 'publish',
	));

	if ($id) {
		
		$id = update_news($id,$id+200000);
		insert_news($id,$donnees['news_id']);
		
		// Set category - create if it doesn't exist yet
		if ($donnees['game_id'] > 0){
			$jeu = get_game($donnees['game_id']);
			wp_set_post_tags($id, $jeu, false);
			
			
		}
		
		switch ($donnees['type']){
			//annonce
			case '9':
			wp_set_post_terms($id, 147, 'category');
			break ;
			//chroniques
			case '23':
			wp_set_post_terms($id, 132, 'category');
			break;
			// bullshit
			case '36' :
			wp_set_post_terms($id, 144, 'category');
			break ;
			// business
			case '20' :
			wp_set_post_terms($id, 141, 'category');
			break;
			// dossiers
			case '22' :
			wp_set_post_terms($id, 133, 'category');
			break;
			// mod 
			case '15' :
			wp_set_post_terms($id, 146, 'category');
			break;
			// vrac
			case '18' :
			wp_set_post_terms($id, 142, 'category');
			break ;
			// patch
			case '7' :
			wp_set_post_terms($id, 143, 'category');
			break ;
			// preview
			case '2' :
			wp_set_post_terms($id, 131, 'category');
			break ;
			// bons plans
			case '35' :
			wp_set_post_terms($id, 134, 'category');
			break;
			//Tests
			case '12' :
			wp_set_post_terms($id, 129, 'category');
			break;
			//vidéos
			case '6':
			wp_set_post_terms($id, 145, 'category');
			break;
			default :
			// news par défaut
			wp_set_post_terms($id, 130, 'category');
		}
		
		$i++;

	}	else {
		echo "WARNING: Failed to insert post into WordPress\n";
	}
}

echo "ok ".$i;
?>