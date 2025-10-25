<?php
require_once 'vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Start session
session_start();

// Initialize Twig
$loader = new FilesystemLoader('templates');
$twig = new Environment($loader);

// Simple routing
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// Remove trailing slash
$path = rtrim($path, '/');

// Route handling
switch ($path) {
    case '':
    case '/':
        echo $twig->render('pages/landing.html.twig');
        break;
    
    case '/auth/login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            handleLogin();
        } else {
            echo $twig->render('pages/auth/login.html.twig');
        }
        break;
    
    case '/auth/signup':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            handleSignup();
        } else {
            echo $twig->render('pages/auth/signup.html.twig');
        }
        break;
    
    case '/dashboard':
        if (!isLoggedIn()) {
            header('Location: /auth/login');
            exit;
        }
        echo $twig->render('pages/dashboard.html.twig', [
            'user' => $_SESSION['user'],
            'ticketStats' => getTicketStats()
        ]);
        break;
    
    case '/tickets':
        if (!isLoggedIn()) {
            header('Location: /auth/login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Debug: Log POST data
            error_log("POST data received: " . json_encode($_POST));
            handleTicketAction();
        }
        
        echo $twig->render('pages/tickets.html.twig', [
            'user' => $_SESSION['user'],
            'tickets' => getTickets()
        ]);
        break;
    
    case '/logout':
        session_destroy();
        header('Location: /');
        exit;
        break;
    
    default:
        header('Location: /');
        exit;
}

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user']);
}

function handleLogin() {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $users = getUsers();
    $user = null;
    
    foreach ($users as $u) {
        if ($u['email'] === $email && $u['password'] === $password) {
            $user = $u;
            break;
        }
    }
    
    if ($user) {
        $_SESSION['user'] = $user;
        header('Location: /dashboard');
        exit;
    } else {
        header('Location: /auth/login?error=invalid_credentials');
        exit;
    }
}

function handleSignup() {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $users = getUsers();
    
    // Check if user already exists
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            header('Location: /auth/signup?error=user_exists');
            exit;
        }
    }
    
    // Add new user
    $users[] = [
        'name' => $name,
        'email' => $email,
        'password' => $password
    ];
    
    saveUsers($users);
    header('Location: /auth/login?success=account_created');
    exit;
}

function handleTicketAction() {
    $action = $_POST['action'] ?? '';
    $ticketId = $_POST['ticket_id'] ?? '';
    
    // Debug: Log the action
    error_log("Ticket action: " . $action . ", ID: " . $ticketId);
    
    switch ($action) {
        case 'create':
            createTicket($_POST);
            break;
        case 'update':
            updateTicket($ticketId, $_POST);
            break;
        case 'delete':
            deleteTicket($ticketId);
            break;
    }
    
    header('Location: /tickets');
    exit;
}

function getUsers() {
    $file = 'data/users.json';
    if (!file_exists($file)) {
        return [];
    }
    return json_decode(file_get_contents($file), true) ?: [];
}

function saveUsers($users) {
    $dir = 'data';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents('data/users.json', json_encode($users, JSON_PRETTY_PRINT));
}

function getTickets() {
    $file = 'data/tickets.json';
    if (!file_exists($file)) {
        return [];
    }
    return json_decode(file_get_contents($file), true) ?: [];
}

function saveTickets($tickets) {
    $dir = 'data';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents('data/tickets.json', json_encode($tickets, JSON_PRETTY_PRINT));
}

function createTicket($data) {
    $tickets = getTickets();
    $ticket = [
        'id' => uniqid(),
        'title' => $data['title'] ?? '',
        'description' => $data['description'] ?? '',
        'status' => $data['status'] ?? 'open',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    // Debug: Log ticket creation
    error_log("Creating ticket: " . json_encode($ticket));
    
    $tickets[] = $ticket;
    saveTickets($tickets);
    
    // Debug: Log after save
    error_log("Tickets after save: " . json_encode($tickets));
}

function updateTicket($id, $data) {
    $tickets = getTickets();
    foreach ($tickets as &$ticket) {
        if ($ticket['id'] === $id) {
            $ticket['title'] = $data['title'] ?? $ticket['title'];
            $ticket['description'] = $data['description'] ?? $ticket['description'];
            $ticket['status'] = $data['status'] ?? $ticket['status'];
            $ticket['updated_at'] = date('Y-m-d H:i:s');
            break;
        }
    }
    saveTickets($tickets);
}

function deleteTicket($id) {
    $tickets = getTickets();
    $tickets = array_filter($tickets, function($ticket) use ($id) {
        return $ticket['id'] !== $id;
    });
    saveTickets(array_values($tickets));
}

function getTicketStats() {
    $tickets = getTickets();
    $stats = [
        'total' => count($tickets),
        'open' => 0,
        'in_progress' => 0,
        'closed' => 0
    ];
    
    foreach ($tickets as $ticket) {
        switch ($ticket['status']) {
            case 'open':
                $stats['open']++;
                break;
            case 'in_progress':
                $stats['in_progress']++;
                break;
            case 'closed':
                $stats['closed']++;
                break;
        }
    }
    
    return $stats;
}
?>
