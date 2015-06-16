<?php
$ran = rand(6,2230000);
header("Location: /api/getaid.php?act=info&av=".$ran."");