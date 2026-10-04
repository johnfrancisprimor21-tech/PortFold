<style>
.jump { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 24px; }
.jump a { font-size: 13px; color: var(--muted); text-decoration: none; padding: 8px 14px; min-height: 36px; display: inline-flex; align-items: center; border: 1px solid var(--border); border-radius: 999px; background: var(--surface); }
.jump a:hover { color: #fff; border-color: var(--border-strong); }
.legend-note { font-size: 14px; color: var(--muted); margin-bottom: 20px; }

.photo-upload-wrap { display: flex; align-items: flex-start; gap: 20px; flex-wrap: wrap; }
.photo-preview-box { width: 110px; height: 110px; border-radius: 10px; border: 1px solid var(--border-strong); background: var(--surface-2); overflow: hidden; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: var(--muted); font-size: 13px; text-align: center; }
.photo-preview-box img, .shot-box img { width: 100%; height: 100%; object-fit: cover; display: block; }
.photo-upload-controls { flex: 1; min-width: 220px; }
.shot-box { width: 100%; max-width: 240px; aspect-ratio: 16/9; border-radius: 8px; border: 1px solid var(--border-strong); background: #111; overflow: hidden; display: flex; align-items: center; justify-content: center; color: var(--muted); font-size: 13px; margin-bottom: 8px; }
input[type="file"] { color: var(--muted); font: inherit; font-size: 14px; max-width: 100%; }
input[type="file"]::file-selector-button { min-height: 40px; padding: 8px 16px; margin-right: 12px; border-radius: 8px; border: 1px solid var(--border-strong); background: #222; color: #ddd; font: inherit; font-size: 14px; cursor: pointer; }
input[type="file"]::file-selector-button:hover { background: #2a2a2a; color: #fff; }

.repeatable-item { background: var(--surface-2); border: 1px solid var(--border); border-radius: 10px; padding: 20px 16px 4px; margin-bottom: 14px; position: relative; }
.remove-btn { position: absolute; top: 6px; right: 6px; width: 40px; height: 40px; background: none; border: 0; border-radius: 8px; color: var(--muted); cursor: pointer; font-size: 24px; line-height: 1; }
.remove-btn:hover { color: #fca5a5; background: rgba(239,68,68,.12); }
.repeatable-item .form-group:first-child, .repeatable-item > .form-row:first-of-type { padding-right: 36px; }
.add-btn { width: 100%; min-height: 44px; background: none; border: 1px dashed var(--border-strong); color: var(--muted); border-radius: 8px; font: inherit; font-size: 14px; cursor: pointer; transition: all .2s; }
.add-btn:hover { border-color: #666; color: #fff; background: var(--surface-2); }
.form-group.check { display: flex; align-items: center; gap: 10px; }
.form-group.check input { width: 20px; height: 20px; accent-color: var(--primary); }
.form-group.check label { margin: 0; cursor: pointer; font-size: 15px; }
.counter { text-align: right; font-size: 13px; color: var(--muted); margin-top: 6px; }
.counter.near { color: var(--warn); }

.action-bar { position: sticky; bottom: 0; z-index: 10; display: flex; gap: 12px; justify-content: flex-end; align-items: center; flex-wrap: wrap; padding: 14px 0; background: linear-gradient(180deg, rgba(13,13,13,0), var(--bg) 35%); }
.action-bar .hint { margin-right: auto; font-size: 13px; color: var(--muted); }
</style>
