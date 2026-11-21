<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Services;

class CategoryIconSuggester
{
    /**
     * Mapeo de términos gastronómicos y comerciales a sus emojis recomendados.
     *
     * @var array<string, list<string>>
     */
    private const KEYWORD_MAP = [
        // Hamburguesas y afines
        'hamburguesa' => ['🍔', '🥩', '🥓', '🍟', '🔥'],
        'burger' => ['🍔', '🥩', '🥓', '🍟', '🔥'],
        'burgers' => ['🍔', '🥩', '🥓', '🍟', '🔥'],
        'smash' => ['🍔', '🥩', '🔥', '🍟'],
        'sandw' => ['🥪', '🥖', '🧀'],
        'pepito' => ['🥪', '🥖', '🥩', '🧀'],
        'choripan' => ['🌭', '🥖', '🥩', '🔥'],

        // Pizzas y pastas
        'pizza' => ['🍕', '🧀', '🍅', '🔥'],
        'pizzas' => ['🍕', '🧀', '🍅', '🔥'],
        'pasta' => ['🍝', '🧀', '🍅', '🥖'],
        'pastas' => ['🍝', '🧀', '🍅', '🥖'],
        'lasagna' => ['🍝', '🧀', '🍅'],
        'calzone' => ['🍕', '🧀', '🥖'],

        // Bebidas
        'bebida' => ['🥤', '🧃', '🧊', '💧'],
        'bebidas' => ['🥤', '🧃', '🧊', '💧'],
        'refresco' => ['🥤', '🧊'],
        'gaseosa' => ['🥤', '🧊'],
        'jugo' => ['🧃', '🥤', '🍊', '🍓'],
        'jugos' => ['🧃', '🥤', '🍊', '🍓'],
        'malta' => ['🥤', '🍺'],
        'cerveza' => ['🍺', '🍻', '🧊'],
        'trago' => ['🍹', '🍸', '🥃'],
        'coctel' => ['🍹', '🍸', '🍍'],
        'cafe' => ['☕', '🥐', '🧁'],
        'te' => ['🧋', '☕', '🍋'],
        'agua' => ['💧', '🧊', '🥤'],

        // Pollo y alitas
        'pollo' => ['🍗', '🐔', '🍟', '🔥'],
        'alita' => ['🍗', '🔥', '🌶️', '🍟'],
        'alitas' => ['🍗', '🔥', '🌶️', '🍟'],
        'crispy' => ['🍗', '🍟', '🔥'],
        'tender' => ['🍗', '🍟'],
        'nugget' => ['🍗', '🍟'],

        // Papas y snacks
        'papa' => ['🍟', '🧀', '🥓'],
        'papas' => ['🍟', '🧀', '🥓'],
        'frita' => ['🍟', '🍗', '🧀'],
        'fritas' => ['🍟', '🍗', '🧀'],
        'snack' => ['🍿', '🥨', '🧀', '🍟'],
        'tequeno' => ['🧀', '🥖', '🍟'],
        'tequenos' => ['🧀', '🥖', '🍟'],
        'nacho' => ['🧀', '🥑', '🌮'],
        'nachos' => ['🧀', '🥑', '🌮'],

        // Perros calientes
        'perro' => ['🌭', '🍟', '🧀'],
        'perros' => ['🌭', '🍟', '🧀'],
        'hotdog' => ['🌭', '🍟'],
        'salchicha' => ['🌭', '🥩'],

        // Tacos y comida mexicana
        'taco' => ['🌮', '🌯', '🥑', '🌶️'],
        'tacos' => ['🌮', '🌯', '🥑', '🌶️'],
        'burrito' => ['🌯', '🌮', '🥑', '🌶️'],
        'quesadilla' => ['🧀', '🌮', '🥑'],
        'mexican' => ['🌮', '🌯', '🥑', '🌶️'],

        // Ensaladas y saludable
        'ensalada' => ['🥗', '🥑', '🥦', '🥒'],
        'ensaladas' => ['🥗', '🥑', '🥦', '🥒'],
        'fit' => ['🥗', '🥑', '🥦', '🌱'],
        'saludable' => ['🥗', '🥑', '🥦', '🥕'],
        'bowl' => ['🥗', '🥑', '🍣', '🍚'],
        'verde' => ['🥗', '🥑', '🥦', '🥒'],

        // Postres y dulces
        'postre' => ['🍰', '🍦', '🍨', '🧁', '🍩'],
        'postres' => ['🍰', '🍦', '🍨', '🧁', '🍩'],
        'torta' => ['🍰', '🎂', '🍫'],
        'pastel' => ['🍰', '🧁', '🍓'],
        'helado' => ['🍦', '🍨', '🍧'],
        'helados' => ['🍦', '🍨', '🍧'],
        'dona' => ['🍩', '☕'],
        'donas' => ['🍩', '☕'],
        'dulce' => ['🍬', '🍫', '🧁'],
        'chocolate' => ['🍫', '🍰', '🍪'],
        'galleta' => ['🍪', '🥛'],
        'cookies' => ['🍪', '🥛'],

        // Desayunos y panadería
        'desayuno' => ['🍳', '🥞', '🥓', '☕', '🥐'],
        'desayunos' => ['🍳', '🥞', '🥓', '☕', '🥐'],
        'arepa' => ['🫓', '🧀', '🥩', '🍳'],
        'arepas' => ['🫓', '🧀', '🥩', '🍳'],
        'empanada' => ['🥟', '🥟', '🧀', '🥩'],
        'empanadas' => ['🥟', '🥟', '🧀', '🥩'],
        'pancake' => ['🥞', '🍓', '☕'],
        'waffle' => ['🧇', '🍫', '🍓'],
        'pan' => ['🥖', '🥐', '🍞', '🥯'],
        'croissant' => ['🥐', '☕'],

        // Farmacia y salud
        'farmacia' => ['💊', '🩹', '🩺', '🧴', '🌡️'],
        'medicamento' => ['💊', '🩹', '🩺', '💉'],
        'medicamentos' => ['💊', '🩹', '🩺', '💉'],
        'medicina' => ['💊', '🩹', '🩺'],
        'pastilla' => ['💊', '💧'],
        'pastillas' => ['💊', '💧'],
        'jarabe' => ['💊', '🧴', '💧'],
        'salud' => ['💊', '🩹', '🩺', '❤️'],
        'cuidado' => ['🧴', '🧼', '🪥', '✨'],
        'higiene' => ['🧼', '🪥', '🧴', '🧻'],
        'jabon' => ['🧼', '🧴', '✨'],
        'shampoo' => ['🧴', '🧼', '✨'],
        'crema' => ['🧴', '✨'],
        'dental' => ['🦷', '🪥', '✨'],
        'vitamina' => ['💊', '🌿', '🍎'],
        'vitaminas' => ['💊', '🌿', '🍎'],
        'primeros auxilios' => ['🩹', '🩺', '🚑'],
        'curacion' => ['🩹', '🩺'],

        // Ferretería y herramientas
        'ferreteria' => ['🔧', '🔨', '🪛', '🔩', '💡'],
        'ferreterias' => ['🔧', '🔨', '🪛', '🔩', '💡'],
        'herramienta' => ['🔨', '🪛', '🔧', '🪚', '🧰'],
        'herramientas' => ['🔨', '🪛', '🔧', '🪚', '🧰'],
        'tornillo' => ['🔩', '⚙️', '🪛'],
        'tornillos' => ['🔩', '⚙️', '🪛'],
        'tuerca' => ['🔩', '⚙️'],
        'clavo' => ['🔨', '🔩'],
        'clavos' => ['🔨', '🔩'],
        'martillo' => ['🔨', '🧰'],
        'destornillador' => ['🪛', '🔨'],
        'llave' => ['🔧', '🔩'],
        'pintura' => ['🎨', '🖌️', '🧱'],
        'pinturas' => ['🎨', '🖌️', '🧱'],
        'brocha' => ['🎨', '🖌️'],
        'electricidad' => ['💡', '🔌', '⚡', '🔦'],
        'electrico' => ['💡', '🔌', '⚡'],
        'foco' => ['💡', '🔦', '⚡'],
        'bombillo' => ['💡', '⚡'],
        'iluminacion' => ['💡', '🔦', '⚡'],
        'cable' => ['🔌', '⚡', '💡'],
        'plomeria' => ['🔧', '🚰', '🗜️'],
        'tubo' => ['🔧', '🚰'],
        'tuberia' => ['🔧', '🚰'],
        'candado' => ['🔒', '🔑'],
        'cerradura' => ['🔒', '🔑'],
        'seguridad' => ['🔒', '🦺', '🔑'],
        'construccion' => ['🧱', '🔨', '🪜', '🦺'],

        // Sushi y asiática
        'sushi' => ['🍣', '🍱', '🥢', '🍤'],
        'roll' => ['🍣', '🥢', '🍱'],
        'rolls' => ['🍣', '🥢', '🍱'],
        'ramen' => ['🍜', '🥢', '🥚'],
        'arroz' => ['🍚', '🥢', '🥘'],
        'wok' => ['🥢', '🍜', '🍚'],
        'oriental' => ['🍣', '🍜', '🥢'],
        'china' => ['🥡', '🥢', '🥟', '🍜'],

        // Carnes y parrilla
        'carne' => ['🥩', '🍖', '🔥', '🔪'],
        'carnes' => ['🥩', '🍖', '🔥', '🔪'],
        'parrilla' => ['🥩', '🍖', '🔥', '🥓'],
        'asado' => ['🥩', '🍖', '🔥', '🍷'],
        'costilla' => ['🍖', '🥩', '🔥'],

        // Promociones y combos
        'combo' => ['⭐', '🔥', '🍔', '🥤', '🍟'],
        'combos' => ['⭐', '🔥', '🍔', '🥤', '🍟'],
        'promo' => ['🏷️', '🔥', '⭐', '⚡', '💥'],
        'promos' => ['🏷️', '🔥', '⭐', '⚡', '💥'],
        'oferta' => ['🏷️', '🔥', '💥', '⭐'],
        'nuevo' => ['✨', '🔥', '⭐'],
        'popular' => ['🔥', '⭐', '👑'],
        'favorito' => ['❤️', '⭐', '👑'],
        'infantil' => ['👶', '🍟', '🍔', '🧃', '🎈'],
        'ninos' => ['👶', '🍟', '🍔', '🧃', '🎈'],
    ];

