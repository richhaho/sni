<?php

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Client::class, 20)->create()->each(function ($c) {
            $user = factory(App\User::class)->create(['email' => $c->email, 'first_name' => $c->first_name, 'last_name' => $c->last_name, 'client_id' => $c->id]);
            $default_role = App\Role::where('name', 'client')->get()->first();
            $user->attachRole($default_role);
            $c->client_user_id = ($user->id);
            $c->save();

            $entity = new App\Entity();

            if (strlen(trim($c->company_name)) == 0) {
                $entity->firm_name = $c->full_name;
            } else {
                $entity->firm_name = $c->company_name;
            }
            $entity->latest_type = 'client';
            $entity->client_id = $c->id;
            $entity->save();

            //create associate
            $associate = new App\ContactInfo();
            if (strlen($c->first_name) > 0) {
                $associate->first_name = $c->first_name;
            } else {
                $associate->first_name = ' ';
            }
            if (strlen($c->last_name) > 0) {
                $associate->last_name = $c->last_name;
            } else {
                $associate->last_name = ' ';
            }
            //$associate->gender = $c->gender;
            $associate->address_1 = $c->address_1;
            $associate->address_2 = $c->address_2;
            $associate->city = $c->city;
            $associate->county = $c->county;
            $associate->state = $c->state;
            $associate->zip = $c->zip;
            $associate->country = $c->country;
            $associate->phone = $c->phone;
            $associate->mobile = $c->mobile;
            $associate->fax = $c->fax;
            $associate->email = $c->email;
            $associate->primary = 2;
            $associate->status = 1;
            $associate->entity_id = $entity->id;
            $associate->save();

            $xne = rand(1, 8);
            factory(App\Entity::class, $xne)->create(['client_id' => $c->id])->each(function ($e) {
                $xco = rand(1, 5);

                factory(App\ContactInfo::class)->states('primary')->create(['entity_id' => $e->id]);

                factory(App\ContactInfo::class, $xco)->create(['entity_id' => $e->id]);
            });
            $xcontcats = $c->contacts()->pluck('contact_infos.id')->toArray();

            if (count($xcontcats) >= 1) {
                $xnj = rand(1, 4);
                factory(App\Job::class, $xnj)->create(['client_id' => $c->id])->each(function ($j) use ($xcontcats, $associate) {
                    $job_party = new App\JobParty();
                    $job_party->job_id = $j->id;
                    $job_party->type = 'client';
                    $job_party->entity_id = $associate->entity_id;
                    $job_party->contact_id = $associate->id;
                    $job_party->save();

                    $keys = array_rand($xcontcats, rand(1, (count($xcontcats) > 6 ? 6 : count($xcontcats))));
                    if (is_array($keys)) {
                        foreach ($keys as $key => $val) {
                            $contacts_ids[$key] = $xcontcats[$val];
                        }
                    } else {
                        $contacts_ids[0] = $xcontcats[$keys];
                    }
                    //add parties
                    if (is_array($contacts_ids) || is_object($contacts_ids)) {
                        $faker = Faker::create();
                        foreach ($contacts_ids as $cid) {
                            $contact = App\ContactInfo::find($cid);
                            $party_type = $faker->randomElements(['customer', 'general_contractor', 'bond', 'landowner', 'leaseholder', 'copy_recipient', 'sub_contractor', 'sub_sub']);
                            $job_party = new App\JobParty();
                            $job_party->job_id = $j->id;
                            $job_party->type = $party_type[0];
                            $job_party->entity_id = $contact->entity_id;
                            $job_party->contact_id = $contact->id;
                            $job_party->save();
                            switch ($party_type[0]) {
                                case 'bond':
                                    $xfn = rand(1, 20);
                                    $file_name = 'lorem'.sprintf('%03d', $xfn).'.pdf';
                                    $f = new Illuminate\Http\UploadedFile(public_path('pdf/'.$file_name), $file_name);
                                    $xfilename = 'job-'.$j->id.'-party-'.$job_party->id.'.'.$f->guessExtension();
                                    $xpath = 'jobparties/bonds/pdfs';
                                    $f->storeAs($xpath, $xfilename);
                                    $job_party->bond_pdf_filename = $f->getClientOriginalName();
                                    $job_party->bond_pdf = $xpath.'/'.$xfilename;
                                    $job_party->bond_pdf_filename_mime = $f->getMimeType();
                                    $job_party->bond_pdf_filename_size = $f->getSize();
                                    $job_party->bond_amount = $j->contract_amount * 0.85;
                                    $job_party->bond_bookpage_number = strtoupper($faker->bothify('???#####-??'));
                                    $job_party->bond_date = $faker->dateTimeBetween('-30 days', '10 days');
                                    $job_party->save();
                                    break;
                                case  'landowner':
                                    $job_party->landowner_deed_number = strtoupper($faker->bothify('??-##??##-??'));
                                    $job_party->landowner_lien_prohibition = $faker->numberBetween(0, 1);
                                    $job_party->save();
                                    break;
                                case 'leaseholder':
                                    $job_party->leaseholder_lease_agreement = strtoupper($faker->paragraphs(3, true));
                                    $job_party->leaseholder_lease_number = $faker->bothify('########');
                                    $job_party->leaseholder_bookpage_number = strtoupper($faker->bothify('???#####-??'));
                                    $job_party->save();
                                    break;
                            }
                        }
                    }

                    //add atachments
                    $num_attchments = rand(1, 3);
                    for ($i = 1; $i < $num_attchments; $i++) {
                        $attachment = new App\Attachment();
                        $file_name = 'lorem'.sprintf('%03d', rand(1, 20)).'.pdf';
                        $f = new Illuminate\Http\UploadedFile(public_path('pdf/'.$file_name), $file_name);
                        $attachment_types = array_flatten(App\AttachmentType::get()->pluck('slug')->toArray());
                        $xtype = $faker->randomElements($attachment_types);

                        $attachment->type = $xtype[0];
                        $attachment->description = strtoupper($faker->text(150));
                        $attachment->original_name = $f->getClientOriginalName();
                        $attachment->file_mime = $f->getMimeType();
                        $attachment->file_size = $f->getSize();
                        $attachment->user_id = $j->client->admin_user->id;
                        $j->attachments()->save($attachment);
                        $attachment->save();

                        $xfilename = 'attachment-'.$attachment->id.'.'.$f->guessExtension();
                        $xpath = 'attachments/jobs/'.$j->id.'/';
                        $f->storeAs($xpath, $xfilename);
                        $attachment->file_path = $xpath.$xfilename;
                        $attachment->save();

                        //dd($f->getMimeType());
                        switch ($f->getMimeType()) {
                            case 'application/pdf':
                                $xblob = file_get_contents($f->getRealPath());
                                $img = new \Imagick();
                                $img->readImageBlob($xblob);
                                $img->setIteratorIndex(0);
                                $img->setImageFormat('png');
                                $img->setbackgroundcolor('rgb(64, 64, 64)');
                                $img->thumbnailImage(300, 300, true, true);
                                Storage::put($xpath.'thumbnail-'.$attachment->id.'.png', $img);
                                $attachment->thumb_path = $xpath.'thumbnail-'.$attachment->id.'.png';

                                break;
                            case 'image/jpeg':
                            case 'image/png':
                                $xblob = file_get_contents($f->getRealPath());
                                $img = new \Imagick();
                                $img->readImageBlob($xblob);
                                $img->setImageFormat('png');
                                $img->setbackgroundcolor('rgb(64, 64, 64)');
                                $img->thumbnailImage(300, 300, true, true);
                                Storage::put($xpath.'thumbnail-'.$attachment->id.'.png', $img);
                                $attachment->thumb_path = $xpath.'thumbnail-'.$attachment->id.'.png';
                                break;
                            default:
                                $attachment->thumb_path = null;
                                break;
                        }
                        $attachment->save();
                    }

                    //add work_orders
                    $num_wo = rand(1, 3);
                    factory(App\WorkOrder::class, $num_wo)->create(['job_id' => $j->id])->each(function ($wo) {
                        $num_attchments = rand(1, 3);
                        $faker = Faker::create();
                        for ($i = 1; $i < $num_attchments; $i++) {
                            $attachment = new App\Attachment();
                            $file_name = 'lorem'.sprintf('%03d', rand(1, 20)).'.pdf';
                            $f = new Illuminate\Http\UploadedFile(public_path('pdf/'.$file_name), $file_name);
                            $attachment_types = array_flatten(App\AttachmentType::get()->pluck('slug')->toArray());
                            $xtype = $faker->randomElements($attachment_types);

                            $attachment->type = $xtype[0];
                            $attachment->description = strtoupper($faker->text(150));
                            $attachment->original_name = $f->getClientOriginalName();
                            $attachment->file_mime = $f->getMimeType();
                            $attachment->file_size = $f->getSize();
                            $attachment->user_id = $wo->job->client->admin_user->id;
                            $wo->attachments()->save($attachment);
                            $attachment->save();

                            $xfilename = 'attachment-'.$attachment->id.'.'.$f->guessExtension();
                            $xpath = 'attachments/workorders/'.$wo->id.'/';
                            $f->storeAs($xpath, $xfilename);
                            $attachment->file_path = $xpath.$xfilename;
                            $attachment->save();

                            //dd($f->getMimeType());
                            switch ($f->getMimeType()) {
                                case 'application/pdf':
                                    $xblob = file_get_contents($f->getRealPath());
                                    $img = new \Imagick();
                                    $img->readImageBlob($xblob);
                                    $img->setIteratorIndex(0);
                                    $img->setImageFormat('png');
                                    $img->setbackgroundcolor('rgb(64, 64, 64)');
                                    $img->thumbnailImage(300, 300, true, true);
                                    Storage::put($xpath.'thumbnail-'.$attachment->id.'.png', $img);
                                    $attachment->thumb_path = $xpath.'thumbnail-'.$attachment->id.'.png';

                                    break;
                                case 'image/jpeg':
                                case 'image/png':
                                    $xblob = file_get_contents($f->getRealPath());
                                    $img = new \Imagick();
                                    $img->readImageBlob($xblob);
                                    $img->setImageFormat('png');
                                    $img->setbackgroundcolor('rgb(64, 64, 64)');
                                    $img->thumbnailImage(300, 300, true, true);
                                    Storage::put($xpath.'thumbnail-'.$attachment->id.'.png', $img);
                                    $attachment->thumb_path = $xpath.'thumbnail-'.$attachment->id.'.png';
                                    break;
                                default:
                                    $attachment->thumb_path = null;
                                    break;
                            }
                            $attachment->save();
                        }
                    });
                });
            }
        });
    }
}
