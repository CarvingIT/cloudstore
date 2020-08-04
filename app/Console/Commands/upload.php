<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Source;
use App\Drive;
use App\Http\Controllers\GoogleDriveController;

class upload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:local';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload from local directory to Cloud Drive';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sources = Source::where('type','local')->get();
        foreach($sources as $s){
            $drive = Drive::find($s->drive_id);
            $creds = json_decode($s->credentials);
            if($drive->type == 'GoogleDrive'){
                $c = new GoogleDriveController($drive);
                $path = $creds->path;
                echo 'Source: '. $s->name."\nDirectory: ". 
                $path."\nCloud drive: ". $drive->name . " (".$drive->type.")\n";
                // get list of files
                $files = scandir($creds->path);
                foreach($files as $f){
                    $ftype = filetype($path.'/'.$f);
                    //echo $f." - ".$ftype."\n";
                    if($ftype == 'file'){
                        $cloud_file = $c->upload($path.'/'.$f);
                        echo $f ."\t".$cloud_file->id."\n";
                    }
                }
            }
        }
        return 0;
    }
}