    /**
     * Emojis organizados por categoría para la paleta completa.
     *
     * @var array<string, list<string>>
     */
    private const GROUPED_ICONS = [
        'Comida Rápida' => ['🍔', '🍟', '🌭', '🥪', '🍗', '🌮', '🌯', '🧀', '🥓', '🍖', '🍿', '🥨'],
        'Pizzas & Pastas' => ['🍕', '🍝', '🧀', '🍅', '🥖', '🥣', '🫒', '🧄', '🧅', '🌶️', '🍷'],
        'Bebidas & Cafés' => ['🥤', '🧃', '☕', '🧋', '🍺', '🍻', '🍹', '🍸', '🍷', '🧊', '💧', '🍾'],
        'Farmacia & Salud' => ['💊', '🩹', '🩺', '💉', '🧴', '🧼', '🦷', '🪥', '🌡️', '🧪', '🚑', '🌿', '🧻', '🧬', '😷', '❤️'],
        'Ferretería & Hogar' => ['🔨', '🪛', '🔧', '🪚', '🗜️', '🔩', '⚙️', '🪜', '🧰', '💡', '🔌', '🎨', '🧱', '🔒', '🔑', '📏', '🔦', '🧹', '🦺', '🚰'],
        'Platos & Carnes' => ['🥩', '🍖', '🍣', '🍜', '🍱', '🥟', '🍲', '🥘', '🍤', '🍚', '🥢'],
        'Postres & Dulces' => ['🍰', '🍦', '🍨', '🍩', '🍪', '🧁', '🍫', '🎂', '🥞', '🧇', '🍮', '🍬'],
        'Desayunos & Pan' => ['🥐', '🍞', '🥯', '🥖', '🍳', '🫓', '🥟', '🥪', '🥞', '🧇'],
        'Saludable & Frutas' => ['🥗', '🥑', '🥦', '🥒', '🥕', '🥬', '🌽', '🍅', '🍎', '🍓', '🍌', '🍊', '🍇'],
        'Promos & Especiales' => ['⭐', '🔥', '🏷️', '⚡', '👑', '💥', '✨', '🍽️', '❤️', '🎉', '🛒', '📦', '🥇'],
    ];

