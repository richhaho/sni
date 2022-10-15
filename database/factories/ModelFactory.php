<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'first_name' => strtoupper($faker->firstName),
        'last_name' => strtoupper($faker->lastName),
        'email' => strtoupper($faker->unique()->safeEmail),
        'status' => 1,
        'password' => $password ?: $password = bcrypt('123456'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Client::class, function (Faker\Generator $faker) {
    $gender = ['male', 'female'];
    $billing_type = ['none', 'attime', 'invoiced'];
    $send_certified = ['none', 'green', 'nongreen'];
    $print_method = ['none', 'sni', 'client'];

    $xg = $gender[rand(0, 1)];
    $xb = $billing_type[rand(0, 2)];
    $xs = $send_certified[rand(0, 2)];
    $xp = $print_method[rand(0, 2)];

    return [
        'company_name' => strtoupper($faker->company),
        'title' => strtoupper($faker->jobTitle($xg)),
        'first_name' => strtoupper($faker->firstName($xg)),
        'last_name' => strtoupper($faker->lastName),
        'address_1' => strtoupper($faker->buildingNumber.' '.$faker->streetName),
        'address_2' => strtoupper($faker->secondaryAddress),
        'city' => strtoupper($faker->city),
        'county' => strtoupper($faker->citySuffix),
        'state' => strtoupper($faker->state),
        'zip' => $faker->postcode,
        'country' => strtoupper('United States'),
        'phone' => $faker->phoneNumber,
        'mobile' => $faker->phoneNumber,
        'fax' => $faker->phoneNumber,
        'email' => strtoupper($faker->unique()->safeEmail),
        'default_materials' => strtoupper($faker->paragraphs(3, true)),
        'interest_rate' => $faker->randomFloat(2, 0, 25),
        'gender' => $xg,
        'billing_type' => $xb,
        'send_certified' => $xs,
        'print_method' => $xp,
        'status' => 4,

    ];
});

$factory->define(App\Entity::class, function (Faker\Generator $faker) {
    $lates_type = [
        'customer',
        'general_contractor',
        'bond',
        'landowner',
        'leaseholder',
        'lender',
        'copy_recipient',
        'sub_contractor',
        'sub_sub',
    ];
    $xl = $lates_type[rand(0, 8)];

    return [
        'firm_name' => strtoupper($faker->company),
        'latest_type' => $xl,

    ];
});

$factory->define(App\ContactInfo::class, function (Faker\Generator $faker) {
    $gender = ['male', 'female'];
    $xg = $gender[rand(0, 1)];

    return [
        'first_name' => strtoupper($faker->firstName($xg)),
        'last_name' => strtoupper($faker->lastName),
        'address_1' => strtoupper($faker->buildingNumber.' '.$faker->streetName),
        'address_2' => strtoupper($faker->secondaryAddress),
        'city' => strtoupper($faker->city),
        'county' => strtoupper($faker->citySuffix),
        'state' => strtoupper($faker->state),
        'zip' => $faker->postcode,
        'country' => strtoupper('United States'),
        'phone' => $faker->phoneNumber,
        'mobile' => $faker->phoneNumber,
        'fax' => $faker->phoneNumber,
        'email' => strtoupper($faker->unique()->safeEmail),
        'gender' => $xg,
    ];
});

    $factory->define(App\Job::class, function (Faker\Generator $faker) {
        $job_statuses = [
            'notice-to-owner' => 'Notice to Owner',
            'release-of-lien' => 'Release of Lien',
            'demand-letter' => 'Demand Letter',
            'claim-of-lien' => 'Claim of Lien',
            'ammended-claim-of-lien' => 'Amended Claim of Lien',
            'notice-of-non-payment' => 'Notice of Non Payment',
            'partial-satisfaction-of-lien' => 'Partial Satisfaction of Lien',
            'satisfaction-of-lien' => 'Satisfaction of Lien',
        ];

        return [
            'type' => $faker->randomElement(['public', 'private']),
            'status' => $faker->randomElement($job_statuses),
            'name' => strtoupper($faker->bs),
            'address_1' => strtoupper($faker->buildingNumber.' '.$faker->streetName),
            'address_2' => strtoupper($faker->secondaryAddress),
            'address_corner' => strtoupper($faker->secondaryAddress),
            'city' => strtoupper($faker->city),
            'state' => strtoupper($faker->state),
            'county' => strtoupper($faker->citySuffix),
            'zip' => $faker->postcode,
            'country' => strtoupper('United States'),
            'started_at' => $faker->dateTimeBetween('-30 days', '10 days'),
            'contract_amount' => $faker->numberBetween(1500, 150000),
            'default_materials' => strtoupper($faker->paragraphs(rand(1, 3), true)),
            'legal_description' => strtoupper($faker->paragraphs(rand(1, 3), true)),
            'folio_number' => strtoupper($faker->bothify('???######')),
            'interest_rate' => $faker->randomFloat(2, 0, 25),
        ];
    });

$factory->define(App\WorkOrder::class, function (Faker\Generator $faker) {
    $wo_types = App\WorkOrderType::all()->pluck('slug')->toArray();
    $statuses = [
        'open',
        'cancelled',
        'cancelled charge',
        'search',
        'tax rolls',
        'phone calls',
        'atids',
        'pending',
        'data entry',
        'edit',
        'completed',
        'cancelled no charge',
        'closed',
        'payment pending',
    ];

    return [
        'type' => $faker->randomElement($wo_types),
        'status' => $faker->randomElement($statuses),
        'due_at' => $faker->dateTimeBetween('-30 days', '10 days'),
        'is_rush' => rand(0, 1),
    ];
});

$factory->state(App\ContactInfo::class, 'primary', function ($faker) {
    return [
        'primary' => 1,
    ];
});
