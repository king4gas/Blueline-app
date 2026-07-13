<!DOCTYPE html>
<html>
<head>
    <title>Orderan Masuk Baru</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f5; padding: 20px; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <h2 style="color: #0891b2; margin-top: 0;">Halo Admin BlueLine,</h2>
        <p>Ada orderan baru yang masuk ke sistem. Berikut adalah rinciannya:</p>
        
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr>
                <td style="padding: 8px 0; font-weight: bold; border-bottom: 1px solid #e4e4e7;">No. Invoice</td>
                <td style="padding: 8px 0; border-bottom: 1px solid #e4e4e7; text-align: right;">{{ $order->invoice_number }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold; border-bottom: 1px solid #e4e4e7;">Total Pembayaran</td>
                <td style="padding: 8px 0; border-bottom: 1px solid #e4e4e7; text-align: right; color: #0891b2; font-weight: bold;">
                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold; border-bottom: 1px solid #e4e4e7;">Status Awal</td>
                <td style="padding: 8px 0; border-bottom: 1px solid #e4e4e7; text-align: right; text-transform: uppercase; color: #eab308;">
                    {{ str_replace('_', ' ', $order->status) }}
                </td>
            </tr>
        </table>

        <p>Silakan login ke halaman Admin Dashboard BlueLine untuk memeriksa kelengkapan data dan melakukan verifikasi pembayaran.</p>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="{{ url('/admin/orders') }}" style="background: #0891b2; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">
                Buka Manajemen Order
            </a>
        </div>
    </div>
</body>
</html>