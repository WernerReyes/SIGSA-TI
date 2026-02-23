<?php

namespace App\Mail;

use App\Enums\Asset\AssetStatus;
use App\Enums\Asset\AssetTypeCategory;
use App\Models\Asset;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccessoryOutOfStockMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct()
    {

    }

    private function getAccessories()
{
    return Asset::select('id', 'type_id')
        ->with('type:id,name')
        ->whereNot('status', AssetStatus::AVAILABLE) // solo los no disponibles
        ->whereHas('type', fn($q) => 
            $q->where('doc_category', AssetTypeCategory::ACCESSORY->value)
        )
        // excluir tipos que tengan al menos un disponible
        ->whereDoesntHave('type.assets', fn($q) => 
            $q->where('status', AssetStatus::AVAILABLE)
        )
        ->get()
        ->unique(fn($item) => strtolower($item->type->name));
}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Accesorios fuera de stock',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.accessory-out-of-stock',
            with: [
                'assets' => $this->getAccessories(),
            ],

        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
