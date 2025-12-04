<?php
echo "DIR actual: " . __DIR__;
echo "\nRuta con /../php: " . realpath(__DIR__ . '/../php');
echo "\nRuta con /../../php: " . realpath(__DIR__ . '/../../php');
?>
