<form class="add-form" method="POST">
  <input type="hidden" name="action" value="add">
  <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
  <div class="add-row">
    <input class="add-input" type="text" name="text" placeholder="新しいタスクを入力..." autocomplete="off" autofocus>
    <button class="add-btn" type="submit">ADD</button>
  </div>
  <div class="add-meta">
    <select name="priority" class="meta-select">
      <option value="medium">優先度: 中</option>
      <option value="high">優先度: 高</option>
      <option value="low">優先度: 低</option>
    </select>
    <input type="date" name="due_date" class="meta-date">
  </div>
</form>
