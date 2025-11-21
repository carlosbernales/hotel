<?php
copy('https://getcomposer.org/installer', 'composer-setup.php');
if (hash_file('sha384', 'composer-setup.php') === trim(file_get_contents('https://composer.github.io/installer.sig'))) {
    echo 'Installer verified';
} else {
    echo 'Installer corrupt';
    unlink('composer-setup.php');
    exit(1);
}
echo PHP_EOL;
include 'composer-setup.php';
unlink('composer-setup.php');
