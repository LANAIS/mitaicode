<?php

/**
 * Obtiene el nombre de la categoría a partir de su clave.
 *
 * @param string $category
 * @return string
 */
function getCategoryName($category)
{
    $categories = [
        'avatar' => 'Avatar',
        'badge' => 'Insignia',
        'rank' => 'Rango',
        'skin' => 'Tema',
        'special' => 'Especial'
    ];
    
    return $categories[$category] ?? $category;
}

/**
 * Obtiene la clase CSS para el badge de la categoría.
 *
 * @param string $category
 * @return string
 */
function getCategoryBadgeClass($category)
{
    $classes = [
        'avatar' => 'info',
        'badge' => 'success',
        'rank' => 'warning',
        'skin' => 'primary',
        'special' => 'danger'
    ];
    
    return $classes[$category] ?? 'secondary';
} 