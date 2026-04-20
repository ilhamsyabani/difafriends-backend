<tr>
<td>
<table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="content-cell" align="center">
    <p style="color: #a1a1aa; font-size: 12px; margin: 0 0 8px;">
        {{ Illuminate\Mail\Markdown::parse($slot) }}
    </p>
    <p style="color: #a1a1aa; font-size: 11px; margin: 0 0 4px;">
        &copy; {{ date('Y') }} DifaFriends &mdash; Platform Edukasi Anak Berkebutuhan Khusus
    </p>
    <p style="margin: 0;">
        <a href="{{ config('app.url') }}" style="color: #7B2D8B; font-size: 11px; text-decoration: none;">difafriends.com</a>
        &nbsp;&middot;&nbsp;
        <a href="{{ config('app.url') }}/settings/profile" style="color: #a1a1aa; font-size: 11px; text-decoration: none;">Pengaturan Akun</a>
    </p>
</td>
</tr>
</table>
</td>
</tr>
