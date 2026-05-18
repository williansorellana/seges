$content = Get-Content "resources/views/vehicles/index.blade.php" -Raw
$ifs = ([regex]'@if').Matches($content).Count
$elses = ([regex]'@else').Matches($content).Count
$endifs = ([regex]'@endif').Matches($content).Count
$foreachs = ([regex]'@foreach').Matches($content).Count
$endforeachs = ([regex]'@endforeach').Matches($content).Count
$phps = ([regex]'@php').Matches($content).Count
$endphps = ([regex]'@endphp').Matches($content).Count

Write-Host "IFs: $ifs"
Write-Host "ELSEs: $elses"
Write-Host "ENDIFs: $endifs"
Write-Host "FOREACHs: $foreachs"
Write-Host "ENDFOREACHs: $endforeachs"
Write-Host "PHPs: $phps"
Write-Host "ENDPHPs: $endphps"
