<?php

namespace Database\Seeders;

use App\Enums\ProductOrganicType;
use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Main
            [
                'name' => 'Hamburger',
                'description' => "Un classique tout simple, à prix doux. Idéal aussi pour les enfants.",
                'price' => 16_00,
                'price_points' => 500,
                'type' => 1,
                'is_homemade' => true,
                'image_path' => 'burger.jpg',
                'manufacturing_time' => 300,
                'profit_margin' => 30,
                'ingredients' => [
                    Ingredient::whereName('Pain')->first()->id,
                    Ingredient::whereName('Viande haché de boeuf')->first()->id,
                    Ingredient::whereName('Oignons caramélisés')->first()->id,
                    Ingredient::whereName('Cornichons (Concombres)')->first()->id,
                    Ingredient::whereName('Ketchup')->first()->id,
                ],
            ],
            [
                'name' => 'Cheese',
                'description' => "Délicieux mélange de fromages fondant, de bacon croustillant et d'oignons caramélisés.",
                'price' => 19_00,
                'price_points' => 850,
                'type' => 1,
                'is_homemade' => true,
                'image_path' => 'cheese.jpg',
                'manufacturing_time' => 300,
                'profit_margin' => 40,
                'ingredients' => [
                    Ingredient::whereName('Pain')->first()->id,
                    Ingredient::whereName('Viande haché de boeuf')->first()->id,
                    Ingredient::whereName('Oignons caramélisés')->first()->id,
                    Ingredient::whereName('Cornichons (Concombres)')->first()->id,
                    Ingredient::whereName('Ketchup')->first()->id,
                    Ingredient::whereName('Fromage fondant')->first()->id,
                    Ingredient::whereName('Beacon')->first()->id,
                ],
            ],
            [
                'name' => 'Cheese Brown',
                'description' => "Né dans la tête de notre jumeau américain, en hommage et par amour pour Chris Brown, ce burger marie notre sauce brune maison avec notre cheese. Un goût puissant et unique, introuvable ailleurs.",
                'price' => 23_00,
                'type' => 1,
                'is_homemade' => true,
                'image_path' => 'cheese.jpg',
                'manufacturing_time' => 300,
                'profit_margin' => 40,
            ],
            [
                'name' => 'Double Cheese',
                'description' => "Pour les grosses faims : deux fois plus de viande, deux fois plus de fromage fondant. Rien que ça.",
                'price' => 25_00,
                'type' => 1,
                'is_homemade' => true,
                'image_path' => 'double-cheese.jpg',
                'manufacturing_time' => 300,
                'profit_margin' => 40,
            ],
            [
                'name' => 'Raclette',
                'description' => "Raclette fondue des Alpes vaudoises. Pas valaisanne, non c'est vrai… mais franchement mon ami, tu t’en remettras quand t'aura gouté à c’te équipe.",
                'price' => 24_00,
                'type' => 1,
                'is_homemade' => true,
                'image_path' => 'cheese.jpg',
                'manufacturing_time' => 300,
                'profit_margin' => 40,
            ],
            [
                'name' => 'Brebis',
                'description' => "Avec son mélange sucré-salé, grâce au miel et au fromage de brebis. Simple, différent, et puissant en goût.",
                'price' => 26_00,
                'type' => 1,
                'is_homemade' => true,
                'image_path' => 'cheese.jpg',
                'manufacturing_time' => 300,
                'profit_margin' => 40,
            ],
            [
                'name' => 'Bleu',
                'description' => "Il a du coffre. Une odeur bien marquée, une pâte onctueuse, et un goût qui tient tête.",
                'price' => 26_00,
                'type' => 1,
                'is_homemade' => true,
                'image_path' => 'cheese.jpg',
                'manufacturing_time' => 300,
                'profit_margin' => 40,
            ],

            // Side
            [
                'name' => 'Frites',
                'description' => 'Des frites maisons double cuisson, au maximum à 170 degrée pour réduire la formation d’acrylamide, une substance cancérogène qui se forme lors de la cuisson à haute température.',
                'price' => 6_00,
                'price_points' => 150,
                'type' => 2,
                'is_homemade' => true,
                'image_path' => 'fries.jpg',
                'manufacturing_time' => 480,
                'profit_margin' => 50,
                'ingredients' => [
                    Ingredient::whereName('Sel')->first()->id,
                    Ingredient::whereName('Pomme de terre farineuse')->first()->id,
                ],
            ],
            [
                'name' => 'Nuggets',
                'description' => 'Filet de poulet enrobés d’une panure légère et extra croustillante, dorés à la perfection à l’extérieur et ultra moelleux à l’intérieur.',
                'price' => 6_00,
                'price_points' => 400,
                'type' => 2,
                'is_homemade' => true,
                'image_path' => 'tenders.jpg',
                'manufacturing_time' => 480,
                'profit_margin' => 50,
                'ingredients' => [
                    Ingredient::whereName('Poitrine de poulet')->first()->id,
                    Ingredient::whereName('Panure')->first()->id,
                    Ingredient::whereName('Sel')->first()->id,
                ],
            ],

            // Boissons
            [
                'name' => 'Sirop Nanah',
                'description' => 'Sirop de menthe artisanale du Pays-d’Enhaut, rafraîchie d’une touche de vrai citron de la côte Amalfitaine. Une boisson fait maison pleine de fraîcheur, sans arômes bidons ni poudre blanche magique.',
                'price' => 2_90,
                'price_points' => 150,
                'type' => 3,
                'is_homemade' => true,
                'container_quantity' => 40,
                'container_quantity_format' => 1,
                'image_path' => 'lemonade.jpg',
                'manufacturing_time' => 30,
                'profit_margin' => 75,
                'ingredients' => [
                    Ingredient::whereName('Sirop de Menthe Marocaine')->first()->id,
                    Ingredient::whereName('Jus de citron')->first()->id,
                ],
            ],
            [
                'name' => 'Sirop Thym',
                'description' => 'Sirop de Thym artisanale du Pays-d’Enhaut, rafraîchie d’une touche de vrai citron de la côte Amalfitaine. Une boisson pétillante fait maison pleine de fraîcheur, sans arômes bidons ni poudre blanche magique.',
                'price' => 2_90,
                'price_points' => 150,
                'type' => 3,
                'is_homemade' => true,
                'container_quantity' => 40,
                'container_quantity_format' => 1,
                'image_path' => 'lemonade.jpg',
                'manufacturing_time' => 30,
                'profit_margin' => 75,
                'ingredients' => [
                    Ingredient::whereName('Sirop de Thym')->first()->id,
                    Ingredient::whereName('Jus de citron')->first()->id,
                ],
            ],
            [
                'name' => 'Vivi Kola',
                'description' => 'Le cola suisse emblématique depuis 1938, au goût équilibré d’arômes naturels, noix de kola et vanille.',
                'price' => 3_90,
                'type' => 3,
                'is_homemade' => false,
                'organic_type' => null,
                'container_quantity' => 33,
                'container_quantity_format' => 1,
                'image_path' => 'vivikola.jpg',
                'manufacturing_time' => 30,
                'profit_margin' => 75,
            ],
            [
                'name' => 'Vivi Kola Zero',
                'description' => 'Le cola suisse emblématique depuis 1938, sans sucre, au goût équilibré d’arômes naturels, noix de kola et vanille.',
                'price' => 3_90,
                'type' => 3,
                'is_homemade' => false,
                'organic_type' => null,
                'container_quantity' => 33,
                'container_quantity_format' => 1,
                'image_path' => 'vivikola.jpg',
                'manufacturing_time' => 30,
                'profit_margin' => 75,
            ],
            [
                'name' => 'Ginger Beer',
                'description' => 'Une ginger beer bio, artisanale et fabriqué en suisse romande avec des ingrédients naturels.',
                'price' => 3_90,
                'type' => 3,
                'is_homemade' => false,
                'container_quantity' => 33,
                'container_quantity_format' => 1,
                'organic_type' => ProductOrganicType::EUROPEAN,
                'image_path' => 'GingerBeer.webp',
                'manufacturing_time' => 30,
                'profit_margin' => 75,
            ],
            [
                'name' => 'Limonade Orange artisanale',
                'description' => 'Pur jus d’orange sanguine et de citron jaune bio, fabriqué en suisse romande avec des ingrédients naturels.',
                'price' => 3_90,
                'type' => 3,
                'is_homemade' => false,
                'container_quantity' => 33,
                'container_quantity_format' => 1,
                'organic_type' => ProductOrganicType::SWISS,
                'image_path' => 'LesPetillantes_Orange.webp',
                'manufacturing_time' => 30,
                'profit_margin' => 75,
            ],
            [
                'name' => 'UNO mate caféiné',
                'description' => "Une boisson naturellement énérgisante à base de Yerba Maté pour t'accompagner dans les aventures du quotidien. Le parfait super kick gourmand et naturel.",
                'price' => 3_90,
                'type' => 3,
                'is_homemade' => false,
                'container_quantity' => 33,
                'container_quantity_format' => 1,
                'organic_type' => ProductOrganicType::EUROPEAN,
                'image_path' => 'UnoMate.webp',
                'manufacturing_time' => 30,
                'profit_margin' => 75,
            ],

            // Deserts
            [
                'name' => 'Cookie chocolat',
                'description' => 'Cookies chocolat fait maison, croustillants sur les bords et fondants au centre.',
                'price' => 6_00,
                'price_points' => 300,
                'type' => 4,
                'is_homemade' => true,
                'image_path' => 'Cookie.jpg',
                'manufacturing_time' => 30,
                'profit_margin' => 40,
            ],

            [
                'name' => 'Pancake',
                'description' => 'Pancakes maison, moelleux et dorés à souhait, préparés sur place comme il se doit. Ni poudre bizarre, ni mélange tout prêt — juste de vrais pancakes faits comme à la maison.',
                'price' => 6_00,
                'price_points' => 300,
                'type' => 4,
                'is_homemade' => true,
                'image_path' => 'Cookie.jpg',
                'manufacturing_time' => 180,
                'profit_margin' => 50,
            ],
            [
                'name' => 'Meringue & Crème double',
                'description' => 'Meringue croustillante accompagnée de crème double du Pays-d’Enhaut, épaisse et généreuse. Une spécialité régionale pleine de douceur, simple et irrésistible.',
                'price' => 9_90,
                'type' => 4,
                'is_homemade' => false,
                'image_path' => 'meringue-creme.jpg',
                'manufacturing_time' => 180,
                'profit_margin' => 30,
                'ingredients' => [
                    Ingredient::whereName('Meringue')->first()->id,
                    Ingredient::whereName('Crème double')->first()->id,
                ],
            ],
            [
                'name' => 'Glace vanille',
                'description' => 'Fabriquées artisanalement à Crissier par Kalan.',
                'price' => 5_50,
                'type' => 4,
                'is_homemade' => false,
                'organic_type' => ProductOrganicType::BUD,
                'image_path' => 'glace-kalan-vanille.jpg',
                'manufacturing_time' => 30,
                'profit_margin' => 30,
            ],

            // Sauces
            [
                'name' => 'Ketchup',
                'description' => 'Le ketchup Heinz, c’est notre petit péché, on a honte et on l’avoue ! Comme le cola, il doit être là. On n’a pas encore trouvé mieux, mais on y travaille activement.',
                'price' => 2_00,
                'price_points' => 100,
                'type' => 5,
                'is_homemade' => false,
                'organic_type' => ProductOrganicType::EUROPEAN,
                'image_path' => 'sauce-ketchup.jpg',
                'manufacturing_time' => 10,
                'profit_margin' => 30,
            ],
            [
                'name' => 'Mayonnaise',
                'description' => "Fabriqué base de jaune d'oeuf",
                'price' => 2_00,
                'price_points' => 100,
                'type' => 5,
                'is_homemade' => true,
                'image_path' => 'sauce-mayonnaise.jpg',
                'manufacturing_time' => 10,
                'profit_margin' => 30,
                'ingredients' => [
                    Ingredient::whereName("Jaune d'oeuf pasteurisé")->first()->id,
                    Ingredient::whereName('Vinaigre')->first()->id,
                    Ingredient::whereName('Huile de tournesol High Oleic')->first()->id,
                ],
            ],
            [
                'name' => 'Moutarde Miel',
                'description' => "Selon nous, le mélange parfait pour les tenders et le burger au chevre. De la moutarde maison, faites à partir de graine de moutarde suisse et bio. Et un miel issue de nos montage.",
                'price' => 2_00,
                'price_points' => 100,
                'type' => 5,
                'is_homemade' => true,
                'image_path' => 'sauce-mustard.jpg',
                'manufacturing_time' => 10,
                'profit_margin' => 30,
            ],
        ];


        foreach ($products as $data) {
            $ingredientData = $data['ingredients'] ?? [];
            unset($data['ingredients']);

            $product = Product::create($data);

            if (!empty($ingredientData)) {
                if (array_is_list($ingredientData)) {
                    $product->ingredients()->sync($ingredientData);
                } else {
                    $product->ingredients()->sync($ingredientData);
                }
            }
        }
    }
}
