# phpbb-ai-moderation
**AI-powered moderation extension for phpBB (3.3.x). Automatically screens posts using OpenAI models and applies configurable moderation actions.**

phpbb-ai-moderation is a modern moderation engine for phpBB forums that uses OpenAI models to analyse user messages *before they are posted*.  
Instead of silently flagging content for moderators to review, this extension **stops the message at submission time**, explains the issue to the user, shows the problematic text span, and lets them correct the message immediately.

The extension is fully configurable and requires *no custom PHP patching* of phpBB.

---

## ✨ Features

### 🔍 **Real-time AI moderation**
- Evaluates both *subject* and *message body*
- Detects insults, harassment, hate speech, violence, sexual content, self-harm, extremism, spam, fraud, phishing, and more
- Extracts the smallest offending text span
- Provides a clear human-readable explanation (FR or EN)

### 🧭 **Optional off-topic moderation**
When enabled, the AI also receives:
- The forum section name  
- The hierarchical path of the section  

This allows the model to evaluate whether the message fits the topic of the section and block obvious off-topic posts.

### 🔧 **Fully configurable from the ACP**
You can set:
- The OpenAI model (default: `gpt-4.1-mini`)
- Detection threshold
- Custom moderation prompt (extended rules, languages, section-specific constraints)
- Option to enable/disable off-topic moderation
- Reset settings to safe defaults

No code editing required.

### 🌐 **Automatic multilingual support**
The extension automatically detects the user interface language (French or English) and produces:
- Explanations
- Warnings
- Error messages  
…in the correct language for the user.

### ⚡ **Non-blocking UI with spinner**
A small loading spinner appears while the AI is analysing the message, preventing double submissions and improving UX.

---

## 🧠 How Moderation Works

1. The extension captures the subject and message text
2. It sends them to the specified OpenAI model with:
   - A system prompt (built-in or admin-customized)
   - The forum section path (if off-topic moderation is enabled)
3. The model returns a strict JSON structure containing:
   - `decision: allow | block`
   - `category`
   - `severity` (0–1)
   - `offending_span`
   - `explanation`
4. If blocked, the message is *not sent* and the user receives:
   - A moderation notice
   - The explanation
   - The text excerpt that caused the block
   - Instructions to correct their message and resubmit

Moderation happens **before database insert**, keeping your forum clean.

---

## 💲 Estimated Cost of Use

The extension is extremely inexpensive to run.

### Example with default model: `gpt-4.1-mini`
Average message size: **300–500 characters**  
OpenAI cost (input + output): **≈ $0.003 per 1,000 messages**

| Messages / month | Approx. cost |
|------------------|--------------|
| 1,000            | ~$0.003      |
| 10,000           | ~$0.03       |
| 100,000          | ~$0.30       |

Even a large forum typically stays under **$1 USD per month**.

Costs increase only if you choose more advanced models (e.g. `gpt-4.1` or `gpt-o3`).

---

## 📦 Installation

1. Extract the extension into:  
   `ext/sylebel/aimod/`
2. In the ACP → Customize → Manage Extensions  
   activate **AI Moderation**
3. Go to ACP → Extensions → AI Moderation  
   configure:
   - API key  
   - Model  
   - Detection threshold  
   - Optional off-topic moderation  
   - Custom rules (optional)

A full installation guide is available in:
- `INSTALL.txt` (English)
- `INSTALL-FR.txt` (Français)

---

## 👥 Authors

- **Sylvain Lebel (sylebel)** — Project author  
- **ChatGPT (OpenAI GPT-5.1)** — Code generation assistance, documentation, and system design support  

---

## 📜 License

Released under the MIT License.

---

## 🧩 Compatibility

- phpBB **3.3.x**
- PHP **7.4–8.2**
- Works with all phpBB themes (no template patching required)

---

## 🤝 Contributions

Bug reports, feature suggestions and pull requests are welcome!  
See `CONTRIBUTING.md` for guidelines.

## Credits

Developed by **Sylvain Lebel** and **OpenAI ChatGPT**, 2025.  
Designed for the Consciousness of the Real community forum and released for the broader phpBB ecosystem.
