<?php
// Turn off error reporting
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE);

// If repo is passed in the URL
if ($_GET["repo"]) {
	$dir = $_GET["repo"];
}

// Change to the requested directory
chdir($dir);

// Set repo name from directory name
$repo = trim(substr(getcwd(), strripos(getcwd(), "/"), strlen(getcwd())), "/");

// Get information on the remotes
$remote = exec("git remote -v");
$remote = "http://" . str_replace(":", "/",rtrim(substr($remote , strpos($remote, "@")+1), ".git (push)"));

// Set up output array
$output = array();
// Fill array with git log
exec("git log -n 100",$output);
// Push git log into $history array
$history = array();
foreach($output as $line){
    if(strpos($line, 'commit')===0){
	if(!empty($commit)){
	    array_push($history, $commit);
	    unset($commit);
	}
	$commit['hash']   = substr($line, strlen('commit'));
    }
    else if(strpos($line, 'Author')===0){
	$commit['author'] = substr($line, strlen('Author:'));
    }
    else if(strpos($line, 'Date')===0){
	$commit['date']   = substr($line, strlen('Date:'));
    }
    else{
	$commit['message']  .= $line;
    }
}
?>
<!doctype html>
<html lang="en" class="no-js">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link href='http://fonts.googleapis.com/css?family=Droid+Serif|Open+Sans:400,700' rel='stylesheet' type='text/css'>

	<link rel="stylesheet" href="css/reset.css"> <!-- CSS reset -->
	<link rel="stylesheet" href="css/style.css"> <!-- Resource style -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css"> <!-- FontAwesome -->
	<script src="js/modernizr.js"></script> <!-- Modernizr -->

	<title>Git Log: <?php echo $repo; ?></title>
</head>
<body>
	<header>
		<h1><i class="fa fa-code-fork"></i> <?php echo $repo; ?></h1>
	</header>

	<section id="cd-timeline" class="cd-container">

<?php
// Process each entry in history as $item
foreach($history as $item) {
?>
		<div class="cd-timeline-block">
			<div class="cd-timeline-img cd-picture">
				<i class="fa fa-history fa-2x"></i>
			</div> <!-- cd-timeline-img -->

			<div class="cd-timeline-content">
				<h2><?php echo "Commit: " . substr($item['hash'], 0, 8);?></h2>
				<p class="author">Committed by: <?php echo $item['author'];?></p>
				<p class="message"><?php echo $item['message'];?></p>
				<a href="<?php echo $remote; ?>/commit/<?php echo trim($item['hash'], " "); ?>" class="cd-read-more" target="_blank">Read more</a>
				<span class="cd-date"><?php echo $item['date'];?></span>
			</div> <!-- cd-timeline-content -->
		</div> <!-- cd-timeline-block -->
<?php
}
?>
	</section> <!-- cd-timeline -->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="js/main.js"></script> <!-- Resource jQuery -->
</body>
</html>
