AI Moderation (OpenAI) for phpBB
=================================

Authors
-------
- Sylvain Lebel — original author (https://sylebel.net)
- ChatGPT (OpenAI GPT-5.1 Thinking) — co-author of the AI logic and integration helper

Summary
-------
This extension connects phpBB to OpenAI in order to perform **automatic moderation**
of messages *before* they are posted.  The AI can:

- block insults, harassment, hate and violent content;
- block praise or denial of genocidal / extremist regimes;
- block spam, fraud and commercial advertising unrelated to the forum;
- optionally, block messages that are clearly off-topic for the current section.

When a message is blocked, the author sees a detailed explanation and a short
excerpt of the problematic text so they can correct it and submit again.

Basic usage
-----------
1. Install and enable the extension (see INSTALL.txt).
2. In the ACP, go to the AI Moderation settings page.
3. Enter your OpenAI API key, choose a model (for example: `gpt-4.1-mini`),
   and set the detection threshold (0–1, typical values: 0.3–0.7).
4. Optionally enable off-topic moderation.
5. Post a few test messages to verify that blocking behaviour matches your policy.

Costs
-----
The cost depends on the chosen OpenAI model and on the amount of text analysed.
For example, with a lightweight model such as `gpt-4.1-mini`, the cost per
1,000 short messages is typically **very low** (fractions of a US dollar),
but you should always check current prices in your OpenAI account.

License
-------
This extension is published under the GPL-2.0 license.
