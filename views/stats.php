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
  <div class="stat">
    <div class="stat-num"><?= $pct ?>%</div>
    <div class="stat-label">PROGRESS</div>
  </div>
</div>

<div class="progress-wrap">
  <div class="progress-bar" style="width:<?= $pct ?>%"></div>
</div>

<div class="filter-tabs">
  <a href="todo.php" class="filter-tab <?= $filter === 'all' ? 'active' : '' ?>">ALL (<?= $total ?>)</a>
  <a href="todo.php?filter=pending" class="filter-tab <?= $filter === 'pending' ? 'active' : '' ?>">PENDING (<?= $pending ?>)</a>
  <a href="todo.php?filter=done" class="filter-tab <?= $filter === 'done' ? 'active' : '' ?>">DONE (<?= $done_count ?>)</a>
</div>
