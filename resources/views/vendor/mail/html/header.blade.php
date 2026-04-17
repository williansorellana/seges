@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            <div style="margin-top: 10px; font-weight: bold; font-size: 18px;">
                {{ config('app.name') }}
            </div>
        </a>
    </td>
</tr>