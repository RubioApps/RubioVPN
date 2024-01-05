# Security Policy

## Supported Versions

RubioVPN is released as a beta version 1.0.0 and it works for PHP8.x.
This will not work for previous versions of PHP

| Version | Supported          |
| ------- | ------------------ |
| 8.x     | :white_check_mark: |
| 7.x     | :white_check_mark: |
| < 7.x   | :x:                |

## Reporting a Vulnerability

RubioVPN uses a shell execution command to launch an external bash script that controlls the systemctl 

** This is extremely touchy **

To do so, RubioVPN needs the sudoers file to be modified to add a single execution of a single (named .exec_wrapper) which contains a simple bash script.

If you find any issue with this kind of interactions or other, please [Report a bug](https://github.com/RubioApps/RubioVPN/blob/main/.github/ISSUE_TEMPLATE/bug_report.md)

Thank you.
