<?php
[$year, $mon] = array_map('intval', explode('-', $month));
$first_ts      = mktime(0, 0, 0, $mon, 1, $year);
$days_in_month = (int)date('t', $first_ts);
$start_dow     = (int)date('w', $first_ts); // 0=日曜
$today         = date('Y-m-d');
$prev_month    = date('Y-m', mktime(0, 0, 0, $mon - 1, 1, $year));
$next_month    = date('Y-m', mktime(0, 0, 0, $mon + 1, 1, $year));

$by_date = [];
foreach ($todos_all as $t) {
    if (!empty($t['due_date'])) {
        $by_date[$t['due_date']][] = $t;
    }
}
$unscheduled = array_values(array_filter($todos_all, fn($t) => empty($t['due_date'])));
?>

<div class="cal-header">
  <a href="?view=calendar&month=<?= $prev_month ?>" class="cal-nav">&#8249;</a>
  <span class="cal-month-label"><?= date('Y年n月', $first_ts) ?></span>
  <a href="?view=calendar&month=<?= $next_month ?>" class="cal-nav">&#8250;</a>
</div>

<div class="cal-grid">
  <?php foreach (['日', '月', '火', '水', '木', '金', '土'] as $i => $d): ?>
    <div class="cal-dow <?= $i === 0 ? 'sun' : ($i === 6 ? 'sat' : '') ?>"><?= $d ?></div>
  <?php endforeach; ?>

  <?php for ($i = 0; $i < $start_dow; $i++): ?>
    <div class="cal-day empty"></div>
  <?php endfor; ?>

  <?php for ($d = 1; $d <= $days_in_month; $d++):
    $date_str  = sprintf('%04d-%02d-%02d', $year, $mon, $d);
    $day_tasks = $by_date[$date_str] ?? [];
    $is_today  = $date_str === $today;
    $is_past   = $date_str < $today;
    $dow       = ($start_dow + $d - 1) % 7;
  ?>
    <div class="cal-day <?= $is_today ? 'today' : '' ?> <?= $dow === 0 ? 'sun' : ($dow === 6 ? 'sat' : '') ?>">
      <span class="cal-date <?= $is_today ? 'today-badge' : '' ?>"><?= $d ?></span>
      <div class="cal-tasks">
        <?php foreach ($day_tasks as $t):
          $p = $t['priority'] ?? 'medium';
        ?>
          <div class="cal-task priority-<?= $p ?> <?= $t['done'] ? 'done' : '' ?> <?= (!$t['done'] && $is_past) ? 'overdue' : '' ?>"
            onclick="openEditModal(<?= $t['id'] ?>, <?= json_encode($t['text'], JSON_HEX_QUOT | JSON_HEX_TAG) ?>, '<?= $p ?>', '<?= htmlspecialchars($t['due_date']) ?>')">
            <?= htmlspecialchars(mb_strimwidth($t['text'], 0, 18, '…')) ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endfor; ?>
</div>

<div class="unscheduled-section">
  <div class="section-label" style="margin-bottom:12px">期限未設定 (<?= count($unscheduled) ?>)</div>
  <?php if (empty($unscheduled)): ?>
    <div class="unscheduled-empty">期限未設定のタスクはありません</div>
  <?php else: ?>
    <div class="unscheduled-list">
      <?php foreach (array_reverse($unscheduled) as $t):
        $p = $t['priority'] ?? 'medium';
      ?>
        <div class="unscheduled-item <?= $t['done'] ? 'is-done' : '' ?>"
          onclick="openEditModal(<?= $t['id'] ?>, <?= json_encode($t['text'], JSON_HEX_QUOT | JSON_HEX_TAG) ?>, '<?= $p ?>', '')">
          <span class="unsched-dot priority-dot-<?= $p ?>"></span>
          <span class="unsched-text"><?= htmlspecialchars($t['text']) ?></span>
          <?php if ($t['done']): ?><span class="unsched-done-badge">完了</span><?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
