<?php
echo json_encode($_SESSION['orderable_array'][ (int)$_GET['a'] ]);