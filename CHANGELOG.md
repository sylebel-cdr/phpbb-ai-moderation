# CHANGELOG — sylebel/aimod

## **1.0.1 — 2025-12-07**
### **Major Fixes**
- Critical fix for phpBB language loading: replaced `add_lang_ext()` with proper `core.user_setup` injection. Prevents language fallback to English.
- Completed FR/EN language files with missing keys:
  - AIMOD_BLOCK_DETAIL_SENSITIVE  
  - AIMOD_CATEGORY_UNKNOWN  
  - AIMOD_BLOCK_SPAN  
  - AIMOD_BLOCK_FOOTER
- Cleaned ACP template: removed duplicate off-topic checkbox, added second textarea.

### **New Features**
- Default off-topic moderation prompt added.
- Improved “Reset to Default”: restores main prompt, off-topic prompt, model (`gpt-4.1-mini`), and config_text fields.

### **Stability Improvements**
- Better prompt generation for both moderation passes.
- Normalized language keys across ACP and frontend.
- Improved ACP form handling.
- Cleaned internal events for predictable behavior.

---

## **1.0.0 — Initial Release**
- Two-pass AI moderation (sensitive content + optional off-topic).
- Automatic block or moderation queue.
- ACP configuration panel (API key, model, threshold, prompts).
