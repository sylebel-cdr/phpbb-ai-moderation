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

## 💲 Estimated Cost of Use (with full calculation)

The extension itself is free.  
Your only cost comes from the OpenAI API.

It can operate in two modes:

- **Base mode** → 1 AI request per message (safety moderation)  
- **Extended mode** → 2 AI requests per message (safety + off-topic)

Below is an exact breakdown using the real OpenAI pricing for  
**gpt-4.1-mini** (January 2025):

- **$0.03 per 1,000 input tokens**  
- **$0.06 per 1,000 output tokens**

Typical phpBB messages are short: **300–500 characters**,  
which is **≈ 60–100 tokens** total (input + output).

---

### 📘 1) Cost *per message*

#### 🔹 Cost per pass (1 AI call)

Example for a message of **80 tokens total** (input + output):

80 tokens × $0.03 / 1,000 tokens = $0.0024

pgsql
Copier le code

So **one pass costs ≈ $0.0024** (about a quarter of a cent).

#### 🔹 1-pass mode (safety only)

cost_per_message_1pass ≈ $0.0024

shell
Copier le code

#### 🔹 2-pass mode (safety + off-topic)

cost_per_message_2pass = 2 × $0.0024 = $0.0048

---

### 📗 2) Cost for 1,000 / 10,000 / 100,000 messages

Using the cost per message above:

| Messages per month | Cost (1 pass)     | Cost (2 passes)   |
|--------------------|-------------------|-------------------|
| 1,000              | 1,000 × $0.0024 = **$2.40** | 1,000 × $0.0048 = **$4.80** |
| 10,000             | **$24.00**        | **$48.00**        |
| 100,000            | **$240.00**       | **$480.00**       |

---

### 📙 Summary (practical interpretation)

- A **small forum** (1,000 msgs/month) costs  
  **$2–5 per month** depending on your settings.
- A **medium forum** (10,000 msgs/month) costs  
  **$20–50 per month**.
- A **large forum** (100,000 msgs/month) costs  
  **$200–500 per month**.

#### ✔ Costs remain low *unless you have high traffic*.  
#### ✔ Off-topic detection (2-pass) simply doubles the price.  
#### ✔ Anyone can recalculate using:  

(tokens_per_message / 1000) × 0.03 × number_of_passes × number_of_messages

---

> To update these estimates, simply plug in your own values  
> for tokens per message and the latest OpenAI pricing.

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
