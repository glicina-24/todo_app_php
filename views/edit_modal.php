<div id="edit-overlay" class="modal-overlay" style="display:none">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">タスクを編集</span>
      <button class="modal-close" type="button" onclick="closeEditModal()">&#x2715;</button>
    </div>
    <form method="POST">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="edit-id">
      <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
      <div class="modal-field">
        <label class="modal-label">タスク</label>
        <textarea class="modal-textarea" name="text" id="edit-text" rows="3" required></textarea>
      </div>
      <div class="modal-row">
        <div class="modal-field">
          <label class="modal-label">優先度</label>
          <select name="priority" id="edit-priority" class="modal-select">
            <option value="high">高</option>
            <option value="medium">中</option>
            <option value="low">低</option>
          </select>
        </div>
        <div class="modal-field">
          <label class="modal-label">期限日</label>
          <input type="date" name="due_date" id="edit-due" class="modal-date-input">
        </div>
      </div>
      <div class="modal-actions">
        <button type="button" class="modal-cancel" onclick="closeEditModal()">キャンセル</button>
        <button type="submit" class="modal-save">保存</button>
      </div>
    </form>
  </div>
</div>
<script>
function openEditModal(id, text, priority, dueDate) {
  document.getElementById('edit-id').value = id;
  document.getElementById('edit-text').value = text;
  document.getElementById('edit-priority').value = priority;
  document.getElementById('edit-due').value = dueDate;
  document.getElementById('edit-overlay').style.display = 'flex';
}
function closeEditModal() {
  document.getElementById('edit-overlay').style.display = 'none';
}
document.getElementById('edit-overlay').addEventListener('click', function(e) {
  if (e.target === this) closeEditModal();
});
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeEditModal();
});
</script>