    /**
     * Sugiere hasta $limit íconos recomendados según el nombre de la categoría.
     *
     * @return list<string>
     */
    public static function suggest(string $name, int $limit = 6): array
    {
        $normalized = self::normalize($name);

        if ($normalized === '') {
            return ['⭐', '🔥', '🍽️', '🏷️', '✨', '👑'];
        }

        $suggested = [];

        // Buscar coincidencia en keywords
        foreach (self::KEYWORD_MAP as $keyword => $icons) {
            if (str_contains($normalized, $keyword)) {
                foreach ($icons as $icon) {
                    if (! in_array($icon, $suggested, true)) {
                        $suggested[] = $icon;
                    }
                }
            }
        }

        // Si no hubo coincidencia o faltan sugerencias, rellenar con íconos universales
        $defaults = ['⭐', '🔥', '🍽️', '🏷️', '✨', '👑', '🍔', '🍕', '🥤', '🍰'];
        foreach ($defaults as $default) {
            if (count($suggested) >= $limit) {
                break;
            }
            if (! in_array($default, $suggested, true)) {
                $suggested[] = $default;
            }
        }

        return array_slice($suggested, 0, $limit);
    }

    /**
     * Catálogo completo de íconos agrupados para el selector visual.
     *
     * @return array<string, list<string>>
     */
    public static function groupedIcons(): array
    {
        return self::GROUPED_ICONS;
    }

    /**
     * Devuelve el mapa completo de keywords para pasarlo a Alpine.js en cliente.
     *
     * @return array<string, list<string>>
     */
    public static function keywordMap(): array
    {
        return self::KEYWORD_MAP;
    }

    /**
     * Normaliza un texto removiendo acentos, caracteres especiales y convirtiendo a minúsculas.
     */
    public static function normalize(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');

        // Reemplazo de caracteres acentuados
        $unaccented = strtr($text, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'ü' => 'u', 'ñ' => 'n',
            'à' => 'a', 'è' => 'e', 'ì' => 'i', 'ò' => 'o', 'ù' => 'u',
        ]);

        return preg_replace('/[^a-z0-9 ]/u', ' ', $unaccented) ?? '';
    }
}
