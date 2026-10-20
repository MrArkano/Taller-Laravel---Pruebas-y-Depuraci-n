<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Publisher;
use App\Models\Manga;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MangaDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Categorías / Demografías
        $categories = [
            [
                'name' => 'Shonen',
                'slug' => 'shonen',
                'description' => 'Aventuras épicas, combate, emoción, comedia y superación personal dirigidas a un público apasionado.',
            ],
            [
                'name' => 'Seinen',
                'slug' => 'seinen',
                'description' => 'Narrativas complejas, contenido maduro, dilemas morales, thrillers psicológicos y profundidad artística.',
            ],
            [
                'name' => 'Shojo',
                'slug' => 'shojo',
                'description' => 'Historias centradas en relaciones interpersonales, romance profundo, drama y evolución personal.',
            ],
            [
                'name' => 'Dark Fantasy',
                'slug' => 'dark-fantasy',
                'description' => 'Fantasía sombría, mundos hostiles, elementos sobrenaturales, monstruos y luchas desesperadas.',
            ],
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[$cat['slug']] = Category::firstOrCreate(
                ['slug' => $cat['slug']],
                ['name' => $cat['name'], 'description' => $cat['description']]
            );
        }

        // 2. Editoriales / Publishers
        $publishers = [
            [
                'name' => 'Panini Manga',
                'slug' => 'panini-manga',
                'country' => 'México / Japón',
                'description' => 'Uno de los mayores sellos editoriales de historietas y cómics a nivel mundial.',
                'website' => 'https://www.panini.com.mx',
            ],
            [
                'name' => 'Editorial Ivrea',
                'slug' => 'editorial-ivrea',
                'country' => 'Argentina / España',
                'description' => 'Editorial pionera en la traducción y distribución de manga con enorme fidelidad a la obra japonesa.',
                'website' => 'https://www.ivreality.com.ar',
            ],
            [
                'name' => 'Norma Editorial',
                'slug' => 'norma-editorial',
                'country' => 'España',
                'description' => 'Sello de referencia en historietas europeas, manga japonés y ediciones coleccionista de altísima gama.',
                'website' => 'https://www.normaeditorial.com',
            ],
            [
                'name' => 'Milky Way Ediciones',
                'slug' => 'milky-way',
                'country' => 'España',
                'description' => 'Editorial reconocida por su catálogo selecto, autores de culto y exquisita encuadernación.',
                'website' => 'https://www.milkywayediciones.com',
            ],
        ];

        $publisherModels = [];
        foreach ($publishers as $pub) {
            $publisherModels[$pub['slug']] = Publisher::firstOrCreate(
                ['slug' => $pub['slug']],
                [
                    'name' => $pub['name'],
                    'country' => $pub['country'],
                    'description' => $pub['description'],
                    'website' => $pub['website'],
                ]
            );
        }

        // 3. Mangas (Al menos 8 títulos reconocidos de prueba)
        $mangas = [
            [
                'title' => 'Berserk: Maximum Edition Vol. 1',
                'slug' => 'berserk-maximum-vol-1',
                'author' => 'Kentaro Miura',
                'category' => 'seinen',
                'publisher' => 'panini-manga',
                'price' => 19.90,
                'stock' => 24,
                'cover_image' => 'https://images.unsplash.com/photo-1578632767115-351597cf2477?w=600&auto=format&fit=crop&q=80',
                'synopsis' => 'Guts, el Guerrero Negro, blande una espada colosal y una prótesis con cañón en pos de venganza contra los Apóstoles y la temible Mano de Dios. Acompañado por el inquieto elfo Puck, recorre un mundo medieval desolado por la peste, la herejía y la barbarie, marcado a fuego por el estigma maldito que atrae a las criaturas del infierno cada noche.',
                'isbn' => '978-8413340012',
                'pages' => 440,
                'format' => 'Kanzenban / Deluxe (15x21 cm)',
                'is_featured' => true,
            ],
            [
                'title' => 'One Piece Vol. 1: Romance Dawn',
                'slug' => 'one-piece-vol-1',
                'author' => 'Eiichiro Oda',
                'category' => 'shonen',
                'publisher' => 'editorial-ivrea',
                'price' => 9.50,
                'stock' => 50,
                'cover_image' => 'https://images.unsplash.com/photo-1618336753974-aae8e04506aa?w=600&auto=format&fit=crop&q=80',
                'synopsis' => 'Monkey D. Luffy se lanza a navegar con el firme propósito de encontrar el mítico One Piece y convertirse en el legendario Rey de los Piratas. Con los poderes elásticos adquiridos tras ingerir por descuido una Fruta del Diablo, Luffy busca formar su tripulación comenzando por el fiero cazarrecompensas Roronoa Zoro.',
                'isbn' => '978-8496461001',
                'pages' => 208,
                'format' => 'Tankobon (11.5x17 cm)',
                'is_featured' => true,
            ],
            [
                'title' => 'Chainsaw Man Vol. 1: Perro y Motosierra',
                'slug' => 'chainsaw-man-vol-1',
                'author' => 'Tatsuki Fujimoto',
                'category' => 'shonen',
                'publisher' => 'norma-editorial',
                'price' => 10.00,
                'stock' => 35,
                'cover_image' => 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?w=600&auto=format&fit=crop&q=80',
                'synopsis' => 'Denji es un joven paupérrimo que arriesga el pellejo cazando demonios menores con Pochita, su fiel perro motosierra demoníaco, intentando cancelar la deuda abrumadora dejada por su padre ante la Yakuza. Traicionado y asesinado, un pacto definitivo funde sus corazones y lo hace renacer como el sádico e imparable Chainsaw Man.',
                'isbn' => '978-8467943207',
                'pages' => 192,
                'format' => 'Tankobon (11.5x17 cm)',
                'is_featured' => true,
            ],
            [
                'title' => 'Jujutsu Kaisen Vol. 1: Ryomen Sukuna',
                'slug' => 'jujutsu-kaisen-vol-1',
                'author' => 'Gege Akutami',
                'category' => 'shonen',
                'publisher' => 'panini-manga',
                'price' => 10.50,
                'stock' => 40,
                'cover_image' => 'https://images.unsplash.com/photo-1563089145-599997674d42?w=600&auto=format&fit=crop&q=80',
                'synopsis' => 'Yuji Itadori es un estudiante dotado de una fuerza física descomunal. Para rescatar a los miembros de su club ante un ataque inminente de espíritus malditos, engulle sin pensarlo el dedo embalsamado de Ryomen Sukuna, alojando en su propio cuerpo al monarca absoluto de las maldiciones.',
                'isbn' => '978-8467940022',
                'pages' => 192,
                'format' => 'Tankobon (11.5x17 cm)',
                'is_featured' => false,
            ],
            [
                'title' => 'Attack on Titan (Shingeki no Kyojin) Vol. 1',
                'slug' => 'attack-on-titan-vol-1',
                'author' => 'Hajime Isayama',
                'category' => 'seinen',
                'publisher' => 'norma-editorial',
                'price' => 11.00,
                'stock' => 30,
                'cover_image' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?w=600&auto=format&fit=crop&q=80',
                'synopsis' => 'Confinada tras tres muros gigantescos para escapar de la voracidad de los Titanes, la raza humana ha disfrutado de un siglo de relativa calma. La paz se quiebra súbitamente cuando el Titán Colosal desmorona la muralla exterior, empujando a Eren Jaeger a jurar el exterminio de cada monstruo.',
                'isbn' => '978-8467909029',
                'pages' => 192,
                'format' => 'Tankobon (11.5x17 cm)',
                'is_featured' => false,
            ],
            [
                'title' => 'Tokyo Ghoul Vol. 1',
                'slug' => 'tokyo-ghoul-vol-1',
                'author' => 'Sui Ishida',
                'category' => 'dark-fantasy',
                'publisher' => 'editorial-ivrea',
                'price' => 9.80,
                'stock' => 18,
                'cover_image' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=600&auto=format&fit=crop&q=80',
                'synopsis' => 'En el subsuelo de Tokio habitan los ghouls, seres carnívoros que se camuflan entre la multitud y subsisten alimentándose de carne humana. Ken Kaneki es un taciturno estudiante que, luego de un letal atentado, sobrevive gracias al trasplante médico de órganos de un ghoul, despertando con hambre sanguinaria.',
                'isbn' => '978-8416426027',
                'pages' => 220,
                'format' => 'Tankobon (11.5x17 cm)',
                'is_featured' => false,
            ],
            [
                'title' => 'Demon Slayer: Kimetsu no Yaiba Vol. 1',
                'slug' => 'demon-slayer-vol-1',
                'author' => 'Koyoharu Gotouge',
                'category' => 'shonen',
                'publisher' => 'panini-manga',
                'price' => 10.00,
                'stock' => 45,
                'cover_image' => 'https://images.unsplash.com/photo-1613376023733-0a73315d9b06?w=600&auto=format&fit=crop&q=80',
                'synopsis' => 'Tanjiro Kamado, un muchacho humilde y abnegado, regresa a su hogar en la montaña solo para hallar a su familia despiadadamente asesinada. Su hermana Nezuko, la única sobreviviente, ha sido transformada en un demonio, motivando a Tanjiro a empuñar la espada del Cuerpo de Cazadores para devolverle su humanidad.',
                'isbn' => '978-8467935400',
                'pages' => 192,
                'format' => 'Tankobon (11.5x17 cm)',
                'is_featured' => true,
            ],
            [
                'title' => 'Spy x Family Vol. 1',
                'slug' => 'spy-x-family-vol-1',
                'author' => 'Tatsuya Endo',
                'category' => 'shonen',
                'publisher' => 'editorial-ivrea',
                'price' => 11.50,
                'stock' => 28,
                'cover_image' => 'https://images.unsplash.com/photo-1569701813229-33284b643e3c?w=600&auto=format&fit=crop&q=80',
                'synopsis' => 'Para infiltrarse en el círculo de un esquivo político enemigo, el espía internacional de élite Twilight debe armar una familia ficticia en tiempo récord. Lo que nunca imaginó es que la niña huérfana que adoptó es una telépata y su flamante esposa es una temible asesina a sueldo.',
                'isbn' => '978-8418271106',
                'pages' => 212,
                'format' => 'Tankobon (11.5x17 cm)',
                'is_featured' => false,
            ],
            [
                'title' => 'Vagabond Vol. 1: Takezo',
                'slug' => 'vagabond-vol-1',
                'author' => 'Takehiko Inoue',
                'category' => 'seinen',
                'publisher' => 'editorial-ivrea',
                'price' => 16.50,
                'stock' => 15,
                'cover_image' => 'https://images.unsplash.com/photo-1514533450685-4493e01d1fdc?w=600&auto=format&fit=crop&q=80',
                'synopsis' => 'Inspirada en las crónicas del legendario samurái Miyamoto Musashi, narra la metamorfosis de Shinmen Takezo tras sobrevivir a la carnicería de Sekigahara, desatando una búsqueda espiritual y marcial para descubrir el auténtico significado de ser invencible bajo el cielo.',
                'isbn' => '978-8495861115',
                'pages' => 260,
                'format' => 'Kanzenban / Deluxe (15x21 cm)',
                'is_featured' => false,
            ],
            [
                'title' => 'Nana Vol. 1',
                'slug' => 'nana-vol-1',
                'author' => 'Ai Yazawa',
                'category' => 'shojo',
                'publisher' => 'milky-way',
                'price' => 12.00,
                'stock' => 22,
                'cover_image' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=600&auto=format&fit=crop&q=80',
                'synopsis' => 'Dos jóvenes con el mismo nombre y veinte años de edad coinciden en un tren rumbo a Tokio. Una persigue a su novio con un temperamento soñador; la otra busca triunfar con su banda de punk rock Black Stones. Sus vidas se entrelazan en un departamento compartido donde el amor, la lealtad y la música pondrán a prueba sus destinos.',
                'isbn' => '978-8416960019',
                'pages' => 196,
                'format' => 'B6 (12.8x18.2 cm)',
                'is_featured' => false,
            ],
        ];

        foreach ($mangas as $data) {
            Manga::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'category_id' => $categoryModels[$data['category']]->id,
                    'publisher_id' => $publisherModels[$data['publisher']]->id,
                    'title' => $data['title'],
                    'author' => $data['author'],
                    'price' => $data['price'],
                    'stock' => $data['stock'],
                    'cover_image' => $data['cover_image'],
                    'synopsis' => $data['synopsis'],
                    'isbn' => $data['isbn'],
                    'pages' => $data['pages'],
                    'format' => $data['format'],
                    'is_featured' => $data['is_featured'],
                ]
            );
        }
    }
}
