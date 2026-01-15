@props(['url', 'text' => 'View Details'])

<table role="presentation" style="width: 100%; margin: 30px 0;">
    <tr>
        <td align="center">
            <a href="{{ $url }}" 
               style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 6px rgba(13, 148, 136, 0.3);">
                {{ $text }}
            </a>
        </td>
    </tr>
</table>
