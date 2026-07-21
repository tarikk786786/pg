$f = "d:\Server\htdocs\PAY\auth\transactions.php"
$bytes = [System.IO.File]::ReadAllBytes($f)

# â‚¹ in UTF-8 is: C3 A2 C2 82 C2 B9 (double-encoded ₹)
# â€" in UTF-8 is: C3 A2 C2 80 C2 93 (double-encoded —)
# â†' in UTF-8 is: C3 A2 C2 86 C2 92 (double-encoded →)

# Read as Latin-1 to get raw bytes, then fix
$text = [System.Text.Encoding]::UTF8.GetString($bytes)

# Replace broken rupee symbol with actual ₹
$broken_rupee = [char]0x00E2, [char]0x201A, [char]0x00B9 -join ''
$broken_dash = [char]0x00E2, [char]0x20AC, [char]0x201C -join ''
$broken_arrow = [char]0x00E2, [char]0x2020, [char]0x2019 -join ''

$text = $text.Replace($broken_rupee, [char]0x20B9)
$text = $text.Replace($broken_dash, [char]0x2014)
$text = $text.Replace($broken_arrow, [char]0x2192)

[System.IO.File]::WriteAllText($f, $text, (New-Object System.Text.UTF8Encoding $false))
Write-Host "Fixed encoding in transactions.php"
