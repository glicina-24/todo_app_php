<div class="actions-row">
  <span class="section-label"><?= count($todos) ?> TASKS</span>
  <?php if ($done_count > 0): ?>
  <form method="POST">
    <input type="hidden" name="action" value="clear_done">
    <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
    <button class="clear-btn" type="submit">完了済みを削除 (<?= $done_count ?>)</button>
  </form>
  <?php endif; ?>
</div>

<?php if (empty($todos)): ?>
  <div class="empty">
    <span class="empty-icon">ZERO</span>
    <div class="empty-text">タスクがありません。上に入力してください。</div>
  </div>
<?php else: ?>
  <div class="todo-list">
    <?php foreach ($todos as $todo): ?>
      <?php
        $priority   = $todo['priority'] ?? 'medium';
        $is_overdue = !$todo['done'] && !empty($todo['due_date']) && $todo['due_date'] < date('Y-m-d');
      ?>
      <div class="todo-item priority-<?= $priority ?> <?= $todo['done'] ? 'is-done' : '' ?>">

        <form class="toggle-form" method="POST">
          <input type="hidden" name="action" value="toggle">
          <input type="hidden" name="id" value="<?= $todo['id'] ?>">
          <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
          <button class="check-btn <?= $todo['done'] ? 'checked' : '' ?>" type="submit" title="完了切替">
            <svg class="check-icon" viewBox="0 0 12 10">
              <polyline points="1,5 4.5,8.5 11,1"/>
            </svg>
          </button>
        </form>

        <div class="todo-content">
          <span class="todo-text"><?= htmlspecialchars($todo['text']) ?></span>
          <?php if (!empty($todo['due_date'])): ?>
            <span class="due-label <?= $is_overdue ? 'overdue' : '' ?>">
              期限: <?= htmlspecialchars($todo['due_date']) ?><?= $is_overdue ? ' — 期限超過' : '' ?>
            </span>
          <?php endif; ?>
        </div>

        <span class="todo-date"><?= $todo['created_at'] ?></span>

        <button class="edit-btn" type="button" title="編集"
          onclick="openEditModal(
            <?= $todo['id'] ?>,
            <?= json_encode($todo['text'], JSON_HEX_QUOT | JSON_HEX_TAG) ?>,
            '<?= htmlspecialchars($priority) ?>',
            '<?= htmlspecialchars($todo['due_date'] ?? '') ?>'
          )">
          <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M10 2l2 2-7 7H3v-2l7-7z"/>
          </svg>
        </button>

        <form class="delete-form" method="POST">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= $todo['id'] ?>">
          <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
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
