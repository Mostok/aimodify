<?php

exec("zip -r /var/www/www-root/data/www/test.ru/donationsystem.zip /var/www/www-root/data/www/test.ru/", $r, $rr); 

echo "<pre>";
print_r($r);
echo "</pre>";

echo "<pre>";
print_r($rr);
echo "</pre>";


?>