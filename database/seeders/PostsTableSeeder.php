<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        $posts = [];
        $categories = collect(Category::all()->modelKeys());
        $user = collect(User::where('id', '>', 2)->get()->modelKeys());

        for ($i = 0; $i < 980; $i++) {
            $days = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28'];
            $months = ['01', '02', '03', '04', '05', '06', '07', '08'];
            $post_date = "2020-" . Arr::random($months) . "-" . Arr::random($days) . " 01:01:01";
            $post_title = $faker->sentence(mt_rand(3, 6), true);


            $posts[] = [
                'title'         => $post_title,
                'slug'          => Str::slug($post_title),
                'description'   => $faker->paragraph(),
                'status'        => rand(0, 1),
                'comment_able'  => rand(0, 1),
                'user_id'       => $user->random(),
                'category_id'   => $categories->random(),
                'created_at'    => $post_date,
                'updated_at'    => $post_date,

            ];
        }

        $chunks = array_chunk($posts, 500);
        foreach ($chunks as $chunk) {
            Post::insert($chunk);
        }


        for ($i = 0; $i < 20; $i++) {
            $days = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28'];
            $months = ['01', '02', '03', '04', '05', '06', '07', '08'];
            $post_date = "2020-" . Arr::random($months) . "-" . Arr::random($days) . " 01:01:01";
            $post_title = $faker->sentence(mt_rand(3, 6), true);
            $tmp_images = [
                public_path('assets/tmp/01.jpg'),
                public_path('assets/tmp/01.jpg'),
                public_path('assets/tmp/02.png'),
                public_path('assets/tmp/03.jpg'),
                public_path('assets/tmp/04.jpg'),
                public_path('assets/tmp/05.jpg'),
                public_path('assets/tmp/06.jpg'),
                public_path('assets/tmp/07.jpg'),
                public_path('assets/tmp/08.jpg'),
                public_path('assets/tmp/09.jpg'),
                public_path('assets/tmp/10.jpg'),
                public_path('assets/tmp/11.jpg'),
                public_path('assets/tmp/12.jpg'),
                public_path('assets/tmp/13.jpg'),
                public_path('assets/tmp/14.jpg'),
                public_path('assets/tmp/15.jpg'),
                public_path('assets/tmp/16.jpg'),
                public_path('assets/tmp/17.jpg'),
                public_path('assets/tmp/18.jpg'),
                public_path('assets/tmp/19.jpg'),
                public_path('assets/tmp/20.jpg'),
                public_path('assets/tmp/21.jpg'),
                public_path('assets/tmp/22.jpg'),
                public_path('assets/tmp/23.jpg'),
                public_path('assets/tmp/24.jpg'),
                public_path('assets/tmp/25.jpg'),
                public_path('assets/tmp/26.jpg'),
            ];


            $userPosts = Post::create([
                'title'         => $post_title,
                'slug'          => Str::slug($post_title),
                'description'   => $faker->paragraph(),
                'status'        => rand(0, 1),
                'comment_able'  => rand(0, 1),
                'user_id'       => rand(3, 5),
                'category_id'   => $categories->random(),
                'created_at'    => $post_date,
                'updated_at'    => $post_date,
            ]);

            $filename = ''.$userPosts->slug.'.jpg';
            $path = public_path('/assets/posts/' . $filename);
            Image::make(Arr::random($tmp_images))->save($path, 100);

            $userPosts->media()->create([
                'file_name' => $filename,
                'file_size' => 5465564,
                'file_type' => 'image/jpg',
            ]);

        }

    }
}
