<?php

require_once(__DIR__.'/src/autoload.php');
$gameLauncher = new Application\Launcher();
$gameLauncher->run();

/*
function main() {

	$gameOver = false;
	$stdin = fopen('php://stdin', 'r');
	$stdout = fopen('php://stdout', 'r');

	while($gameOver == false) {

		$line = trim(fgets(STDIN));
		if($line) {
echo 
"\033[1;34mThis is a blue text.\033[0m
O | X | X
_________

X | O | X
_________

X | X | O
";

echo 
" 

";
		}

	}

}

main();*/