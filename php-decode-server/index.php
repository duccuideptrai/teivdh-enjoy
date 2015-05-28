<?php
require_once("config.php");
define('START_OFFSET', 0);
define('FILM_PER_PAGE', 100);

function getLastestWatched($index,$pageSize,$con_db){
	$sql="select distinct `xml_id`,`name`,`description`,`image_url` FROM `log` ORDER BY `create_time` DESC limit :pageSize OFFSET :index";
	$stm=$con_db->prepare($sql);
	$stm->bindValue(':index', (int) $index, PDO::PARAM_INT); 
	$stm->bindValue(':pageSize', (int) $pageSize, PDO::PARAM_INT); 
	$stm->execute();
	return $stm->fetchAll();
}

function getMostWatched($index,$pageSize,$con_db){
	$sql=<<<DUCCUI
		select 
			`xml_id`,`name`,`image_url`,count(*) as watch_count,`description`
		FROM 
			(
		        SELECT  
		        	`xml_id`,`name`,`description`,`image_url`
		        FROM 
		        	`log`
		        GROUP BY 
		        	`xml_id`, `ip`
		    ) as tmp_filter
		GROUP BY `xml_id`
		ORDER BY  watch_count DESC limit :pageSize offset :index
DUCCUI;

	$stm=$con_db->prepare($sql);
	$stm->bindValue(':index', (int) $index, PDO::PARAM_INT); 
	$stm->bindValue(':pageSize', (int) $pageSize, PDO::PARAM_INT); 
	$stm->execute();
	return $stm->fetchAll();
}


try {
	$con_db=new PDO($connect_string,DB_USER,DB_PASSWORD,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
} catch (Exception $e) {
	die('khong mo dc database'. $e->getMessage());
}

$lastWatched=getLastestWatched(START_OFFSET,FILM_PER_PAGE,$con_db);
$mostWatched=getMostWatched(START_OFFSET,FILM_PER_PAGE,$con_db);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<link rel="stylesheet" href="css/normalize.css" />
	<link rel="stylesheet" href="css/style.css" />
	<title>Watched History</title>
</head>
<body>
	<div class="page-content">
		<div class="film-info">
			<div class="film-info__title">Last Watch Video</div>
			<div class="film-list">
				<?php
					foreach ($lastWatched as $film) {
						?>
						<div class="film-list__item">
							<div class="film-list__item-wrapper">
								<img class="film-list__item-thumbnail" src="<?=$film['image_url']?>" alt="<?=$film['name']?>" class="film-list__thumbnail" />
								<div class="film-list__item-title"><?=$film['name']?></div>
								<div class="film-list__item-description"><?=$film['description']?></div>
							</div>
						</div>
						<?php
					}
				?>	
			</div>
		</div>
		<div class="film-info">
			<div class="film-info__title">Most Watch Video</div>
			<div class="film-list">
				<?php
					foreach ($mostWatched as $film) {
						?>
						<div class="film-list__item">
							<div class="film-list__item-wrapper">
								<img class="film-list__item-thumbnail" src="<?=$film['image_url']?>" alt="<?=$film['name']?>" class="film-list__thumbnail" />
								<div class="film-list__item-title"><?=$film['name']?></div>
								<div class="film-list__item-description"><?=$film['description']?></div>
							</div>
						</div>
						<?php
					}
				?>	
			</div>
		</div>
	</div>
</body>
</html>
