<?php
$conn = new mysqli("localhost", "root", "", "restAPI");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        $id   = $conn->real_escape_string($_GET['id']);
        $sql  = $conn->query("SELECT * FROM customers WHERE id='" . $id . "'");
        $data = $sql->fetch_assoc();
    } else {
        $data = array();
        $sql  = $conn->query("SELECT * FROM customers");
        while ($d = $sql->fetch_assoc()) {
            $data[] = $d;
        }
    }
    exit(json_encode($data));
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['nombre']) && isset($_POST['apellido'])) {
        $nombre   = $conn->real_escape_string($_POST['nombre']);
        $apellido = $conn->real_escape_string($_POST['apellido']);
        $sql      = $conn->query("INSERT INTO customers (nombre,apellido) VALUES ('$nombre','$apellido')");
        exit(json_encode(array("status" => "success")));
    } else {
        exit(json_encode(array("status" => "falied", "reason" => "check your inputs")));
    }
    exit(json_encode($data));
    echo 'POST';
} else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    if (!isset($_GET['id'])) {
        exit(json_encode(array("status" => "falied", "reason" => "check your inputs")));
    }

    $customerID = $conn->real_escape_string($_GET['id']);
    $data       = urldecode(file_get_contents('php://input'));

    if (strpos($data, '=') !== false) {
        //name=Sendid&age=29
        $allPairs = array();
        $data     = explode('&', $data);
        foreach ($data as $pair) {
            $pair               = explode('=', $pair);
            $allPairs[$pair[0]] = $pair[1];
        }

        if (isset($allPairs['nombre']) && isset($allPairs['apellido'])) {
            $conn->query("UPDATE customers SET nombre = '" . $allPairs['nombre'] . "', apellido='" . $allPairs['apellido'] . "' WHERE id = '" . $customerID . "'");
        } else if (isset($allPairs['nombre'])) {
            $conn->query("UPDATE customers SET nombre = '" . $allPairs['nombre'] . "' WHERE id = '" . $customerID . "'");
        } else if (isset($allPairs['apellido'])) {
            $conn->query("UPDATE customers SET apellido='" . $allPairs['apellido'] . "' WHERE id = '" . $customerID . "'");
        } else {
            exit(json_encode(array("status" => "falied", "reason" => "check your inputs")));
        }

        exit(json_encode(array("status" => "success")));
    } else {
        exit(json_encode(array("status" => "falied", "reason" => "check your inputs")));
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    if (!isset($_GET['id'])) {
        exit(json_encode(array("status" => "falied", "reason" => "check your inputs")));
    } else {
        $customerID = $conn->real_escape_string($_GET['id']);
        $conn->query("DELETE FROM customers WHERE id = '" . $customerID . "'");
        exit(json_encode(array("status" => "success")));
    }
}
