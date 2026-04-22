<?php
$action = $_POST['action'] ?? '';
if (!$action) return;

$f        = $_POST['filter'] ?? 'all';
$redirect = 'todo.php' . ($f !== 'all' ? '?filter=' . urlencode($f) : '');

switch ($action) {
    case 'add':
        if (empty(trim($_POST['text'] ?? ''))) break;
        $_SESSION['todos'][] = [
            'id'         => $_SESSION['next_id']++,
            'text'       => trim($_POST['text']),
            'done'       => false,
            'priority'   => in_array($_POST['priority'] ?? '', ['high', 'medium', 'low']) ? $_POST['priority'] : 'medium',
            'due_date'   => !empty($_POST['due_date']) ? $_POST['due_date'] : null,
            'created_at' => date('Y-m-d H:i'),
        ];
        header('Location: ' . $redirect);
        exit;

    case 'toggle':
        if (!isset($_POST['id'])) break;
        $id = (int)$_POST['id'];
        foreach ($_SESSION['todos'] as &$t) {
            if ($t['id'] === $id) { $t['done'] = !$t['done']; break; }
        }
        unset($t);
        header('Location: ' . $redirect);
        exit;

    case 'edit':
        if (!isset($_POST['id']) || empty(trim($_POST['text'] ?? ''))) break;
        $id = (int)$_POST['id'];
        foreach ($_SESSION['todos'] as &$t) {
            if ($t['id'] === $id) {
                $t['text']     = trim($_POST['text']);
                $t['priority'] = in_array($_POST['priority'] ?? '', ['high', 'medium', 'low']) ? $_POST['priority'] : ($t['priority'] ?? 'medium');
                $t['due_date'] = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
                break;
            }
        }
        unset($t);
        header('Location: ' . $redirect);
        exit;

    case 'delete':
        if (!isset($_POST['id'])) break;
        $id = (int)$_POST['id'];
        $_SESSION['todos'] = array_values(array_filter($_SESSION['todos'], fn($t) => $t['id'] !== $id));
        header('Location: ' . $redirect);
        exit;

    case 'clear_done':
        $_SESSION['todos'] = array_values(array_filter($_SESSION['todos'], fn($t) => !$t['done']));
        header('Location: todo.php');
        exit;
}
