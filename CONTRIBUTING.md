# Contributing to phpbb-ai-moderation

Thank you for your interest in contributing to **phpbb-ai-moderation**!  
This document explains the recommended workflow, coding rules, and guidelines for submitting issues and pull requests.

---

## 🚀 How You Can Contribute

### 1. Report Bugs
If you encounter any issue:
- Check existing issues first
- Open a new *Bug Report* using the provided template
- Include reproduction steps, PHP version, phpBB version and logs if possible

### 2. Request Features
Feature ideas are welcome!  
Use the *Feature Request* template and explain:
- What problem it solves
- Why it benefits users
- Example use cases

### 3. Submit Pull Requests
If you want to contribute code:

1. Fork the repository  
2. Create a new branch for your feature or fix  
3. Follow the coding standards below  
4. Submit a pull request using the PR template  
5. Ensure your changes do not break phpBB core compatibility

---

## 🧹 Coding Standards

### PHP
- Follow phpBB’s coding style (PSR-2–inspired)
- Use namespaces and avoid global logic
- Avoid modifying phpBB core files

### Templates (Twig)
- Keep templates minimal and portable across phpBB themes
- Do not hardcode style folder names (use `/styles/all/`)

### Language files
- All strings go into `language/en/` and `language/fr/`
- No hardcoded text inside PHP files

### AI Moderation Logic
- Keep the JSON output contract strict
- Avoid hardcoded moderation rules unless part of the default prompt
- Everything configurable should be in ACP
