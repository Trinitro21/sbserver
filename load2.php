<?php

/*Retrieves the file from a key

X-PETC-A: token if no nnid
X-PETC-B: token if nnid
X-PETC-C: key
*/

include "functions.php";
init(true);

$key=$h["X-PETC-C"];

fromkey(true);
