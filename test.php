<?php
echo '<pre>';
echo "Current dir: " . __DIR__ . "\n";
echo "Checking paths:\n";

$paths = [
       "/phpbackend/routes/route.php",
    __DIR__ . "/../phpbackend/routes/route.php",
    __DIR__ . "/../../phpbackend/routes/route.php",
    __DIR__ . "/../../../phpbackend/routes/route.php",
    __DIR__ . "/../../../../phpbackend/routes/route.php"
];

foreach ($paths as $p) {
    echo $p . " => " . (file_exists($p) ? "FOUND" : "NOT FOUND") . "\n";
}
