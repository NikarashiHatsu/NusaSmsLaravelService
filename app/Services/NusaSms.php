<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NusaSms {
    /**
     * The encoded base64 file.
     *
     * @var string $base64Media
     */
    private static $base64Media;

    /**
     * The URL of a media.
     *
     * @var string $urlMedia
     */
    private static $urlMedia;

    /**
     * The file name of the encoded base64 file.
     *
     * @var string $fileName
     */
    private static $fileName;

    /**
     * The WA media caption.
     *
     * @var string
     */
    private static $caption;

    /**
     * The WA sender.
     *
     * @var string|null
     */
    private static $sender = null;

    /**
     * The WA message.
     *
     * @var string
     */
    private static $message;

    /**
     * Get the URL of the NusaSMS API.
     *
     * @author Aghits Nidallah https://github.com/NikarashiHatsu
     * @return string
     */
    private static function _getUrl(): string
    {
        return config('nusasms.nusasms_env') == 'production'
            ? config('nusasms.nusasms_url')
            : config('nusasms.nusasms_dev_url');
    }

    /**
     * Get the key of the NusaSMS API.
     *
     * @author Aghits Nidallah https://github.com/NikarashiHatsu
     * @return string
     */
    private static function _getKey(): string
    {
        return config('nusasms.nusasms_env') == 'production'
            ? config('nusasms.nusasms_api_key')
            : config('nusasms.nusasms_dev_key');
    }

    /**
     * Set the media as Base64. Please note that maximum character length
     * encoded is 512 KB.
     *
     * @author Aghits Nidallah https://github.com/NikarashiHatsu
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public static function setMediaAsBase64(Request $request)
    {
        $fileKey = key($request->file());
        $file = $request->file($fileKey);

        $request->validate([
            $fileKey => 'required|file|max:512',
        ]);

        self::$base64Media = base64_encode(file_get_contents($file));
        self::$fileName = $file->getClientOriginalName();
    }

    /**
     * Set the url media.
     *
     * @author Aghits Nidallah https://github.com/NikarashiHatsu
     * @param string $url
     * @return void
     */
    public static function setUrlMedia(string $url)
    {
        self::$urlMedia = $url;
    }

    /**
     * Set the caption.
     *
     * @author Aghits Nidallah https://github.com/NikarashiHatsu
     * @param string $caption
     * @return void
     */
    public static function setCaption(string $caption)
    {
        self::$caption = $caption;
    }

    /**
     * Set the sender. The sender is optional.
     *
     * @author Aghits Nidallah https://github.com/NikarashiHatsu
     * @param string $sender
     * @return void
     */
    public static function setSender(string $sender)
    {
        self::$sender = $sender;
    }

    /**
     * Set the message.
     *
     * @author Aghits Nidallah https://github.com/NikarashiHatsu
     * @param string $message
     * @return void
     */
    public static function setMessage(string $message)
    {
        self::$message = $message;
    }

    /**
     * Get user data using API Key.
     *
     * @author Aghits Nidallah https://github.com/NikarashiHatsu
     * @return array|null
     */
    public static function getUser(): array
    {
        $endpoint = self::_getUrl() . "/nusasms_api/1.0/auth/api_key";
        $http = Http::withHeaders([
                'APIKey' => self::_getKey(),
                'Content-Type' => 'application/json'
            ])
            ->withoutVerifying()
            ->get($endpoint);

        $response = $http->json();

        return $response;
    }

    /**
     * Get Balance Data.
     *
     * @author Aghits Nidallah https://github.com/NikarashiHatsu
     * @return array|null
     */
    public static function getBalance(): array
    {
        $endpoint = self::_getUrl() . "/nusasms_api/1.0/balance";
        $http = Http::withHeaders([
                'APIKey' => self::_getKey(),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])
            ->withoutVerifying()
            ->get($endpoint);

        $response = $http->json();

        return $response;
    }

    /**
     * Send a WhatsApp message with base64 media.
     *
     * @author Aghits Nidallah https://github.com/NikarashiHatsu
     * @param string $destination The phone number, starts with country code, for example '62...'.
     * @return array
     */
    public static function waSendBase64Media($destination): array
    {
        $endpoint = self::_getUrl() . "/nusasms_api/1.0/whatsapp/media";
        $http = Http::withHeaders([
                'APIKey' => self::_getKey(),
                'Content-Type' => 'application/json'
            ])
            ->withoutVerifying()
            ->post($endpoint, [
                'caption' => self::$caption,
                'sender' => self::$sender,
                'destination' => $destination,
                'media_base64' => self::$base64Media,
                'file_name' => self::$fileName,
            ]);

        $response = $http->json();

        return $response;
    }

    /**
     * Send a WhatsApp message with URL media.
     *
     * @author Aghits Nidallah https://github.com/NikarashiHatsu
     * @param string $destination The phone number, starts with country code, for example '62...'.
     * @return array
     */
    public static function waSendUrlMedia($destination): array
    {
        $endpoint = self::_getUrl() . "/nusasms_api/1.0/whatsapp/media";
        $http = Http::withHeaders([
                'APIKey' => self::_getKey(),
                'Content-Type' => 'application/json'
            ])
            ->withoutVerifying()
            ->post($endpoint, [
                'caption' => self::$caption,
                'sender' => self::$sender,
                'destination' => $destination,
                'media_url' => self::$urlMedia,
            ]);

        $response = $http->json();

        return $response;
    }

    /**
     * Send a WhatsApp message.
     *
     * @author Aghits Nidallah https://github.com/NikarashiHatsu
     * @param string $destination The phone number, starts with country code, for example '62...'.
     * @return array
     */
    public static function waSendMessage($destination): array
    {
        $endpoint = self::_getUrl() . "/nusasms_api/1.0/whatsapp/message";
        $http = Http::withHeaders([
                'APIKey' => self::_getKey(),
                'Content-Type' => 'application/json'
            ])
            ->withoutVerifying()
            ->post($endpoint, [
                'destination' => $destination,
                'sender' => self::$sender,
                'message' => self::$message,
            ]);

        $response = $http->json();

        return $response;
    }

    /**
     * Get a message info based on ref_no.
     *
     * @author Aghits Nidallah https://github.com/NikarashiHatsu
     * @param string $ref_no
     * @return array
     */
    public static function waGetMessageInfo($ref_no): array
    {
        $endpoint = self::_getUrl() . "/nusasms_api/1.0/whatsapp/status/" . $ref_no;
        $http = Http::withHeaders([
                'APIKey' => self::_getKey(),
                'Content-Type' => 'application/json'
            ])
            ->withoutVerifying()
            ->get($endpoint);

        $response = $http->json();

        return $response;
    }
}