<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Event;
use App\Models\Question;
use App\Models\Response;
use App\Models\Spot;
use App\Models\SportCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application database with sample Moroccan sports data.
     */
    public function run(): void
    {
        // Sport categories
        $categories = [
            ['name' => 'Football', 'icon' => 'futbol'],
            ['name' => 'Basketball', 'icon' => 'basketball'],
            ['name' => 'Tennis', 'icon' => 'tennis'],
            ['name' => 'Running', 'icon' => 'running'],
            ['name' => 'Swimming', 'icon' => 'swimmer'],
            ['name' => 'Yoga', 'icon' => 'yoga'],
            ['name' => 'Volleyball', 'icon' => 'volleyball'],
            ['name' => 'Boxing', 'icon' => 'boxing-glove'],
            ['name' => 'Cycling', 'icon' => 'bicycle'],
            ['name' => 'Gym', 'icon' => 'dumbbell'],
        ];

        foreach ($categories as $cat) {
            SportCategory::create($cat);
        }

        // Admin user
        $admin = User::create([
            'name' => 'Admin Bssahty',
            'email' => 'admin@bssahty.ma',
            'password' => Hash::make('password'),
            'city' => 'Casablanca',
            'level' => 'pro',
            'role' => 'admin',
            'bio' => 'Administrator of the Bssahty platform.',
        ]);
        $admin->sportCategories()->attach([1, 2, 3]);

        // Regular users
        $users = [];
        $userData = [
            ['name' => 'Youssef El Amrani', 'email' => 'youssef@example.com', 'city' => 'Safi', 'level' => 'intermediate'],
            ['name' => 'Fatima Zahra', 'email' => 'fatima@example.com', 'city' => 'Casablanca', 'level' => 'beginner'],
            ['name' => 'Karim Benzaoui', 'email' => 'karim@example.com', 'city' => 'Rabat', 'level' => 'pro'],
            ['name' => 'Amina Hassani', 'email' => 'amina@example.com', 'city' => 'Marrakech', 'level' => 'intermediate'],
            ['name' => 'Omar Tazi', 'email' => 'omar@example.com', 'city' => 'Safi', 'level' => 'beginner'],
        ];

        foreach ($userData as $i => $data) {
            $user = User::create(array_merge($data, [
                'password' => Hash::make('password'),
                'bio' => 'Sports enthusiast from ' . $data['city'],
            ]));
            $user->sportCategories()->attach([($i % 10) + 1, (($i + 3) % 10) + 1]);
            $users[] = $user;
        }

        // Spots (Moroccan cities)
        $spots = [
            Spot::create([
                'name' => 'Stade El Massira', 'description' => 'Grand terrain de football à Safi.',
                'latitude' => 32.2994, 'longitude' => -9.2372, 'city' => 'Safi', 'address' => 'Avenue Mohammed V, Safi',
                'sport_category_id' => 1, 'created_by' => $users[0]->id, 'status' => 'approved',
            ]),
            Spot::create([
                'name' => 'Parc de la Ligue Arabe', 'description' => 'Parc idéal pour le jogging et le yoga.',
                'latitude' => 33.5883, 'longitude' => -7.6187, 'city' => 'Casablanca', 'address' => 'Bd Moulay Youssef',
                'sport_category_id' => 4, 'created_by' => $users[1]->id, 'status' => 'approved',
            ]),
            Spot::create([
                'name' => 'Complexe Sportif Moulay Abdellah', 'description' => 'Complexe multi-sport à Rabat.',
                'latitude' => 33.9566, 'longitude' => -6.8677, 'city' => 'Rabat', 'address' => 'Avenue Ibn Sina',
                'sport_category_id' => 2, 'created_by' => $users[2]->id, 'status' => 'approved',
            ]),
            Spot::create([
                'name' => 'Terrain de Proximité Safi', 'description' => 'Petit terrain de quartier pour le foot.',
                'latitude' => 32.3008, 'longitude' => -9.2400, 'city' => 'Safi', 'address' => 'Quartier Biada, Safi',
                'sport_category_id' => 1, 'created_by' => $users[4]->id, 'status' => 'approved',
            ]),
            Spot::create([
                'name' => 'Salle de Boxing Atlas', 'description' => 'Salle de boxe moderne à Marrakech.',
                'latitude' => 31.6295, 'longitude' => -7.9811, 'city' => 'Marrakech', 'address' => 'Guéliz, Marrakech',
                'sport_category_id' => 8, 'created_by' => $users[3]->id, 'status' => 'approved',
            ]),
            Spot::create([
                'name' => 'Pending Gym Spot', 'description' => 'New gym awaiting approval.',
                'latitude' => 33.5731, 'longitude' => -7.5898, 'city' => 'Casablanca', 'address' => 'Maarif',
                'sport_category_id' => 10, 'created_by' => $users[1]->id, 'status' => 'pending',
            ]),
        ];

        // Questions
        $questions = [
            Question::create([
                'title' => 'Cherche gardien pour match ce soir à Safi',
                'content' => 'On organise un match 5v5 au Stade El Massira ce soir à 20h. Il nous manque un gardien. Qui est dispo ?',
                'user_id' => $users[0]->id, 'spot_id' => $spots[0]->id, 'sport_category_id' => 1,
            ]),
            Question::create([
                'title' => 'Meilleur spot pour courir à Casablanca ?',
                'content' => 'Je suis nouvelle à Casa et je cherche des endroits sympas pour faire du jogging le matin.',
                'user_id' => $users[1]->id, 'sport_category_id' => 4,
            ]),
            Question::create([
                'title' => 'Qui pour du basket le weekend à Rabat ?',
                'content' => 'Cherche des joueurs de basket de niveau intermédiaire pour des sessions le samedi matin au complexe Moulay Abdellah.',
                'user_id' => $users[2]->id, 'spot_id' => $spots[2]->id, 'sport_category_id' => 2,
            ]),
            Question::create([
                'title' => 'Cours de yoga en plein air à Casablanca',
                'content' => 'Est-ce que quelqu\'un connaît des sessions de yoga gratuites ou pas chères au Parc de la Ligue Arabe ?',
                'user_id' => $users[3]->id, 'spot_id' => $spots[1]->id, 'sport_category_id' => 6,
            ]),
        ];

        // Responses
        Response::create([
            'content' => 'Je suis dispo ! Je peux jouer gardien. Envoie-moi un message.',
            'question_id' => $questions[0]->id, 'user_id' => $users[4]->id,
        ]);
        Response::create([
            'content' => 'Le Parc de la Ligue Arabe est top pour le jogging, surtout tôt le matin.',
            'question_id' => $questions[1]->id, 'user_id' => $users[2]->id,
        ]);
        Response::create([
            'content' => 'Essaie aussi la corniche, c\'est super agréable !',
            'question_id' => $questions[1]->id, 'user_id' => $users[3]->id,
        ]);
        Response::create([
            'content' => 'Je suis partant pour le basket ! Quel niveau tu cherches exactement ?',
            'question_id' => $questions[2]->id, 'user_id' => $users[0]->id,
        ]);

        // Events
        $event = Event::create([
            'title' => 'Match de foot 5v5 - Safi',
            'description' => 'Match amical ce soir au Stade El Massira.',
            'question_id' => $questions[0]->id,
            'user_id' => $users[0]->id,
            'spot_id' => $spots[0]->id,
            'date' => now()->addDays(2)->setHour(20),
            'max_participants' => 10,
        ]);
        $event->participants()->attach([$users[0]->id, $users[4]->id]);

        // Conversations
        $conversation = Conversation::create();
        $conversation->users()->attach([$users[0]->id, $users[4]->id]);
        $conversation->messages()->create([
            'body' => 'Salut ! Tu es toujours dispo pour le match ?',
            'user_id' => $users[0]->id,
        ]);
        $conversation->messages()->create([
            'body' => 'Oui bien sûr ! Je serai là à 20h.',
            'user_id' => $users[4]->id,
            'is_read' => true,
        ]);

        // Favorites
        $users[0]->favoriteSpots()->attach([$spots[0]->id, $spots[3]->id]);
        $users[1]->favoriteSpots()->attach([$spots[1]->id]);

        // Likes
        $users[1]->likedQuestions()->attach([$questions[0]->id]);
        $users[2]->likedQuestions()->attach([$questions[0]->id, $questions[1]->id]);
        $questions[0]->update(['likes_count' => 2]);
        $questions[1]->update(['likes_count' => 1]);
    }
}
