@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            <img src="{{ asset('images/dimak-logo.png') }}" alt="{{ config('app.name') }}"
                style="height: 60px; max-width: 200px;">
        </a>
    </td>
</tr>