# CHANGELOG — sylebel/aimod

## **1.0.2 — 2025-12-08**
### **Bug Fixes**
- Fixed missing ACP language key `AIMOD_ACP_SETTINGS` that caused the literal key to appear in the ACP left menu.
- Updated French and English `info_acp_aimod.php` files to include the missing label and ensure proper translation.
- Ensured ACP module labels render correctly in both languages.

### **Stability Improvements**
- Harmonized ACP language keys (`ACP_AIMOD_*` and `AIMOD_ACP_*`) across EN/FR files.
- Normalized structure of ACP language files to avoid fallback to raw keys.
  
### **Versioning**
- Updated `composer.json` to version **1.0.2**.

---

## **1.0.1 — 2025-12-07**
### **Major Fixes**
- Replaced `add_lang_ext()` with proper `core.user_setup` event injection to prevent phpBB from falling back to English globally.
- Completed FR/EN language files by adding missing keys:
  - `AIMOD_BLOCK_DETAIL_SENSITIVE`
  - `AIMOD_CATEGORY_UNKNOWN`
  - `AIMOD_BLOCK_SPAN`
  - `AIMOD_BLOCK_FOOTER`
- Cleaned ACP template: removed duplicate off-topic checkbox and added dedicated textarea for off-topic moderation prompt.

### **New Features**
- Added default off-topic moderation prompt.
- Improved “Reset to Default”: restores main prompt, off-topic prompt, model (`gpt-4.1-mini`), and all config_text fields.

### **Stability Improvements**
- Improved prompt construction for both moderation passes.
- Normalized language keys across ACP and frontend templates.
- Improved ACP form handling and validation.
- Cleaned internal events for predictable moderation behavior.

---

## **1.0.0 — Initial Release**
- Two-pass AI moderation (sensitive content + optional off-topic detection).
- Automatic block or moderation queue handling.
- ACP configuration panel: API key, model, threshold, prompt editors.
- JSON response with text span extraction and human-readable explanations.

