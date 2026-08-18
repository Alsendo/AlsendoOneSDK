# Security Policy

## Reporting a vulnerability

Please do **not** report security vulnerabilities through public GitHub issues.

Report them privately via
[GitHub Security Advisories](https://github.com/Alsendo/AlsendoOneSDK/security/advisories/new)
for this repository. You will receive a response as soon as possible, and we
will keep you informed about the progress of the fix.

## Scope notes

- This SDK signs every request with HMAC-SHA256 using your `app_secret`.
  Treat the secret like a password — do not commit it to version control and
  do not log it. The SDK itself never logs credentials or signed payloads.
- The SDK communicates exclusively with `apaczka.pl` hosts over HTTPS.
