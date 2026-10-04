.theme-toggle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-width: 44px;
    min-height: 44px;
    padding: 0 12px;
    border: 1px solid var(--theme-control-border);
    border-radius: 999px;
    background: var(--theme-control-bg);
    color: var(--ink);
    font: inherit;
    font-size: .74rem;
    font-weight: 600;
    line-height: 1;
    white-space: nowrap;
    cursor: pointer;
    transition: background-color .18s ease, border-color .18s ease, transform .18s ease;
}
.theme-toggle:hover { background: var(--theme-control-hover); transform: translateY(-1px); }
.theme-toggle[aria-pressed="true"] { border-color: var(--theme-control-active); }
.theme-toggle svg { width: 17px; height: 17px; flex: 0 0 17px; }
.theme-icon--sun { display: none; }
:root[data-theme="light"] .theme-icon--sun { display: block; }
:root[data-theme="light"] .theme-icon--moon { display: none; }
@media (max-width: 640px) {
    .theme-toggle { padding-inline: 10px; }
    .theme-toggle-label { display: none; }
}
@media (prefers-reduced-motion: reduce) {
    .theme-toggle { transition: none; }
    .theme-toggle:hover { transform: none; }
}
