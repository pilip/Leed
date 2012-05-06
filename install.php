<?php require_once('common.php'); ?>


<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title></title>
	<meta name="description" content="">
	<meta name="author" content="">

	<meta name="viewport" content="width=device-width">

	<link rel="stylesheet" href="css/style.css">

	<script src="js/libs/modernizr-2.5.3-respond-1.1.0.min.js"></script>
</head>
<body>
	<div id="header-container">
		<header class="wrapper clearfix">
			<h1 class="logo" id="title"><a href="./index.php">L<i>eed</i></a></h1>
			<nav>
			</nav>
		</header>
	</div>


	<div id="main-container">

<div id="main" class="wrapper clearfix">



			<?php
if(isset($_['installButton'])){
	//Création de la base et des tables
	$feedManager->create();
	$eventManager->create();
	$userManager->create();
	$folderManager->create();
	$configurationManager->create();
	//Ajout de l'administrateur
	$admin = new User();
	$admin->setLogin($_['login']);
	$admin->setPassword($_['password']);
	$admin->save();
	//Identification de l'utilisateur en session
	$_SESSION['currentUser'] = serialize($admin);
	//Ajout des préférences et reglages
	$configurationManager->put('root',$_['root']);
	//$configurationManager->put('view',$_['view']);
	$configurationManager->put('articleView',$_['articleView']);

	$configurationManager->put('articlePerPages',$_['articlePerPages']);
	$configurationManager->put('articleDisplayLink',$_['articleDisplayLink']);
	$configurationManager->put('articleDisplayDate',$_['articleDisplayDate']);
	$configurationManager->put('articleDisplayAuthor',$_['articleDisplayAuthor']);
	//Création du dossier de base
	$folder = new Folder();
	$folder->setName($_['category']);
	$folder->setParent(-1);
	$folder->setIsopen(1);
	$folder->save();
	

?>

	 <article style="width:100%;">
				<header>
					<h1>Installation de Leed termin&eacute;e</h1>
					<p>L'installation de Leed est termin&eacute;e, n'oubliez pas de mettre en place le CRON adapt&eacute; pour que vos flux se mettent &agrave; jour, exemple :</p>

					<code>sudo crontab -e</code>
					<p>Dans le fichier qui s'ouvre ajoutez la ligne :</p>
					<code>0 * * * * wget -q -O /var/www/leed/logsCron http://127.0.0.1/leed/action.php?action=synchronize</code>
					<p>Quittez et sauvegardez le fichier.</p>
					<p>Cet exemple mettra &agrave; jour vos flux toutes les heures et ajoutera le rapport de mise a jour sous le nom "logsCron" dans votre dossier leed</p>
	 				<p>N'oubliez pas de supprimer la page install.php par mesure de s&eacute;curit&eacute;</p>
	 				<p>Cliquez <a style="color:#F16529;" href="index.php">ici</a> pour acceder au script</p>
	 <?php
}else{
?>

			<aside>
				<h3 class="left">Verifications</h3> 
				<ul class="clear" style="margin:0">

						<?php 

						if(!is_writable('./')){
							$test['Erreur'][]='Ecriture impossible dans le repertoire Leed, veuillez ajouter les permissions en ecriture sur tous le dossier (sudo chmod 777 -R /var/www/leed/)';
						}else{
							$test['Succ&egrave;s'][]='Permissions sur le dossier courant : OK';
						}

						if (!@function_exists('file_get_contents')){
							 $test['Erreur'][] = 'La fonction requise "file_get_contents" est inaccessible sur votre serveur, verifiez votre version de PHP.';
						}else{
							 $test['Succ&egrave;s'][] = 'Fonction requise "file_get_contents" : OK';	
						}
						if (!@function_exists('file_put_contents')){
							 $test['Erreur'][] = 'La fonction requise "file_put_contents" est inaccessible sur votre serveur, verifiez votre version de PHP.';
						}else{
							 $test['Succ&egrave;s'][] = 'Fonction requise "file_put_contents" : OK';	
						}
						if (@version_compare(PHP_VERSION, '4.3.0') <= 0){
						 $test['Erreur'][] = 'Votre version de PHP ('.PHP_VERSION.') est trop ancienne, il est possible que certaines fonctionalitees du script comportent des disfonctionnements.';
						}else{
						 $test['Succ&egrave;s'][] = 'Compabilit&eacute; de version PHP ('.PHP_VERSION.') : OK';	
						}

						if (!@extension_loaded('sqlite3')){
						 $test['Erreur'][] = 'L\'Extension Sqlite3 n\'est pas activ&eacute;e sur votre serveur, merci de bien vouloir l\'installer';
						}else{
						 $test['Succ&egrave;s'][] = 'Extension Sqlite3 : OK';	
						}

						foreach($test as $type=>$messages){
						?>
						<li style="font-size:10px;color:#ffffff;background-color:<?php echo ($type=='Erreur'?'#F16529':'#008000'); ?>"><?php echo $type; ?> :<ul><?php foreach($messages as $message){?><li style="border:1px solid #212121"><?php echo $message; ?></li><?php } ?></ul></li><li>&nbsp;</li>
						<?php } ?>
				</ul>
			</aside>

	<?php  if(!isset($test['Erreur'])){ ?>		
	<form action="install.php" method="POST">
			<article>
				<header>
					<h1>Installation de Leed</h1>
					<p>Merci de prendre quelques instants pour v&eacute;rifier les infos ci dessous :</p>
				
				</header>
			
				<section>
					<h2>G&eacute;n&eacute;ral</h2>
					<p>Racine du projet : <input type="text" name="root" value="<?php echo str_replace(basename(__FILE__),'','http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>"></p>
					<h3>Laissez bien un "/" en fin de chaine ex : http://monsite.com/leed/</h3>
				</section>

				<section>
					<h2>Administrateur</h2>
					<p>Identifiant de l'administrateur: <input type="text" name="login" placeholder="Identifiant"></p>
					<p>Mot de passe de l'administrateur: <input type="text" name="password" placeholder="Mot de passe"></p>
					<h3>Si vous perdez vos identifiants admin, supprimez le fichier database.db pour reinitialiser le script (nb: l'ensemble des donn&eacute;es seront perdues)</h3>
				</section>

				<section>
					<h2>Pr&eacute;ferences</h2>
					<!--<p>Vue des flux: <input type="radio" value="list" name="view">Liste <input type="radio" value="mosaic" name="view">Mosaique</p>
					<h3>Mosaic : affichage par bloc, style netvives, liste: affichage en liste style rssLounge</h3>-->
					<p>Affichage des articles: <input type="radio" checked="checked" value="partial" name="articleView">Partiel <input type="radio" value="complete" name="articleView">Complet</p>
					<h3>Nb: si vous choissisez un affichage partiel des articles, un click sur ces derniers menera à l'article sur le blog de l'auteur.</h3>
					<p>Nombre d'articles par pages: <input type="text" value="5" name="articlePerPages"></p>
					<p>Affichage du lien direct de l'article: <input type="radio" checked="checked" value="1" name="articleDisplayLink">Oui <input type="radio" value="0" name="articleDisplayLink">Non</p>
					<p>Affichage de la date de l'article: <input type="radio" checked="checked" value="1" name="articleDisplayDate">Oui <input type="radio" value="0" name="articleDisplayDate">Non</p>
					<p>Affichage de l'auteur de l'article: <input type="radio" checked="checked" value="1" name="articleDisplayAuthor">Oui <input type="radio" value="0" name="articleDisplayAuthor">Non</p>
					<p>Cat&eacute;gorie par defaut: <input type="text" value="General" name="category"></p>
					
				</section>
				<button name="installButton">Lancer l'installation</button>
			</article>
	</form>		
	<?php }else{ ?>
	<p>Il vous manque des pr&eacute;requis pour continuer l'installation, r&eacute;f&eacute;rez vous au panneau de droite.</p>
	<?php }?>		
	<?php } ?>
		</div> <!-- #main -->


	</div> <!-- #main-container -->

	<div id="footer-container">
		<footer class="wrapper">
			<p>Leed "Light Feed" by <a target="_blank" href="http://blog.idleman.fr">Idleman</a></p>
		</footer>
	</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.2.min.js"><\/script>')</script>

<script src="js/script.js"></script>
</body>
</html>