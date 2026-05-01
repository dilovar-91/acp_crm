<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;

class RecordController extends Controller
{
    public function getAudio($id, $recording_id)
    {
        $api_key = config('mango.api_key_' . $id);
        $salt_key = config('mango.api_salt_' . $id);
        $timestamp = Carbon::now()->addDays(1)->timestamp;
        $sign = hash('sha256', $api_key . $timestamp . $recording_id . $salt_key);
        $link = "https://app.mango-office.ru/vpbx/queries/recording/link/" . $recording_id . "/play/" . $api_key . "/" . $timestamp . "/" . $sign;
        $headers = [
            'Content-Type' => 'audio/mpeg',
        ];
        return Response::stream(function () use ($link) {
            $stream = fopen($link, 'rb');
            fpassthru($stream);
            fclose($stream);
        }, 200, $headers);
    }

    public function getAudio2($id, $recording_id)
    {

        $api_key = config('mango.api_key_' . $id);
        $salt_key = config('mango.api_salt_' . $id);
        $timestamp = Carbon::now()->addDays(1)->timestamp;
        $sign = hash('sha256', $api_key . $timestamp . $recording_id . $salt_key);
        $link = "https://app.mango-office.ru/vpbx/queries/recording/link/" . $recording_id . "/play/" . $api_key . "/" . $timestamp . "/" . $sign;
        return redirect()->away($link);
    }

    public function getAudio3($id, $recording_id)
    {

        $api_key = config('mango.api_key_' . $id);
        $salt_key = config('mango.api_salt_' . $id);
        $timestamp = Carbon::now()->addDays(1)->timestamp;
        $sign = hash('sha256', $api_key . $timestamp . $recording_id . $salt_key);
        $link = "https://app.mango-office.ru/vpbx/queries/recording/link/" . $recording_id . "/play/" . $api_key . "/" . $timestamp . "/" . $sign;
        $response = Http::get($link);

        // Check if the request was successful
        if ($response->successful()) {
            $fileContents = $response->body();

            // Determine the desired file name for the download (you can adjust this)
            $fileName = 'mango_record.mp3';

            // Set the appropriate MIME type for an MP3 file
            $mimeType = 'audio/mpeg';

            // Create a response with headers for file download
            return response($fileContents)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', "attachment; filename={$fileName}");
        } else {
            // Return an error response if the request to the remote URL fails
            return response('Error downloading file', 500);
        }
    }
}
