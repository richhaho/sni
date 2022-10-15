<?php

use Illuminate\Database\Seeder;

class FreshDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('attachments')->truncate();
        Storage::deleteDirectory('attachments');

        DB::table('batch_details')->truncate();

        DB::table('clients')->delete();
        $statement = 'ALTER TABLE clients AUTO_INCREMENT = 100000;';
        DB::unprepared($statement);

        DB::table('contact_infos')->delete();
        $statement = 'ALTER TABLE  contact_infos AUTO_INCREMENT = 1;';
        DB::unprepared($statement);

        DB::table('entities')->delete();
        $statement = 'ALTER TABLE  entities AUTO_INCREMENT = 1;';
        DB::unprepared($statement);

        DB::table('ftp_connections')->truncate();
        DB::table('ftp_locations')->truncate();

        DB::table('invoices')->truncate();
        DB::table('invoice_lines')->truncate();

        DB::table('jobs')->truncate();

        DB::table('jobs')->truncate();

        DB::table('job_parties')->truncate();
        Storage::deleteDirectory('jobparties');

        DB::table('job_change_orders')->truncate();

        DB::table('job_payment_histories')->truncate();

        DB::table('mailing_batches')->truncate();
        DB::table('notes')->truncate();

        DB::table('notifications')->truncate();
        DB::table('payments')->truncate();
        DB::table('pdf_pages')->truncate();

        DB::table('queue_jobs')->truncate();

        DB::table('users')->where('id', '>', 1)->delete();
        $statement = 'ALTER TABLE users AUTO_INCREMENT = 2;';
        DB::unprepared($statement);

        DB::table('work_orders')->truncate();

        DB::table('work_order_payments')->truncate();

        DB::table('work_order_recipients')->truncate();
    }
}
