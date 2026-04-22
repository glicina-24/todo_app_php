<?php
session_start();

// Initialize todos in session
if (!isset($_SESSION['todos'])) {
    $_SESSION['todos'] = [];
    $_SESSION['next_id'] = 1;
}

// Handle actions
$action = $_POST['action'] ?? '';

if ($action === 'add' && !empty(trim($_POST['text'] ?? ''))) {
    $text = trim($_POST['text']);
    $_SESSION['todos'][] = [
        'id' => $_SESSION['next_id']++,
        'text' => $text,
        'done' => false,
        'created_at' => date('Y-m-d H:i'),
    ];
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if ($action === 'toggle' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    foreach ($_SESSION['todos'] as &$todo) {
        if ($todo['id'] === $id) {
            $todo['done'] = !$todo['done'];
            break;
        }
    }
    unset($todo);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if ($action === 'delete' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $_SESSION['todos'] = array_values(array_filter($_SESSION['todos'], fn($t) => $t['id'] !== $id));
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if ($action === 'clear_done') {
    $_SESSION['todos'] = array_values(array_filter($_SESSION['todos'], fn($t) => !$t['done']));
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$todos = $_SESSION['todos'];
$total = count($todos);
$done_count = count(array_filter($todos, fn($t) => $t['done']));
$pending = $total - $done_count;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TODO</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Noto+Sans+JP:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg: #0d0d0d;
    --surface: #161616;
    --border: #2a2a2a;
    --accent: #e8ff00;
    --accent2: #ff4d4d;
    --text: #f0f0f0;
    --muted: #555;
    --done-text: #3a3a3a;
  }

  body {
    background: var(--bg);
    color: var(--text);
    font-family: 'Noto Sans JP', sans-serif;
    font-weight: 300;
    min-height: 100vh;
    padding: 40px 20px 80px;
  }

  .container {
    max-width: 640px;
    margin: 0 auto;
  }

  /* Header */
  .header {
    margin-bottom: 48px;
  }
  .header-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(64px, 15vw, 96px);
    letter-spacing: 0.04em;
    line-height: 1;
    color: var(--accent);
    text-shadow: 4px 4px 0 #5a6400;
  }
  .header-sub {
    font-size: 12px;
    letter-spacing: 0.25em;
    text-transform: uppercase;
    color: var(--muted);
    margin-top: 4px;
  }

  /* Stats bar */
  .stats {
    display: flex;
    gap: 24px;
    margin-bottom: 32px;
    padding: 16px 20px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-left: 3px solid var(--accent);
  }
  .stat { text-align: center; }
  .stat-num {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 32px;
    line-height: 1;
    color: var(--accent);
  }
  .stat-label {
    font-size: 10px;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--muted);
    margin-top: 2px;
  }
  .stat-num.done-num { color: var(--muted); }

  /* Add form */
  .add-form {
    display: flex;
    gap: 0;
    margin-bottom: 32px;
  }
  .add-input {
    flex: 1;
    background: var(--surface);
    border: 1px solid var(--border);
    border-right: none;
    color: var(--text);
    font-family: 'Noto Sans JP', sans-serif;
    font-size: 15px;
    font-weight: 300;
    padding: 14px 18px;
    outline: none;
    transition: border-color 0.2s;
  }
  .add-input::placeholder { color: var(--muted); }
  .add-input:focus { border-color: var(--accent); }
  .add-btn {
    background: var(--accent);
    color: #000;
    border: none;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 18px;
    letter-spacing: 0.08em;
    padding: 14px 24px;
    cursor: pointer;
    transition: background 0.15s, transform 0.1s;
    white-space: nowrap;
  }
  .add-btn:hover { background: #d4e800; }
  .add-btn:active { transform: scale(0.97); }

  /* Filter / clear row */
  .actions-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
  }
  .section-label {
    font-size: 10px;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    color: var(--muted);
  }
  .clear-btn {
    background: none;
    border: 1px solid var(--border);
    color: var(--muted);
    font-family: 'Noto Sans JP', sans-serif;
    font-size: 11px;
    letter-spacing: 0.1em;
    padding: 6px 12px;
    cursor: pointer;
    transition: all 0.15s;
  }
  .clear-btn:hover { border-color: var(--accent2); color: var(--accent2); }

  /* Todo list */
  .todo-list { display: flex; flex-direction: column; gap: 2px; }

  .todo-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-left: 3px solid transparent;
    transition: border-color 0.2s, background 0.2s;
    animation: slideIn 0.25s ease;
  }
  @keyframes slideIn {
    from { opacity: 0; transform: translateX(-12px); }
    to   { opacity: 1; transform: translateX(0); }
  }
  .todo-item:hover { border-left-color: var(--accent); background: #1c1c1c; }
  .todo-item.is-done {
    border-left-color: var(--border);
    background: var(--bg);
  }
  .todo-item.is-done:hover { border-left-color: var(--muted); }

  /* Checkbox form */
  .toggle-form, .delete-form { display: contents; }

  .check-btn {
    width: 20px; height: 20px;
    border: 2px solid var(--border);
    background: transparent;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    transition: border-color 0.2s, background 0.2s;
    padding: 0;
  }
  .check-btn:hover { border-color: var(--accent); }
  .check-btn.checked {
    background: var(--muted);
    border-color: var(--muted);
  }
  .check-icon {
    width: 10px; height: 10px;
    fill: none;
    stroke: var(--bg);
    stroke-width: 2.5;
    stroke-linecap: round;
    stroke-linejoin: round;
    display: none;
  }
  .check-btn.checked .check-icon { display: block; }

  .todo-text {
    flex: 1;
    font-size: 15px;
    line-height: 1.4;
    color: var(--text);
    transition: color 0.2s;
    word-break: break-all;
  }
  .todo-item.is-done .todo-text {
    color: var(--done-text);
    text-decoration: line-through;
  }

  .todo-date {
    font-size: 10px;
    color: var(--muted);
    white-space: nowrap;
    letter-spacing: 0.05em;
  }

  .del-btn {
    background: none;
    border: none;
    color: var(--muted);
    cursor: pointer;
    padding: 4px;
    display: flex; align-items: center; justify-content: center;
    transition: color 0.15s;
    flex-shrink: 0;
  }
  .del-btn:hover { color: var(--accent2); }
  .del-btn svg { width: 14px; height: 14px; }

  /* Empty state */
  .empty {
    text-align: center;
    padding: 60px 20px;
    color: var(--muted);
    border: 1px dashed var(--border);
  }
  .empty-icon {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 48px;
    color: #222;
    display: block;
    margin-bottom: 8px;
  }
  .empty-text { font-size: 13px; letter-spacing: 0.1em; }

  /* Progress bar */
  .progress-wrap {
    height: 2px;
    background: var(--border);
    margin-bottom: 28px;
    overflow: hidden;
  }
  .progress-bar {
    height: 100%;
    background: var(--accent);
    transition: width 0.4s ease;
  }
</style>
</head>
<body>
<div class="container">

  <div class="header">
    <div class="header-title">TODO</div>
    <div class="header-sub">タスク管理 &mdash; セッション保存</div>
  </div>

  <!-- Stats -->
  <div class="stats">
    <div class="stat">
      <div class="stat-num"><?= $total ?></div>
      <div class="stat-label">TOTAL</div>
    </div>
    <div class="stat">
      <div class="stat-num"><?= $pending ?></div>
      <div class="stat-label">PENDING</div>
    </div>
    <div class="stat">
      <div class="stat-num done-num"><?= $done_count ?></div>
      <div class="stat-label">DONE</div>
    </div>
  </div>

  <!-- Progress -->
  <?php $pct = $total > 0 ? round($done_count / $total * 100) : 0; ?>
  <div class="progress-wrap">
    <div class="progress-bar" style="width:<?= $pct ?>%"></div>
  </div>

  <!-- Add form -->
  <form class="add-form" method="POST">
    <input type="hidden" name="action" value="add">
    <input class="add-input" type="text" name="text" placeholder="新しいタスクを入力..." autocomplete="off" autofocus>
    <button class="add-btn" type="submit">ADD</button>
  </form>

  <!-- Actions row -->
  <div class="actions-row">
    <span class="section-label"><?= $total ?> TASKS</span>
    <?php if ($done_count > 0): ?>
    <form method="POST">
      <input type="hidden" name="action" value="clear_done">
      <button class="clear-btn" type="submit">完了済みを削除 (<?= $done_count ?>)</button>
    </form>
    <?php endif; ?>
  </div>

  <!-- Todo list -->
  <?php if (empty($todos)): ?>
    <div class="empty">
      <span class="empty-icon">ZERO</span>
      <div class="empty-text">タスクがありません。上に入力してください。</div>
    </div>
  <?php else: ?>
    <div class="todo-list">
      <?php foreach (array_reverse($todos) as $todo): ?>
        <div class="todo-item <?= $todo['done'] ? 'is-done' : '' ?>">

          <!-- Toggle -->
          <form class="toggle-form" method="POST">
            <input type="hidden" name="action" value="toggle">
            <input type="hidden" name="id" value="<?= $todo['id'] ?>">
            <button class="check-btn <?= $todo['done'] ? 'checked' : '' ?>" type="submit" title="完了切替">
              <svg class="check-icon" viewBox="0 0 12 10">
                <polyline points="1,5 4.5,8.5 11,1"/>
              </svg>
            </button>
          </form>

          <span class="todo-text"><?= htmlspecialchars($todo['text']) ?></span>
          <span class="todo-date"><?= $todo['created_at'] ?></span>

          <!-- Delete -->
          <form class="delete-form" method="POST">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= $todo['id'] ?>">
            <button class="del-btn" type="submit" title="削除">
              <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <line x1="2" y1="2" x2="12" y2="12"/><line x1="12" y1="2" x2="2" y2="12"/>
              </svg>
            </button>
          </form>

        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>
</body>
</html>