<?php

namespace FC\HomemadeLog;

require __DIR__."/../vendor/autoload.php";

// create a new file using default values for parameters
$logDefault = Log::newDefault();
// write a line in that file
$logDefault->write("default line 1");

// create a new file at a given location with a given name and extension 
$logCustom = Log::newCustom("./","logCustom",".txt");
// write lines in that file
$logCustom->write("custom line 1");
$logCustom->write("custom line 2");

// create a new file and write a line
$quickLog = Log::newQuickWrite("line 1");
// append this new line at the end of the file
$quickLog->write("line 2");
// erase the content of the file and write a new line
$quickLog->rewrite("line 3");

// display a few info about the file
echo "Max File Size in Kb: ".$quickLog->getMaxFileSize()."<br>";
echo "Max File Size Readable: ".$quickLog->getMaxFileSizeReadable()."<br>";
echo "File Size Readable: ".$quickLog->getFileSize()."<br>";
