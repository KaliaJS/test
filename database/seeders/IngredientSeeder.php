<?php

namespace Database\Seeders;

use App\Enums\ProductOrganicType;
use App\Models\Category;
use App\Models\Ingredient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IngredientSeeder extends Seeder
{
    use WithoutModelEvents;

    protected $ingredients = [
        [
            'name' => 'Pain',
            'description' => "Préparé à la main directement dans le camion. Une recette unique, que tu ne trouveras nulle part ailleurs.",
            'quantity' => 100,
            'organic_type' => ProductOrganicType::SWISS_BUD,
            'is_swiss' => true,
        ],
        [
            'name' => 'Viande haché de boeuf',
            'description' => "Élevé en plein air au cœur du Pays-d’Enhaut et nourri uniquement d’herbages locaux, notre bœuf est maturé sur l’os. Les veaux restent auprès de leur mère, dans le respect du rythme naturel.",
            'quantity' => 30,
            'organic_type' => ProductOrganicType::SWISS_BUD,
            'is_swiss' => true,
            'supplement_price' => 6_00,
            'max_supplement' => 2
        ],
        [
            'name' => 'Oignons caramélisés',
            'description' => 'Cuits à feu doux jusqu’à devenir fondants, nos oignons caramélisés ajoutent une touche sucrée-salée qui fait toute la différence.',
            'quantity' => 100,
            'organic_type' => ProductOrganicType::SWISS_BUD,
            'is_swiss' => true,
            'supplement_price' => 2_00,
            'is_removable' => true,
            'max_supplement' => 1
        ],
        [
            'name' => 'Cornichons (Concombres)',
            'description' => "Concombres marinés et cultivés en Suisse, plus doux qu’un cornichon, à la fois acides et légèrement sucrés. Une option locale qui plaira même à ceux qui n’aiment pas les cornichons.",
            'quantity' => 100,
            'organic_type' => ProductOrganicType::SWISS_BUD,
            'is_swiss' => true,
            'supplement_price' => 2_00,
            'max_supplement' => 1,
            'is_removable' => true,
        ],
        [
            'name' => 'Ketchup',
            'description' => "Le ketchup Heinz, c’est notre petit péché, on a honte et on l’avoue ! Comme le cola, il doit être là. On n’a pas encore trouvé mieux, mais on y travaille activement.",
            'organic_type' => ProductOrganicType::EUROPEAN,
            'is_swiss' => false,
            'supplement_price' => 2_00,
            'max_supplement' => 1,
            'is_removable' => true,
        ],
        [
            'name' => 'Fromage fondant',
            'description' => "Un mélange maison de fromages AOP Gruyère et Vacherin Fribourgeois, doux et généreux, sans sel de fonte ni autres cochonneries industrielles.",
            'quantity' => 50,
            'organic_type' => ProductOrganicType::SWISS_BUD,
            'is_swiss' => true,
            'supplement_price' => 3_00,
            'max_supplement' => 1,
            'is_removable' => true,
        ],
        [
            'name' => 'Beacon',
            'description' => "Deux tranches de vrai lard, bien croustillantes et surtout sans nitrites. Les autres s’en fichent de vous empoisonner, mais pas nous. Votre santé passe avant tout.",
            'quantity' => 50,
            'organic_type' => ProductOrganicType::SWISS_BUD,
            'is_swiss' => true,
            'supplement_price' => 3_00,
            'max_supplement' => 1,
            'is_removable' => true,
        ],
        [
            'name' => 'Crème double',
            'description' => "Crème de lait de vache pasteurisée avec minimum 45% de matière grasse.",
            'quantity' => 50,
            'organic_type' => ProductOrganicType::SWISS_BUD,
            'is_swiss' => true,
            'supplement_price' => 4_00,
            'max_supplement' => 1,
        ],
        [
            'name' => 'Meringue',
            'description' => "Meringue croustillante et légère, cuite lentement pour une texture aérienne à l’intérieur et bien craquante à l’extérieur. Un petit nuage sucré qui fond sous la dent.",
            'quantity' => 50,
            'organic_type' => ProductOrganicType::SWISS_BUD,
            'is_swiss' => true,
        ],
        [
            'name' => 'Panure',
            'description' => "Panure préparée avec de la farine de blé, du sel et de la levure. Simple, légère, croustillante — sans additifs, sans poudre bizarre.",
            'quantity' => 50,
            'organic_type' => ProductOrganicType::EUROPEAN,
            'is_swiss' => false,
        ],
        [
            'name' => 'Sel',
            'description' => "Sel extrait des Alpes vaudoises, à Bex, selon un savoir-faire ancestral. Non raffiné, riche en minéraux, il apporte une touche authentique et naturelle à chaque plat.",
            'quantity' => 50,
            'organic_type' => null,
            'is_swiss' => true,
        ],
        [
            'name' => 'Poitrine de poulet',
            'description' => "Poulet fermier élevé localement, avec accès quotidien à un vaste pâturage. Durée d’élevage minimale de 75 jours pour garantir une viande savoureuse et de qualité.",
            'quantity' => 50,
            'organic_type' => ProductOrganicType::SWISS_BUD,
            'is_swiss' => true,
        ],
        [
            'name' => 'Sirop de Menthe Marocaine',
            'description' => "Recolté à la main, provenant d'une microferme Bio de montagne du canton de vaud.",
            'quantity' => 50,
            'organic_type' => ProductOrganicType::SWISS_BUD,
            'is_swiss' => true,
        ],
        [
            'name' => 'Jus de citron',
            'description' => "Pressé à froid et conditionné dans le canton de vaud.",
            'quantity' => 50,
            'organic_type' => ProductOrganicType::EUROPEAN,
            'is_swiss' => false,
        ],
        [
            'name' => 'Sirop de Thym',
            'description' => "Recolté à la main, provenant d'une microferme Bio de montagne du canton de vaud.",
            'quantity' => 50,
            'organic_type' => ProductOrganicType::SWISS_BUD,
            'is_swiss' => true,
        ],
        [
            'name' => 'Pépites de chocolat artisanal',
            'description' => "Élaborées en Suisse par un fabricant centenaire, à base de fèves de cacao ghanéennes.",
            'quantity' => 50,
            'organic_type' => ProductOrganicType::SWISS_BUD,
            'is_swiss' => true,
        ],
        [
            'name' => 'Farine de blé',
            'description' => "Farine de blé issue d’une mouture sur meule de pierre, réalisée dans le Pays-d’Enhaut. Cette méthode lente et traditionnelle permet de conserver les nutriments et les arômes du grain.",
            'quantity' => 50,
            'organic_type' => ProductOrganicType::SWISS_BUD,
            'is_swiss' => true,
        ],
        [
            'name' => 'Beurre',
            'quantity' => 50,
            'organic_type' => ProductOrganicType::SWISS_BUD,
            'is_swiss' => true,
        ],
        [
            'name' => 'Sucre de canne',
            'quantity' => 50,
            'organic_type' => ProductOrganicType::BUD,
            'is_swiss' => true,
        ],
        [
            'name' => 'Sucre de canne complet',
            'quantity' => 50,
            'organic_type' => ProductOrganicType::BUD,
            'is_swiss' => true,
        ],
        [
            'name' => 'Bicarbonate de soude',
            'quantity' => 50,
            'organic_type' => ProductOrganicType::SWISS_BUD,
            'is_swiss' => true,
        ],
        [
            'name' => 'Oeufs entier pasteurisée',
            'quantity' => 50,
            'organic_type' => ProductOrganicType::SWISS_BUD,
            'is_swiss' => true,
        ],
        [
            'name' => "Jaune d'oeuf pasteurisé",
            'quantity' => 50,
            'organic_type' => ProductOrganicType::SWISS_BUD,
            'is_swiss' => true,
        ],
        [
            'name' => 'Vanille en poudre',
            'description' => "Ouin Ouin, c'est pas suisse, Ouin Ouin ça vient de l'autre bout du monde, je vais manifester à Lausanne si tu ne l'enlève pas. Ecoute mon petit bonhomme, la route des épices à toujours existé depuis la nuit des temps. Point !",
            'quantity' => 50,
            'organic_type' => ProductOrganicType::BUD,
            'is_swiss' => true,
        ],
        [
            'name' => 'Graine de moutarde',
            'quantity' => 50,
            'organic_type' => ProductOrganicType::SWISS_BUD,
            'is_swiss' => true,
        ],
        [
            'name' => 'Miel',
            'quantity' => 50,
            'organic_type' => ProductOrganicType::SWISS_BUD,
            'is_swiss' => true,
        ],
        [
            'name' => 'Vinaigre',
            'quantity' => 50,
            'organic_type' => ProductOrganicType::SWISS_BUD,
            'is_swiss' => true,
        ],
        [
            'name' => 'Huile de tournesol High Oleic',
            'quantity' => 50,
            'organic_type' => ProductOrganicType::SWISS_BUD,
            'is_swiss' => true,
        ],
        [
            'name' => 'Pomme de terre farineuse',
            'quantity' => 50,
            'organic_type' => ProductOrganicType::SWISS_BUD,
            'is_swiss' => true,
        ],
        [
            'name' => 'Fromage raclette',
            'description' => "Au lait cru de vache",
            'quantity' => 50,
            'organic_type' => ProductOrganicType::SWISS_BUD,
            'is_swiss' => true,
            'supplement_price' => 4_00,
            'max_supplement' => 1,
        ],
        [
            'name' => 'Fromage bleu',
            'description' => "Fromage à pâte molle persillée au lait cru de vache bio suisse",
            'quantity' => 50,
            'organic_type' => ProductOrganicType::SWISS_BUD,
            'is_swiss' => true,
            'supplement_price' => 4_00,
            'max_supplement' => 1,
        ],
        [
            'name' => 'Camembert de brebis',
            'description' => "Fromage à pâte molle au lait de brebis bio",
            'quantity' => 50,
            'organic_type' => ProductOrganicType::SWISS_BUD,
            'is_swiss' => true,
            'supplement_price' => 4_00,
            'max_supplement' => 1,
        ],
        [
            'name' => 'Moutarde',
            'quantity' => 50,
            'organic_type' => ProductOrganicType::SWISS_BUD,
            'is_swiss' => true,
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ingredient::factory()->createMany($this->ingredients);
    }
}
