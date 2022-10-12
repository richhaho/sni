<?php

use Illuminate\Database\Seeder;

class DeleteDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('attachments')->truncate();
        $statement = 'ALTER TABLE attachments AUTO_INCREMENT = 1;';
        DB::unprepared($statement);
        Storage::deleteDirectory('attachments');

        DB::table('batch_details')->delete();
        $statement = 'ALTER  TABLE batch_details AUTO_INCREMENT = 1;';
        DB::unprepared($statement);

        DB::table('clients')->delete();
        $statement = 'ALTER TABLE clients AUTO_INCREMENT = 100000;';
        DB::unprepared($statement);

        DB::table('contact_infos')->delete();
        $statement = 'ALTER TABLE  contact_infos AUTO_INCREMENT = 1;';
        DB::unprepared($statement);

        DB::table('entities')->delete();
        $statement = 'ALTER TABLE  entities AUTO_INCREMENT = 1;';
        DB::unprepared($statement);

        DB::table('invoices')->delete();
        $statement = 'ALTER TABLE  invoices AUTO_INCREMENT = 1;';
        DB::unprepared($statement);

        DB::table('invoice_lines')->delete();
        $statement = 'ALTER TABLE  invoice_lines AUTO_INCREMENT = 1;';
        DB::unprepared($statement);

        DB::table('jobs')->delete();
        $statement = 'ALTER TABLE  jobs AUTO_INCREMENT = 1;';
        DB::unprepared($statement);

        DB::table('job_parties')->delete();
        $statement = 'ALTER TABLE  job_parties AUTO_INCREMENT = 1;';
        DB::unprepared($statement);
        Storage::deleteDirectory('jobparties');

        DB::table('job_change_orders')->delete();
        $statement = 'ALTER TABLE  job_change_orders AUTO_INCREMENT = 1;';
        DB::unprepared($statement);

        DB::table('job_payment_histories')->delete();
        $statement = 'ALTER TABLE  job_payment_histories AUTO_INCREMENT = 1;';
        DB::unprepared($statement);

        DB::table('jobs')->where('id', '>', 1)->delete();
        $statement = 'ALTER  TABLE jobs AUTO_INCREMENT = 2;';
        DB::unprepared($statement);

        DB::table('mailing_batches')->delete();
        $statement = 'ALTER  TABLE mailing_batches AUTO_INCREMENT = 1;';
        DB::unprepared($statement);

        DB::table('notes')->delete();
        $statement = 'ALTER  TABLE notes AUTO_INCREMENT = 1;';
        DB::unprepared($statement);

        DB::table('payments')->delete();
        $statement = 'ALTER  TABLE payments AUTO_INCREMENT = 1;';
        DB::unprepared($statement);

        DB::table('pdf_pages')->delete();
        $statement = 'ALTER  TABLE pdf_pages AUTO_INCREMENT = 1;';
        DB::unprepared($statement);

        DB::table('settings__lists')->delete();

        DB::table('users')->where('id', '>', 1)->delete();
        $statement = 'ALTER TABLE users AUTO_INCREMENT = 2;';
        DB::unprepared($statement);

        DB::table('work_orders')->delete();
        $statement = 'ALTER TABLE work_orders AUTO_INCREMENT = 1;';
        DB::unprepared($statement);

        DB::table('work_order_payments')->delete();
        $statement = 'ALTER TABLE work_order_payments AUTO_INCREMENT = 1;';
        DB::unprepared($statement);

        DB::table('work_order_recipients')->delete();
        $statement = 'ALTER TABLE work_order_recipients AUTO_INCREMENT = 1;';
        DB::unprepared($statement);
    }
}
