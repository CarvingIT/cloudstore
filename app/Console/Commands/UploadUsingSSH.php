<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Source;
use App\Drive;
use App\UploadRecord;
use App\Http\Controllers\GoogleDriveController;
use Illuminate\Support\Facades\Storage;

class UploadUsingSSH extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:ssh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload from local directory to Cloud Drive';

    protected $temp_dir;
    protected $connection;

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
        $sources = Source::where('type','ssh')->get();
        foreach($sources as $s){
            $details = json_decode($s->details);
            $this->temp_dir = $this->getTempDir();
            $this->setConnection($details);

            $drive = Drive::find($s->drive_id);
            if($drive->type == 'GoogleDrive'){
                $c = new GoogleDriveController($drive);
                $path = $details->path;
                echo 'Source: '. $s->name."\nDirectory: ". 
                $path."\nCloud drive: ". $drive->name . " (".$drive->type.")\n";
                // get list of files
                $files = $this->scanSSHDir($details);
                foreach($files as $f){
                    $source_with_proto = $f;
                    $f = preg_replace('#ssh2.sftp://(\d+)/#','/',$f);
                    $source = $f;
                    $path_reg = '#'.$path.'#';
                    $f = preg_replace($path_reg,'',$f);
                    $tmp_dir = Storage::disk('local')->getAdapter()->getPathPrefix().$this->temp_dir;
                    $local_path = $tmp_dir.$f;
                    $this->makePath($local_path);
                    try{
                        echo "Copying $source to $local_path\n";
                        ssh2_scp_recv($this->connection, $source, $local_path);
                        echo "Uploading $local_path to cloud folder - $details->target_path/\n";
                        $parent_folder_id = empty($details->cloud_id)? null : $details->cloud_id;
                        //echo $parent_folder_id;
                        $cloud_file = $c->upload($local_path, $tmp_dir, $parent_folder_id); 
                        //$cloud_file = $c->upload($local_path); 

                        $upload_entry = new UploadRecord();
                        $upload_entry->path = $source_with_proto; 
                        $upload_entry->size = filesize($local_path);
                        $upload_entry->cloud_file_id = $cloud_file->id;
                        $upload_entry->modification_time = date ("Y-m-d H:i:s.", filemtime($source_with_proto));
                        $upload_entry->drive_id = $s->drive_id;
                        $upload_entry->remote_path = $details->target_path.'/'.$f; 
                        $upload_entry->save();
                        echo $source ."\t".$cloud_file->id."\n";
                    }
                    catch(\Exception $e){
                        echo $e->getMessage()."\n";
                        exit;
                    }
                }
            }
        }
        return 0;
    }

    private function makePath($file_path){
        // separate the file
        $path_els = explode('/', $file_path);
        array_pop($path_els);
        //create the directory
        $new_dir = implode('/',$path_els);
        @mkdir($new_dir, 0755, true);
    }

    private function setConnection($details){
        $connection = ssh2_connect($details->server, $details->port);
        ssh2_auth_password($connection, $details->username, $details->password);
        $this->connection = $connection;
    }

    private function scanSSHDir($details){
        echo "Scanning remote dir for the list of files.\n";
        echo $details->username.'@'.$details->server.":".$details->path."\n";
        $sftp = ssh2_sftp($this->connection);
        $sftp_fd = intval($sftp);
        return $this->getDirContents("ssh2.sftp://$sftp_fd".$details->path);
    }

    private function getDirContents($dir, &$results = array()) {
        $files = scandir($dir);
        foreach ($files as $key => $value) {
            //$path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            $path = $dir . DIRECTORY_SEPARATOR . $value;
            if (!is_dir($path)) {
                $results[] = $path;
            } else if ($value != "." && $value != "..") {
                $this->getDirContents($path, $results);
                //$results[] = $path;
            }
        }
        return $results;
    }

    private function getTempDir(){
        $directory = 'scp_downloads/'.uniqid();
        Storage::makeDirectory($directory);
        return $directory;
    }

    private function copyRemoteFile($source, $target){
        $sftp = ssh2_sftp($this->connection);
        $sftp_fd = intval($sftp);
        //$contents =  
    }
}
