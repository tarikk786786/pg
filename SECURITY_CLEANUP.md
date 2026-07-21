# Security cleanup

This repository was cleaned after credential-harvesting and unsafe payment-session components were found in the imported archive.

Removed components include:

- Banking and payment-app login, PIN, OTP, session-token, and refresh-token handlers
- Payment-app impersonation and merchant-account connection flows
- Telegram bot integration and automated payment-session cron jobs
- Payment pages, payout callbacks, and gateway modules coupled to the unsafe workflows
- Scripts containing embedded third-party API credentials
- Database dumps containing payment-token-oriented schemas or records

Do not deploy an older commit or the original archive. Rotate all hosting, database, payment-provider, and API credentials used by any prior deployment.
