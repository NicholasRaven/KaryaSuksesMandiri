<x-mail::message>
@component('mail::message')
# Halo {{ $customerName }},

Ini adalah pengingat bahwa invoice Anda dengan nomor **{{ $invoice->invoice_number }}** akan segera jatuh tempo.

**Detail Invoice:**
* **Nomor Invoice:** {{ $invoice->invoice_number }}
* **Jumlah Total:** Rp {{ $total_amount }}
* **Tanggal Jatuh Tempo:** {{ $due_date }}

Mohon segera lakukan pembayaran untuk menghindari keterlambatan.

@component('mail::button', ['url' => route('payments.show', $invoice->id)])
Lihat Invoice Anda
@endcomponent

Terima kasih atas perhatian Anda.

Salam hormat,
Tim {{ config('app.name') }}
@endcomponent
</x-mail::message>
