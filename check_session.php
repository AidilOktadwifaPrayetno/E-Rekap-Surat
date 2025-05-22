<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['monitor', 'petugas', 'admin'])) {
    echo json_encode(['status' => 'expired']);
} else {
    echo json_encode(['status' => 'active']);
}