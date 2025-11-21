<?php
session_start();
unset($_SESSION['success_message']);
echo 'Message cleared';
