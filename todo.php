<?php
session_start();

if (!isset($_SESSION['todos'])) {
    $_SESSION['todos'] = [];
    $_SESSION['next_id'] = 1;
}

require_once 'actions.php';

$view       = $_GET['view'] ?? 'list';
$month      = preg_match('/^\d{4}-\d{2}$/', $_GET['month'] ?? '') ? $_GET['month'] : date('Y-m');
$filter     = $_GET['filter'] ?? 'all';
$todos_all  = $_SESSION['todos'];
$total      = count($todos_all);
$done_count = count(array_filter($todos_all, fn($t) => $t['done']));
$pending    = $total - $done_count;
$pct        = $total > 0 ? round($done_count / $total * 100) : 0;

$todos = match($filter) {
    'pending' => array_values(array_filter($todos_all, fn($t) => !$t['done'])),
    'done'    => array_values(array_filter($todos_all, fn($t) => $t['done'])),
    default   => $todos_all,
};
$todos = array_reverse($todos);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TODO</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Noto+Sans+JP:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <div class="header">
    <div class="header-title">TODO</div>
    <div class="header-sub">タスク管理</div>
  </div>

  <div class="view-tabs">
    <a href="todo.php" class="view-tab <?= $view === 'list' ? 'active' : '' ?>">LIST</a>
    <a href="todo.php?view=calendar&month=<?= $month ?>" class="view-tab <?= $view === 'calendar' ? 'active' : '' ?>">CALENDAR</a>
  </div>

  <?php if ($view === 'calendar'): ?>
    <?php include 'views/calendar.php'; ?>
  <?php else: ?>
    <?php include 'views/stats.php'; ?>
    <?php include 'views/form.php'; ?>
    <?php include 'views/list.php'; ?>
  <?php endif; ?>
</div>
<?php include 'views/edit_modal.php'; ?>
</body>
</html>
