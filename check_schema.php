<?php
$cols = DB::select('SHOW COLUMNS FROM gallery_images');
foreach ($cols as $c) {
    echo $c->Field . ' (' . $c->Type . ')' . "\n";
}
echo "\n--- promotions ---\n";
$cols2 = DB::select('SHOW COLUMNS FROM promotions');
foreach ($cols2 as $c) {
    echo $c->Field . ' (' . $c->Type . ')' . "\n";
}
