# Security cleanup

This repository was cleaned after malicious and credential-harvesting components were found in the imported archive.

Removed components include:

- DragonForce and Cylul webshells, including PHP payloads disguised as image files
- Obfuscated remote file managers and upload/command-execution backdoors
- Banking and payment-app login, PIN, OTP, session-token, and refresh-token handlers
- Telegram bot integration and automated payment-session cron jobs
- Payment pages and payout modules coupled to the unsafe credential workflows
- Database dumps containing payment-token-oriented schemas or records

Do not deploy an older commit or the original archive. Rotate all hosting, database, payment-provider, and API credentials used by any prior deployment.
