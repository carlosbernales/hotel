<?php 


require 'db_con.php';





if(isset($_POST['insertStudent'])){
    $fname = $_POST['firstname'];
    $lname = $_POST['lastname'];
    $email = $_POST['email'];
    $pass  = password_hash($_POST['pswd'], PASSWORD_DEFAULT);

    $sql = 'INSERT INTO students(first_name, last_name, email, password) VALUES(:fname,:lname,:email,:pass)';
    $stmt = $pdo->prepare($sql);

    $data = [
        'fname'=> $fname,
        'lname' => $lname,
        'email' => $email,
        'pass' => $pass    
    ];
    try {
        $stmt->execute($data);
         echo 'success!';
        header('Location: index.php');
    } catch (PDOException $e) {
        echo 'Error:'. $e->getMessage();
    }

}



if(isset($_POST['updateStudent'])){

    $id = $_POST['student_id'];
    $fname = $_POST['firstname'];
    $lname = $_POST['lastname'];
    $email = $_POST['email'];

    $sql = 'UPDATE students SET first_name=:fname, last_name=:lname,email=:email WHERE id= :id';
    $stmt = $pdo->prepare($sql);

    $data = [
        'fname'=> $fname,
        'lname' => $lname,
        'email' => $email,
        'id' => $id    
    ];
    try {
        $stmt->execute($data);
        // echo 'success!';
        header('Location: index.php');
    } catch (PDOException $e) {
        echo 'Error:'. $e->getMessage();
    }

}



if(isset($_GET['delete_record'])){

    $id = $_GET['delete_record'];

    $sql = 'DELETE FROM students WHERE id = :id';
    $stmt = $pdo->prepare($sql);

    $data = [
        'id' => $id    
    ];
    try {
        $stmt->execute($data);
        // echo 'success!';
        header('Location: index.php');
    } catch (PDOException $e) {
        echo 'Error:'. $e->getMessage();
    }

}



























?>