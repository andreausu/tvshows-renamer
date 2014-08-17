<?php

namespace Usu\TvShowsRenamer;

use Moinax\TvDb\Client;
use Symfony\Component\Yaml\Parser;

class MediaInfo {

    private $client;

    private function getClientInstance() {
        if ($this->client === null) {
            $yaml = new Parser();

            $config = $yaml->parse(file_get_contents(__DIR__ . '/../../../config/parameters.yml'));
            $this->client = new Client('http://thetvdb.com', $config['tvdb']['api_key']);
        }
        return $this->client;
    }

    public function getSeries($seriesName)
    {
        $seriesName = trim($seriesName);
        $tvdb = $this->getClientInstance();
        try {
            $data = $tvdb->getSeries($seriesName);
        } catch(\Exception $e) {
            var_dump($seriesName, $e);
        }

        if (isset($data[0])) {
            return $data[0];
        } else {
            throw new \UnexpectedValueException('Series ' . $seriesName . ' not found!');
        }
    }

    public function getEpisodeTitle($showId, $seasonNumber, $episodeNumber)
    {
        $tvdb = $this->getClientInstance();
        try {
            $episode = $tvdb->getEpisode($showId, $seasonNumber, $episodeNumber, 'en');
        } catch(\Exception $e) {
            var_dump($showId, $seasonNumber, $episodeNumber, $e);
        }

        if (isset($episode) && property_exists($episode, 'name')) {
            return $episode->name;
        } else {
            throw new \UnexpectedValueException('Episode ' . $showId . ' ' . $seasonNumber . ' ' . $episodeNumber . ' not found!');
        }
    }
}