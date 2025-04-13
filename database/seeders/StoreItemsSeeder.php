<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StoreItem;
use Illuminate\Support\Str;

class StoreItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Avatares
        $this->createAvatarItems();
        
        // Insignias
        $this->createBadgeItems();
        
        // Rangos
        $this->createRankItems();
        
        // Temas
        $this->createSkinItems();
        
        // Especiales
        $this->createSpecialItems();
    }
    
    /**
     * Crear items de tipo Avatar
     */
    private function createAvatarItems(): void
    {
        $avatarItems = [
            [
                'name' => 'Gafas Digitales',
                'description' => 'Gafas futuristas para tu avatar. Perfectas para la programación intensiva.',
                'category' => 'avatar',
                'type' => 'accessory',
                'price' => 50,
                'level_required' => 1,
                'is_limited' => false,
                'image_path' => 'assets/images/avatar-components/accessories/cartoon_glasses.svg'
            ],
            [
                'name' => 'Pelo Neón',
                'description' => 'Estilo de pelo brillante que cambia de color. Destaca en la comunidad con este look único.',
                'category' => 'avatar',
                'type' => 'appearance',
                'price' => 100,
                'level_required' => 2,
                'is_limited' => false,
            ],
            [
                'name' => 'Auriculares Gaming',
                'description' => 'Auriculares RGB para tu avatar. ¡Escucha el código en alta definición!',
                'category' => 'avatar',
                'type' => 'accessory',
                'price' => 75,
                'level_required' => 1,
                'is_limited' => false,
                'image_path' => 'assets/images/avatar-components/accessories/headphones.svg'
            ],
            [
                'name' => 'Corona de Código',
                'description' => 'Una corona brillante que muestra tu estatus como realeza de la programación.',
                'category' => 'avatar',
                'type' => 'accessory',
                'price' => 150,
                'level_required' => 3,
                'is_limited' => true,
                'image_path' => 'assets/images/avatar-components/accessories/crown.svg'
            ],
            [
                'name' => 'Gafas Pixeladas',
                'description' => 'Gafas con estilo retro pixelado para los amantes de lo vintage.',
                'category' => 'avatar',
                'type' => 'accessory',
                'price' => 50,
                'level_required' => 1,
                'is_limited' => false,
                'image_path' => 'assets/images/avatar-components/accessories/pixel_glasses.svg'
            ],
            [
                'name' => 'Pajarita Elegante',
                'description' => 'Una pajarita para darle un toque formal a tu avatar en ocasiones especiales.',
                'category' => 'avatar',
                'type' => 'accessory',
                'price' => 60,
                'level_required' => 1,
                'is_limited' => false,
                'image_path' => 'assets/images/avatar-components/accessories/bowtie.svg'
            ],
            [
                'name' => 'Mascarilla de Desarrollador',
                'description' => 'Protégete con estilo mientras programas con esta mascarilla.',
                'category' => 'avatar',
                'type' => 'accessory',
                'price' => 40,
                'level_required' => 1,
                'is_limited' => false,
                'image_path' => 'assets/images/avatar-components/accessories/facemask.svg'
            ],
            [
                'name' => 'Gorra de Programador',
                'description' => 'Una gorra moderna para protegerte mientras codificas bajo el sol.',
                'category' => 'avatar',
                'type' => 'accessory',
                'price' => 70,
                'level_required' => 1,
                'is_limited' => false,
                'image_path' => 'assets/images/avatar-components/accessories/cap.svg'
            ],
            [
                'name' => 'Bufanda de Código',
                'description' => 'Una bufanda elegante para mantenerte cálido durante largas sesiones de programación.',
                'category' => 'avatar',
                'type' => 'accessory',
                'price' => 55,
                'level_required' => 1,
                'is_limited' => false,
                'image_path' => 'assets/images/avatar-components/accessories/scarf.svg'
            ],
            [
                'name' => 'Collar de Desarrollador',
                'description' => 'Un collar con una gema que simboliza la joya de tu código.',
                'category' => 'avatar',
                'type' => 'accessory',
                'price' => 100,
                'level_required' => 2,
                'is_limited' => false,
                'image_path' => 'assets/images/avatar-components/accessories/necklace.svg'
            ],
            [
                'name' => 'Traje de Desarrollador',
                'description' => 'Elegante traje con estampados de código. Perfecto para entrevistas virtuales.',
                'category' => 'avatar',
                'type' => 'appearance',
                'price' => 150,
                'level_required' => 3,
                'is_limited' => false,
            ],
            [
                'name' => 'Capa de Hackathon',
                'description' => 'Capa especial que solo los campeones de hackathones pueden usar. ¡Demuestra tu valía!',
                'category' => 'avatar',
                'type' => 'accessory',
                'price' => 300,
                'level_required' => 5,
                'is_limited' => true,
                'stock' => 10,
            ],
        ];
        
        $this->createItems($avatarItems);
    }
    
    /**
     * Crear items de tipo Insignia
     */
    private function createBadgeItems(): void
    {
        $badgeItems = [
            [
                'name' => 'Programador Junior',
                'description' => 'Insignia que certifica tus habilidades básicas de programación.',
                'category' => 'badge',
                'type' => 'decoration',
                'price' => 100,
                'level_required' => 2,
                'is_limited' => false,
            ],
            [
                'name' => 'Experto en Python',
                'description' => 'Demuestra tu dominio del lenguaje Python con esta insignia especializada.',
                'category' => 'badge',
                'type' => 'decoration',
                'price' => 200,
                'level_required' => 3,
                'is_limited' => false,
            ],
            [
                'name' => 'Maestro de Desafíos',
                'description' => 'Solo otorgada a quienes han completado más de 50 desafíos. Una marca de perseverancia.',
                'category' => 'badge',
                'type' => 'decoration',
                'price' => 300,
                'level_required' => 4,
                'is_limited' => false,
            ],
            [
                'name' => 'Campeón de Leaderboard',
                'description' => 'Ocupaste el primer lugar en un leaderboard mensual. ¡Un logro para presumir!',
                'category' => 'badge',
                'type' => 'decoration',
                'price' => 500,
                'level_required' => 5,
                'is_limited' => true,
                'stock' => 5,
            ],
            [
                'name' => 'Innovador Mitaí',
                'description' => 'Reservada para los estudiantes más creativos que proponen soluciones originales.',
                'category' => 'badge',
                'type' => 'decoration',
                'price' => 400,
                'level_required' => 4,
                'is_limited' => true,
                'stock' => 20,
            ],
        ];
        
        $this->createItems($badgeItems);
    }
    
    /**
     * Crear items de tipo Rango
     */
    private function createRankItems(): void
    {
        $rankItems = [
            [
                'name' => 'Aprendiz de Código',
                'description' => 'El primer paso en tu viaje de programación. Todos empezamos aquí.',
                'category' => 'rank',
                'type' => 'title',
                'price' => 50,
                'level_required' => 1,
                'is_limited' => false,
            ],
            [
                'name' => 'Desarrollador Junior',
                'description' => 'Ya dominas los conceptos básicos y estás listo para retos mayores.',
                'category' => 'rank',
                'type' => 'title',
                'price' => 150,
                'level_required' => 3,
                'is_limited' => false,
            ],
            [
                'name' => 'Programador Experimentado',
                'description' => 'Tu conocimiento y experiencia comienzan a destacar en la comunidad.',
                'category' => 'rank',
                'type' => 'title',
                'price' => 300,
                'level_required' => 5,
                'is_limited' => false,
            ],
            [
                'name' => 'Maestro del Código',
                'description' => 'Un nivel reservado para quienes han dominado múltiples aspectos de la programación.',
                'category' => 'rank',
                'type' => 'title',
                'price' => 500,
                'level_required' => 7,
                'is_limited' => false,
            ],
            [
                'name' => 'Leyenda Mitaí',
                'description' => 'El rango más alto. Solo los verdaderos maestros pueden ostentar este título.',
                'category' => 'rank',
                'type' => 'title',
                'price' => 1000,
                'level_required' => 10,
                'is_limited' => true,
                'stock' => 3,
            ],
        ];
        
        $this->createItems($rankItems);
    }
    
    /**
     * Crear items de tipo Skin
     */
    private function createSkinItems(): void
    {
        $skinItems = [
            [
                'name' => 'Tema Oscuro',
                'description' => 'Cambia la apariencia de tu perfil a un elegante modo oscuro. Cuida tus ojos durante las sesiones nocturnas.',
                'category' => 'skin',
                'type' => 'appearance',
                'price' => 100,
                'level_required' => 1,
                'is_limited' => false,
            ],
            [
                'name' => 'Tema Neón',
                'description' => 'Dale a tu perfil un aspecto futurista con colores neón vibrantes.',
                'category' => 'skin',
                'type' => 'appearance',
                'price' => 150,
                'level_required' => 2,
                'is_limited' => false,
            ],
            [
                'name' => 'Tema Retro',
                'description' => 'Viaja al pasado con este skin inspirado en los 8-bits y la nostalgia de los primeros ordenadores.',
                'category' => 'skin',
                'type' => 'appearance',
                'price' => 150,
                'level_required' => 2,
                'is_limited' => false,
            ],
            [
                'name' => 'Tema Minimalista',
                'description' => 'Menos es más. Este tema reduce la interfaz a lo esencial para una máxima concentración.',
                'category' => 'skin',
                'type' => 'appearance',
                'price' => 200,
                'level_required' => 3,
                'is_limited' => false,
            ],
            [
                'name' => 'Tema Holográfico',
                'description' => 'Efectos holográficos premium que hacen que tu perfil destaque con cambios de color según el ángulo.',
                'category' => 'skin',
                'type' => 'appearance',
                'price' => 350,
                'level_required' => 5,
                'is_limited' => true,
                'stock' => 15,
            ],
        ];
        
        $this->createItems($skinItems);
    }
    
    /**
     * Crear items especiales
     */
    private function createSpecialItems(): void
    {
        $specialItems = [
            [
                'name' => 'Pista de Desafío',
                'description' => 'Desbloquea una pista para cualquier desafío que estés enfrentando. ¡Usa con sabiduría!',
                'category' => 'special',
                'type' => 'boost',
                'price' => 75,
                'level_required' => 1,
                'is_limited' => false,
                'effects' => json_encode(['type' => 'hint', 'uses' => 1]),
            ],
            [
                'name' => 'Multiplicador XP x2',
                'description' => 'Duplica los puntos XP obtenidos durante 24 horas. ¡Aprovecha para avanzar rápido!',
                'category' => 'special',
                'type' => 'boost',
                'price' => 200,
                'level_required' => 3,
                'is_limited' => false,
                'effects' => json_encode(['type' => 'xp_multiplier', 'factor' => 2, 'duration' => '24h']),
            ],
            [
                'name' => 'Revivir Racha',
                'description' => 'Restaura tu racha de días consecutivos si la perdiste. ¡Segunda oportunidad!',
                'category' => 'special',
                'type' => 'boost',
                'price' => 150,
                'level_required' => 2,
                'is_limited' => false,
                'effects' => json_encode(['type' => 'streak_restore', 'uses' => 1]),
            ],
            [
                'name' => 'Desafío Exclusivo',
                'description' => 'Desbloquea un desafío especial no disponible de otra manera. Recompensas únicas.',
                'category' => 'special',
                'type' => 'boost',
                'price' => 300,
                'level_required' => 4,
                'is_limited' => true,
                'stock' => 10,
                'effects' => json_encode(['type' => 'exclusive_challenge', 'challenge_id' => 'special_001']),
            ],
            [
                'name' => 'Cambio de Nombre',
                'description' => 'Cambia tu nombre de usuario una vez. ¡Renueva tu identidad!',
                'category' => 'special',
                'type' => 'boost',
                'price' => 400,
                'level_required' => 5,
                'is_limited' => false,
                'effects' => json_encode(['type' => 'username_change', 'uses' => 1]),
            ],
        ];
        
        $this->createItems($specialItems);
    }
    
    /**
     * Método auxiliar para crear items a partir de un array
     */
    private function createItems(array $items): void
    {
        foreach ($items as $item) {
            // Asegurarse de que tiene un slug único
            if (!isset($item['slug'])) {
                $item['slug'] = Str::slug($item['name']);
            }
            
            // Asignar valores por defecto si no existen
            $item['is_active'] = $item['is_active'] ?? true;
            
            // Verificar si el item ya existe
            if (!StoreItem::where('slug', $item['slug'])->exists()) {
                // Crear el item solo si no existe
                StoreItem::create($item);
            }
        }
    }
}
