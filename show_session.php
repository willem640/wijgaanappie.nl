<?php
session_start();
echo json_encode($_SESSION['orderable_array'][ (int)$_GET['a'] ]);