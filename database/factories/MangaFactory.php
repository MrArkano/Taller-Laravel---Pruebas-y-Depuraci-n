<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Manga;
use App\Models\Publisher;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MangaFactory extends Factory
{
    protected $model = Manga::class;

    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'publisher_id' => Publisher::inRandomOrder()->first()?->id ?? Publisher::factory(),
            'title' => ucwords($title),
            'slug' => Str::slug($title),
            'author' => fake()->name(),
            'synopsis' => fake()->paragraphs(2, true),
            'price' => fake()->randomFloat(2, 8.50, 24.99),
            'stock' => fake()->numberBetween(5, 50),
            'cover_image' => 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?w=600&auto=format&fit=crop&q=80',
            'isbn' => fake()->isbn13(),
            'pages' => fake()->numberBetween(180, 450),
            'format' => fake()->randomElement(['Tankobon (11.5x17 cm)', 'Kanzenban / Deluxe (15x21 cm)', 'B6 (12.8x18.2 cm)']),
            'is_featured' => fake()->boolean(25),
        ];
    }
}
