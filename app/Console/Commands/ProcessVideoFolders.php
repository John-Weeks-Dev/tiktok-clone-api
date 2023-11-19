<?php



// app/Console/Commands/ProcessVideoFolders.php

namespace App\Console\Commands;

use App\Http\Controllers\Api\PostController;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class ProcessVideoFolders extends Command
{
    protected $signature = 'process:video-folders {folderPath}';
    protected $description = 'Create users and upload videos for each folder and subfolder';

    public $lastFileName = [];

    public function handle()
    {
        $folderPath = $this->argument('folderPath');
        $this->processFolder($folderPath);
        $this->info("Finished processing folders.");
        return Command::SUCCESS;
    }

    protected function processFolder($folderPath)
    {
        // Create a user from the base folder if it contains files
        $files = Storage::disk('local')->files($folderPath);
        if (!empty($files)) {
            $originalFolderName = basename($folderPath);
            $user = $this->createUserFromFolderName($folderPath, $originalFolderName);
            $this->processFiles($folderPath, $user);
        }

        // Recursive function to process all subdirectories
        $subdirectories = Storage::disk('local')->directories($folderPath);
        if (!empty($subdirectories)) {
            foreach ($subdirectories as $subdirectory) {
                $originalFolderName = basename($folderPath);
                $folderName = basename($subdirectory);
                $user = $this->createUserFromFolderName($folderPath, $originalFolderName);
                $this->processFolder($subdirectory); // Recursive call
            }
        }

    }

    protected function createUserFromFolderName($folderPath, $originalFolderName)
    {
        // Ensure username is in a suitable format for email, e.g., no spaces or special characters
        $baseEmail = strtolower(trim($originalFolderName)) . '@example.com';
        $user = User::where('email', 'LIKE', $baseEmail . '%')->latest('email')->first();

        if ($user && $user->posts()->count() < 14) {
            // The original user has less than 14 posts
            return $user;
        } else {
            // The original user has 14 posts or doesn't exist, so create a new user appended with a random string
            $randomStr = Str::random(5); // Generate a random string
            $email = strtolower(trim($originalFolderName)) . '+' . $randomStr . '@example.com'; // Append the random string to the email

            return User::create([
                'email' => $email,
                'name' => $originalFolderName . ' ' . $randomStr, // Append the random string to the name
                'password' => Hash::make(Str::random(16)), // Secure random password
            ]);
        }
    }

    protected function processFiles($directory, User $user)
    {
        $files = Storage::disk('local')->files($directory);
        $currentPostCount = null;
        foreach ($files as $file) {
            if ($this->isVideo($file)) {
                var_dump($file);
                // Check if the current user has not exceeded the post limit

                    // Once the limit is reached, create a new unique user account
                    $originalFolderName = basename($directory); // Assuming each directory is intended to be a unique user
                    $user = $this->createUserWithRandomAppend($originalFolderName); // Creates a new user
                    $currentPostCount = 0; // Reset the current post count for the new user

                    // Then upload the video to the new user
                    $this->uploadVideo($file, $user);
                    $currentPostCount++;
                }
            }
        }


    protected function createUserWithRandomAppend($originalFolderName)
    {
        // Append a random string to the original folder name to create a unique email address
        $randomStr = Str::random(5);
        $email = strtolower(trim($originalFolderName)) . '+' . $randomStr . '@example.com';

        // Create and return the new user
        return User::create([
            'email' => $email,
            'name' => $originalFolderName . ' ' . $randomStr,
            'password' => Hash::make(Str::random(16)),
        ]);
    }

    protected function uploadVideo($filePath, User $user)
    {
        // Extract the filename from the file path
        $filename = pathinfo($filePath, PATHINFO_FILENAME);

        if (!isset($this->lastFileName["$filename"])) {

        // Initialize the PostController
        $postController = resolve(PostController::class);

        // Determine the local storage path for the video
        $videoPath = Storage::disk('local')->path($filePath);
            var_dump("upvp".$filename);
            var_dump("upfp".$filePath);



            // Create an UploadedFile object simulating the file upload
        $file = new \Illuminate\Http\UploadedFile($videoPath, $filename, mime_content_type($videoPath), null, true);

        // Construct the POST request for the API endpoint, including the file and payload
        $request = Request::create('/api/posts', 'POST', ['text' => $filename], [], ['video' => $file], ['Accept' => 'application/json']);

        // Authenticate as the user who is 'uploading' the video
        auth()->loginUsingId($user->id);

        // Handle the request via the PostController's store method
        $response = $postController->store($request);

        // Determine the result based on the response status code
        if ($response->getStatusCode() == 200) {
            $this->info("Uploaded video from path: {$filePath}");
        } else {
            $this->error("Failed to upload video from path: {$filePath}");
        }

        $this->lastFileName["$filename"] = true;
        }
    }

    protected function isVideo($filePath)
    {
        return in_array(strtolower(pathinfo($filePath, PATHINFO_EXTENSION)), ['mp4', 'avi', 'mov']);
    }
}


