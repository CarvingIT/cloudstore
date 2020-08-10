<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Source;
use App\Drive;
use App\UploadRecord;
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
            $details = json_decode($s->details);
            if($drive->type == 'GoogleDrive'){
                $c = new GoogleDriveController($drive);
                $path = $details->path;
                echo 'Source: '. $s->name."\nDirectory: ". 
                $path."\nCloud drive: ". $drive->name . " (".$drive->type.")\n";
                // get list of files
                $files = scandir($details->path);
                foreach($files as $f){
                    $ftype = filetype($path.'/'.$f);
                    //echo $f." - ".$ftype."\n";
                    if($ftype == 'file'){
                        $parent_folder_id = empty($details->cloud_id)? null : $details->cloud_id;
                        $cloud_file = $c->upload($path.'/'.$f, $parent_folder_id); 
                        
                        $upload_entry = new UploadRecord();
                        $upload_entry->path = $path.'/'.$f;
                        $upload_entry->size = filesize($path.'/'.$f);
                        $upload_entry->cloud_file_id = $cloud_file->id;
                        $upload_entry->modification_time = date ("Y-m-d H:i:s.", filemtime($path.'/'.$f));
                        $upload_entry->drive_id = $s->drive_id;
                        $upload_entry->remote_path = $f;
                        $upload_entry->save();
                        echo $f ."\t".$cloud_file->id."\n";
                        print_r($cloud_file->parents)."\n";
                    }
                }
            }
        }
        return 0;
    }
}
