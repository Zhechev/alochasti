<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\City;
use App\Models\Comment;
use App\Models\Item;
use App\Models\User;
use App\Models\Vote;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GiftShareSeeder extends Seeder
{
    /**
     * Seed the application with realistic demo data.
     */
    public function run(): void
    {
        $cities = $this->seedBulgarianCities();
        $tags = $this->seedTags();

        $categories = collect([
            'Furniture',
            'Electronics',
            'Books',
            'Clothing',
            'Kitchen',
            'Toys',
            'Sports',
            'Garden',
            'Baby',
            'Office',
            'Home Decor',
            'Tools',
        ])->map(function (string $name) {
            return Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'slug' => Str::slug($name)]
            );
        });

        $users = User::factory()->count(25)->create();
        $testUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')]
        );
        $users = $users->prepend($testUser)->unique('id')->values();

        $items = Item::factory()
            ->count(80)
            ->recycle($users)
            ->recycle($categories)
            ->recycle($cities)
            ->create();

        foreach ($items as $item) {
            $item->tags()->sync($tags->random(fake()->numberBetween(0, 4))->pluck('id')->all());
            $this->seedPhotosForItem($item);
            $this->seedCommentsForItem($item, $users);
            $this->seedVotesForItem($item, $users);
        }
    }

    private function seedPhotosForItem(Item $item): void
    {
        $photoCount = fake()->boolean(70) ? fake()->numberBetween(1, 3) : 0;

        for ($i = 0; $i < $photoCount; $i++) {
            $svg = $this->placeholderSvg($item->title, $i + 1);
            $path = "items/{$item->id}/photo_{$i}.svg";

            Storage::disk('public')->put($path, ltrim($svg));

            $item->photos()->create([
                'path' => $path,
                'sort_order' => $i,
            ]);
        }
    }

    /**
     * @param \Illuminate\Support\Collection<int, User> $users
     */
    private function seedCommentsForItem(Item $item, $users): void
    {
        $count = fake()->numberBetween(0, 8);

        if ($count === 0) {
            return;
        }

        Comment::factory()
            ->count($count)
            ->recycle($users)
            ->create([
                'item_id' => $item->id,
            ]);
    }

    /**
     * @param \Illuminate\Support\Collection<int, User> $users
     */
    private function seedVotesForItem(Item $item, $users): void
    {
        $voteCount = fake()->numberBetween(0, 18);

        if ($voteCount === 0) {
            return;
        }

        $voters = $users->random(min($voteCount, $users->count()));

        foreach ($voters as $user) {
            Vote::updateOrCreate(
                ['item_id' => $item->id, 'user_id' => $user->id],
                ['value' => fake()->randomElement([-1, 1])]
            );
        }
    }

    private function placeholderSvg(string $title, int $index): string
    {
        $safeTitle = e(Str::limit($title, 28));

        return <<<SVG
                    <svg xmlns="http://www.w3.org/2000/svg" width="900" height="600" viewBox="0 0 900 600" role="img" aria-label="Item photo">
                    <defs>
                        <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="#f8f9fa"/>
                        <stop offset="100%" stop-color="#dee2e6"/>
                        </linearGradient>
                    </defs>
                    <rect width="900" height="600" fill="url(#bg)"/>
                    <rect x="40" y="40" width="820" height="520" rx="24" fill="#ffffff" opacity="0.85"/>
                    <text x="80" y="150" font-family="Arial, sans-serif" font-size="46" fill="#212529">GiftShare</text>
                    <text x="80" y="220" font-family="Arial, sans-serif" font-size="28" fill="#6c757d">{$safeTitle}</text>
                    <text x="80" y="270" font-family="Arial, sans-serif" font-size="20" fill="#6c757d">Photo {$index}</text>
                    <text x="80" y="520" font-family="Arial, sans-serif" font-size="18" fill="#adb5bd">Placeholder image (seed data)</text>
                    </svg>
                SVG;
    }

    /**
     * Seed all Bulgarian cities and return the collection for recycling in factories.
     *
     * @return \Illuminate\Support\Collection<int, City>
     */
    private function seedBulgarianCities()
    {
        $names = [
            "Айтос","Аксаково","Алфатар","Антоново","Априлци","Ардино","Асеновград","Ахелой","Ахтопол",
            "Балчик","Банкя","Банско","Баня","Батак","Батановци","Белене","Белица","Белово","Белоградчик","Белослав","Берковица","Благоевград","Бобов дол","Бобошево","Божурище","Бойчиновци","Болярово","Борово","Ботевград","Брацигово","Брегово","Брезник","Брезово","Брусарци","Бургас","Бухово","Българово","Бяла","Бяла Слатина","Бяла черква",
            "Варна","Велики Преслав","Велико Търново","Велинград","Ветово","Ветрен","Видин","Враца","Вълчедръм","Вълчи дол",
            "Габрово","Генерал Тошево","Главиница","Глоджево","Годеч","Горна Оряховица","Гоце Делчев","Грамада","Гулянци","Гурково","Гълъбово",
            "Две могили","Дебелец","Девин","Девня","Джебел","Димитровград","Димово","Добринище","Добрич","Долна баня","Долна Митрополия","Долна Оряховица","Долни Дъбник","Долни чифлик","Доспат","Драгоман","Дряново","Дулово","Дунавци","Дупница","Дългопол",
            "Елена","Елин Пелин","Елхово",
            "Завет","Земен","Златарица","Златица","Златоград",
            "Ивайловград","Искър","Исперих","Ихтиман",
            "Каблешково","Каварна","Казанлък","Калофер","Камено","Каолиново","Карлово","Карнобат","Каспичан","Кермен","Килифарево","Китен","Клисура","Кнежа","Козлодуй","Койнаре","Копривщица","Костандово","Костенец","Костинброд","Котел","Кочериново","Кресна","Криводол","Кричим","Крумовград","Кубрат","Куклен","Кула","Кърджали","Кюстендил",
            "Левски","Летница","Ловеч","Лозница","Лом","Луковит","Лъки","Любимец","Лясковец",
            "Мадан","Маджарово","Малко Търново","Мартен","Мездра","Мелник","Меричлери","Мизия","Момин проход","Момчилград","Монтана","Мъглиж",
            "Неделино","Несебър","Николаево","Никопол","Нова Загора","Нови Искър","Нови пазар","Ново село",
            "Обзор","Омуртаг","Опака","Оряхово",
            "Павел баня","Павликени","Пазарджик","Панагюрище","Перник","Перущица","Петрич","Пещера","Пирдоп","Плачковци","Плевен","Плиска","Пловдив","Полски Тръмбеш","Поморие","Попово","Правец","Приморско","Провадия","Първомай",
            "Раднево","Радомир","Разград","Разлог","Ракитово","Раковски","Рила","Роман","Рудозем","Русе",
            "Садово","Самоков","Сандански","Сапарева баня","Свети Влас","Свиленград","Свищов","Своге","Севлиево","Сеново","Септември","Силистра","Симеоновград","Симитли","Славяново","Сливен","Сливница","Смолян","Смядово","Созопол","Сопот","София","Средец","Стамболийски","Стара Загора","Стражица","Стралджа","Стрелча","Суворово","Сунгурларе","Сухиндол","Съединение","Сърница",
            "Твърдица","Тервел","Тетевен","Тополовград","Троян","Трън","Тръстеник","Трявна","Тутракан","Търговище",
            "Угърчин",
            "Хаджидимово","Харманли","Хасково","Хисаря",
            "Цар Калоян","Царево",
            "Чепеларе","Червен бряг","Черноморец","Чипровци","Чирпан",
            "Шабла","Шивачево","Шипка","Шумен",
            "Ябланица","Якоруда","Ямбол",
        ];

        $cities = collect($names)->map(function (string $name) {
            $base = Str::slug($name, '-', 'bg');
            if ($base === '') {
                $base = 'city';
            }
            $slug = $base.'-'.strtolower(dechex(crc32($name)));

            return City::firstOrCreate(
                ['name' => $name],
                ['slug' => $slug]
            );
        })->values();

        return $cities;
    }

    /**
     * Seed a curated set of tags.
     *
     * @return \Illuminate\Support\Collection<int, Tag>
     */
    private function seedTags()
    {
        $names = [
            'urgent pickup',
            'like new',
            'needs repair',
            'pickup only',
            'small',
            'large',
            'fragile',
            'heavy',
            'kids',
            'office',
            'kitchen',
            'electronics',
            'furniture',
            'books',
            'clothes',
            'tools',
            'garden',
            'pet friendly',
        ];

        return collect($names)->map(function (string $name) {
            $base = Str::slug($name);
            if ($base === '') {
                $base = 'tag';
            }

            return Tag::firstOrCreate(
                ['slug' => $base],
                ['name' => $name, 'slug' => $base]
            );
        })->values();
    }
}


