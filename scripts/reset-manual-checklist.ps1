$ErrorActionPreference = 'Stop'

$repoRoot = Split-Path -Parent $PSScriptRoot
$checklistPath = Join-Path $repoRoot 'docs/manual-test-checklist.md'

if (-not (Test-Path -LiteralPath $checklistPath)) {
    throw "Manual checklist was not found at: $checklistPath"
}

$content = Get-Content -LiteralPath $checklistPath
$resetContent = $content -replace '^(\s*-\s*)\[[xX]\]', '$1[ ]'

Set-Content -LiteralPath $checklistPath -Value $resetContent -Encoding utf8

Write-Host "Unchecked all Markdown checklist items in docs/manual-test-checklist.md"
