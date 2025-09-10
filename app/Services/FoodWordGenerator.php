<?php

namespace App\Services;

use App\Models\Order;
use Carbon\Carbon;

class FoodWordGenerator
{
    private array $words = [
        'abricot', 'agneau', 'amande', 'ananas', 'anchois', 'artichaut', 'asperge', 'aubergine', 'avocat', 
        'bagel', 'baie', 'banane', 'bar', 'basilic', 'beignet', 'bettrave', 'beurre', 'blette', 'boeuf', 
        'boisson', 'bourguignon', 'brie', 'brioche', 'brochette', 'brocoli', 'brownie', 'bulgur', 'burger', 
        'cabillaud', 'calamar', 'calisson', 'camembert', 'canard', 'cannelle', 'caponata', 'carotte', 
        'cassoulet', 'caviar', 'cerfeuil', 'cerise', 'champignon', 'chili', 'chips', 'chocolat', 'chou', 
        'choux', 'churros', 'ciboulette', 'citron', 'citronnelle', 'clafoutis', 'colin', 'confiture', 'cookie', 
        'coriandre', 'courge', 'courgette', 'couscous', 'couteau', 'crabe', 'cranberry', 'crepe', 'cresson', 
        'crevette', 'croissant', 'crumble', 'curry', 'datte', 'dorade', 'edamame', 'epinard', 'estragon', 
        'falafel', 'farine', 'fenouil', 'feta', 'figue', 'flan', 'foie', 'fondue', 'fourchette', 'fraise', 
        'framboise', 'frites', 'fromage', 'galette', 'gateau', 'gaufre', 'gingembre', 'gnocchi', 'gombo', 
        'gouda', 'gratin', 'grenade', 'groseille', 'gruyere', 'guacamole', 'gyoza', 'haricot', 'haricots', 
        'homard', 'hotdog', 'houmous', 'huile', 'huitre', 'kaki', 'kebab', 'ketchup', 'kiwi', 'laitue', 'lapin', 
        'lasagne', 'lentille', 'macaron', 'mais', 'mangue', 'marmelade', 'mayonnaise', 'melon', 'menthe', 
        'merlu', 'miel', 'moelleux', 'morue', 'moule', 'moussaka', 'mousse', 'moutarde', 'mozzarella', 
        'myrtille', 'navet', 'noisette', 'noix', 'noodle', 'nugget', 'oignon', 'olive', 'omelette', 'orange', 
        'paella', 'pain', 'pamplemousse', 'pancake', 'paprika', 'parmesan', 'pate', 'persil', 'pesto', 'piment', 
        'pistache', 'pistou', 'pizza', 'poele', 'poire', 'pois', 'poisson', 'poivre', 'poivron', 'polenta', 
        'pomme', 'porc', 'poulet', 'prune', 'pudding', 'quiche', 'quinoa', 'raclette', 'radis', 'raisin', 
        'ratatouille', 'riz', 'romarin', 'roquette', 'rouget', 'rumsteck', 'safran', 'salade', 'sandwich', 
        'sardine', 'saucisse', 'saucisson', 'saumon', 'sel', 'sesame', 'sole', 'soupe', 'spatule', 'steak', 
        'sushi', 'taco', 'tajine', 'tandoori', 'tartare', 'tarte', 'tartiflette', 'tartine', 'thon', 'thym', 
        'tilapia', 'tiramisu', 'tofu', 'tomate', 'truffe', 'truite', 'vanille', 'veloute', 'vinaigre', 'vodka', 
        'wasabi', 'wrap', 'yakitori', 'yaourt', 'yuzu'
    ];

    public function create(int $count = 3): string
    {
        if ($count > count($this->words)) {
            throw new \InvalidArgumentException("Le nombre de mots demandé est supérieur à la quantité de mots disponibles.");
        }

        do {
            $randomWords = array_rand(array_flip($this->words), $count);
            $slug = implode('-', $randomWords);
            $exists = Order::where('slug', $slug)
                ->whereDate('created_at', Carbon::today())
                ->exists();
        } while ($exists);

        return $slug;
    }
}