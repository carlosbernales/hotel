<?php
// Simple PHP test file
session_start();
require_once "db.php";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include header and sidebar
include_once "header.php";
include_once "sidebar.php";
?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="index.php"><em class="fa fa-home"></em></a></li>
            <li class="active">PHP Test</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title">PHP Processing Test</h2>
                </div>
                <div class="panel-body">
                    <h3>PHP Information:</h3>
                    <ul>
                        <li>PHP Version: <?php echo PHP_VERSION; ?></li>
                        <li>Server Software: <?php echo $_SERVER['SERVER_SOFTWARE']; ?></li>
                        <li>Document Root: <?php echo $_SERVER['DOCUMENT_ROOT']; ?></li>
                        <li>Current Time: <?php echo date('Y-m-d H:i:s'); ?></li>
                    </ul>
                    
                    <h3>Database Test:</h3>
                    <?php
                    if (isset($con) && $con) {
                        echo '<div class="alert alert-success">Database connection successful!</div>';
                    } else {
                        echo '<div class="alert alert-danger">Database connection failed!</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div> 