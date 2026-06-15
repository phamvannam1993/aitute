<?php
use Illuminate\Support\Facades\DB;

function uploadYoutube($path, array $data = [], $accessToken = "") {
    $privacyStatus = 'public';
    $OAUTH2_CLIENT_ID = env("GOOGLE_CLIENT_ID");
    $OAUTH2_CLIENT_SECRET = env("GOOGLE_CLIENT_SECRET");
    
    $client = new Google_Client();
    $client->setClientId($OAUTH2_CLIENT_ID);
    $client->setClientSecret($OAUTH2_CLIENT_SECRET);
    $client->setScopes('https://www.googleapis.com/auth/youtube');
    // Define an object that will be used to make all API requests.
    $youtube = new \Google_Service_YouTube($client);
    $client->setAccessToken($accessToken);
    try {
        $video = getVideo($data, $privacyStatus);
   
        // Set the Chunk Size
        $chunkSize = 1 * 1024 * 1024;

        // Set the defer to true
        $client->setDefer(true);

        // Build the request
        $insert = $youtube->videos->insert('status,snippet', $video);

        // Upload
        $media = new \Google_Http_MediaFileUpload(
            $client,
            $insert,
            'video/*',
            null,
            true,
            $chunkSize
        );

        // Set the Filesize
        $media->setFileSize(filesize($path));

        // Read the file and upload in chunks
        $status = false;
        $handle = fopen($path, "rb");

        while (!$status && !feof($handle)) {
            $chunk = fread($handle, $chunkSize);
            $status = $media->nextChunk($chunk);
        }

        fclose($handle);

        $client->setDefer(false);

        // Set ID of the Uploaded Video
        $videoId = $status['id'];
        return ["success" => true, "videoId" => $videoId, "message" => "Đăng bài thành công"];
    }  catch (\Google_Service_Exception $e) {
        $error =  json_decode($e->getMessage(), true);
        return ["success" => false, "message" => $error["error"]["message"], "code" => $error["error"]["code"]];
    } catch (\Google_Exception $e) {
        $error =  json_decode($e->getMessage(), true);
        return ["success" => false, "message" => $error["error"]["message"], "code" => $error["error"]["code"]];
    }
}

function getLatestAccessTokenFromDB($userId)
{
    $latest = DB::table('youtube_access_tokens')->where("user_id", $userId)
                ->latest('created_at')
                ->first();
    if(!$latest) {
        return "";
    }
    return $latest ? (is_array($latest) ? $latest['access_token'] : $latest->access_token ) : null;
}

function getVideo($data, $privacyStatus, $id = null)
{
    // Setup the Snippet
    $snippet = new \Google_Service_YouTube_VideoSnippet();

    if (array_key_exists('title', $data))       $snippet->setTitle($data['title']);
    if (array_key_exists('description', $data)) $snippet->setDescription($data['description']);
    if (array_key_exists('tags', $data))        $snippet->setTags($data['tags']);
    if (array_key_exists('category_id', $data)) $snippet->setCategoryId($data['category_id']);

    // Set the Privacy Status
    $status = new \Google_Service_YouTube_VideoStatus();
    $status->privacyStatus = $privacyStatus;

    // Set the Snippet & Status
    $video = new \Google_Service_YouTube_Video();
    if ($id)
    {
        $video->setId($id);
    }

    $video->setSnippet($snippet);
    $video->setStatus($status);

    return $video;
}