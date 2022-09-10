<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Contact;
use App\Models\Organization;
use App\Models\User;
use App\Models\TypeformPlugin;
use App\Models\ActiveCampaignPlugin;
use App\Models\Image;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $account = Account::create(['name' => 'Um Canal de Luz']);

        User::factory()->create([
            'account_id' => $account->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'password' => 'secret',
            'owner' => true,
        ]);

        User::factory(5)->create(['account_id' => $account->id]);

        // $organizations = Organization::factory(100)
        //     ->create(['account_id' => $account->id]);

        // Contact::factory(100)
        //     ->create(['account_id' => $account->id])
        //     ->each(function ($contact) use ($organizations) {
        //         $contact->update(['organization_id' => $organizations->random()->id]);
        //     });

        TypeformPlugin::create([
            'account_id' => $account->id,
            'name' => 'Fábio Orlovas 123',
            'token' => 'tfp_ADhxUGC6huvjGMzvKpEzhEuCaZSF1SUHsiNCYqKbiXJj_3w4MNikaLKre85',
        ]);

        ActiveCampaignPlugin::create([
            'account_id' => $account->id,
            'name' => 'Um Canal de Luz',
            'api_key' => 'f996dc97729eeafd2c520b124359c06dbf78474a307be260e1d98543b8e419c162ef5cc4',
            'api_url' => 'https://umcanaldeluz91215.api-us1.com',
            'deliverable_link_field_id' => 65,
            'deliverable_tag_id' => 883,
        ]);

        // Image::create([
        //     'account_id' => $account->id,
        //     'name' => 'Logo_MEN_ASC',
        //     'image_path' => 'public/images/v8bIyrptfjRW7OH6Ck5x1swUJsuhQPbem1PxQcR3.png',
        // ]);

        // Image::create([
        //     'account_id' => $account->id,
        //     'name' => 'Logo_header_MEN_ASC',
        //     'image_path' => 'public/images/VbTwXaH8l2OcboLZgBnPbhyQgZGSibh9gqjuuW4W.png',
        // ]);

        // Image::create([
        //     'account_id' => $account->id,
        //     'name' => 'namaste',
        //     'image_path' => 'public/images/ULlfTtIE1yohbeCZs28Y9l01C83u5jL8HpqRnMK6.png',
        // ]);
    }
}
