# Wiki Source

This folder contains wiki-compatible pages used to keep documentation split across focused topics.

## Publishing to GitHub Wiki

GitHub Wiki content lives in a separate repository (`<repo>.wiki.git`).

### Prerequisite

Enable Wiki in the repository settings first. If Wiki is disabled, cloning `*.wiki.git` returns `Repository not found`.

### Bash

```bash
git clone https://github.com/RubenJ01/php-humanize.wiki.git
cp docs/wiki/*.md php-humanize.wiki/
cd php-humanize.wiki
git add .
git commit -m "docs: update wiki pages"
git push
```

### PowerShell

```powershell
git clone "https://github.com/RubenJ01/php-humanize.wiki.git"
Copy-Item "docs/wiki/*.md" -Destination "php-humanize.wiki" -Force
Set-Location "php-humanize.wiki"
git add .
git commit -m "docs: update wiki pages"
git push
```

