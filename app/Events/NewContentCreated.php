<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewContentCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * El título del contenido creado.
     *
     * @var string
     */
    public $contentTitle;

    /**
     * El tipo de contenido.
     *
     * @var string
     */
    public $contentType;

    /**
     * La URL del contenido.
     *
     * @var string
     */
    public $contentUrl;

    /**
     * Crear una nueva instancia del evento.
     */
    public function __construct(string $contentTitle, string $contentType, string $contentUrl)
    {
        $this->contentTitle = $contentTitle;
        $this->contentType = $contentType;
        $this->contentUrl = $contentUrl;
    }
} 