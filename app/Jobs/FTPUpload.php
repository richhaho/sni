<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\MailingBatch;
use App\CompanySetting;
use App\FtpLocation;
use League\Flysystem\Filesystem;
 
// The two filesystem adapters we will use
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Adapter\Ftp as FtpAdapter;
 
// MountManager for quick and easy copying
use League\Flysystem\MountManager;

class FTPUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $batchId;
    protected $location;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($batchId,$location) 
    {
        $this->batchId =$batchId;
        $this->location =$location;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $company = CompanySetting::first();
        $ftplocation = FtpLocation::findOrFail($this->location);
        $batch = MailingBatch::findOrFail($this->batchId);
        $attachment = $batch->attachments->where('original_name','mailing-final-'. $this->batchId .'.pdf')->first();


        $local_adapter = new LocalAdapter(storage_path());
        $local = new Filesystem($local_adapter);

        // And we want to copy it to our FTP
        $ftp_adapter = new FtpAdapter([
         'host' => $company->ftp_host,
         'username' => $company->ftp_user,
         'password' => $company->ftp_password,
        ]);
        $ftp = new Filesystem($ftp_adapter);

        // Mount the two filesystems
        $mountManager = new MountManager([
         'local' => $local,
         'ftp' => $ftp,
        ]);

        // Copy the file from our local disk to the ftp disk
        $mountManager->copy( 'local://app/' . $attachment->file_path, 'ftp:/'.$ftplocation->path . '/' . $attachment->original_name);
        // Copy the file from our local disk to the ftp disk
        //$mountManager->move( 'local://some/file.ext', 'ftp://some/file.ext' );
    }
}
