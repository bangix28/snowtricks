<?php


namespace App\services\videoServices;


class videoLinkServices
{
    const YOUTUBE_URL = "https://www.youtube.com/embed/";
    const DAILYMOTION_URL = "https://www.dailymotion.com/embed/video/";

    /**
     * @param $url
     * @return false|string
     */
    public function extractPlatformFromURL($url)
    {
        if (strpos($url, "youtu") !== false) {

            return $this->encodeYoutube($url);

        } elseif (strpos($url, "dailymotion") !== false || strpos($url, "dai.ly") !== false) {

            return $this->encodeDailymotion($url);

        } else {

            return false;
        }

    }

    /**
     * @param $url
     * @return string
     */
    private function encodeYoutube($url)
    {
        if (strpos($url, "v=") !== false) {

            $id = substr($url, strpos($url, "v=") + 2, 11);

        } elseif (strpos($url, "embed/") !== false) {

            $id = substr($url, strpos($url, "embed/") + 6, 11);

        } elseif (strpos($url, "youtu.be/") !== false) {

            $id = substr($url, strpos($url, "youtu.be/") + 9, 11);

        }

        return self::YOUTUBE_URL.$id;
    }

    /**
     * @param $url
     * @return string
     */
    private function encodeDailymotion($url)
    {
        if (strpos($url, "video/")) {

            $id = substr($url, strpos($url, "video/") + 6, 7);

        } elseif (strpos($url, "dai.ly/")) {

            $id = substr($url, strpos($url, "dai.ly/") + 7, 7);

        }

        return self::DAILYMOTION_URL.$id;
    }

}
