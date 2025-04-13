<?php

namespace App\Console\Commands;

use App\Mail\TestEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email? : Dirección de correo a la cual enviar la prueba}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía un correo de prueba para verificar la configuración de correo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        if (!$email) {
            $email = $this->ask('¿A qué dirección de correo quieres enviar la prueba?');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Dirección de correo inválida: ' . $email);
            return 1;
        }
        
        $this->info('Enviando correo de prueba a: ' . $email);
        
        try {
            Mail::to($email)->send(new TestEmail());
            $this->info('¡Correo enviado correctamente!');
            
            $this->info('Por favor, revisa tu bandeja de entrada (y también la carpeta de spam) para confirmar que has recibido el correo.');
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error al enviar el correo: ' . $e->getMessage());
            
            // Mostrar información detallada para depuración
            $this->line('');
            $this->line('Detalles para depuración:');
            $this->line('-------------------------');
            $this->line('Configuración actual de correo:');
            $this->line('MAIL_MAILER: ' . config('mail.default'));
            $this->line('MAIL_HOST: ' . config('mail.mailers.smtp.host'));
            $this->line('MAIL_PORT: ' . config('mail.mailers.smtp.port'));
            $this->line('MAIL_USERNAME: ' . config('mail.mailers.smtp.username'));
            $this->line('MAIL_ENCRYPTION: ' . config('mail.mailers.smtp.encryption'));
            $this->line('MAIL_FROM_ADDRESS: ' . config('mail.from.address'));
            
            return 1;
        }
    }
}
