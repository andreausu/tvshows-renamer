<?php

namespace Usu\TvShowsRenamer;

use Symfony\Component\Yaml\Parser;

class Renamer {

    static public function rename($absoluteFilePath)
    {
        $seriesName = null;
        $seriesSeason = null;
        $seriesEpisode = null;

        $dirName = dirname($absoluteFilePath);

        if (preg_match('/([\w\.\s]+)(?:S(\d{1,3})E(\d{1,3})|(\d{1,3})x(\d{1,3})|(\d{3,5})).+/', $dirName, $matches)) {
            //var_dump($matches);
            $seriesName = preg_replace('/[\s\.]+/', ' ', $matches[1]);
            $mediaInfo = new MediaInfo();
            $series = $mediaInfo->getSeries($seriesName);
            $episodeTitle = $mediaInfo->getEpisodeTitle($series->id, intval($matches[2]), intval($matches[3]));
        } else {
            throw new \UnexpectedValueException('Unrecognized format! ' . $dirName);
        }

        $newFileName = $series->name . ' S' . $matches[2] . 'E' . $matches[3] . ' - ' . ucfirst(strtolower($episodeTitle)) . '.' . pathinfo($absoluteFilePath, PATHINFO_EXTENSION);

        $baseDir = pathinfo($absoluteFilePath, PATHINFO_DIRNAME);
        $fs = new \Symfony\Component\Filesystem\Filesystem();

        $yaml = new Parser();

        $config = $yaml->parse(file_get_contents(__DIR__ . '/../../../config/parameters.yml'));
        $newFolder = $config['fs']['series_folder'] . '/' . $series->name . '/Season ' . intval($matches[2]);

        if ($fs->exists($config['fs']['series_folder'])) {
            /*if (!$fs->exists($config['fs']['series_folder'] . '/' . $series->name)) {
                mkdir($config['fs']['series_folder'] . '/' . $series->name, 0777, true);
            }*/
            if (!file_exists($newFolder)) {
                mkdir($newFolder, 0777, true);
            }
            $fs->rename($absoluteFilePath, $config['fs']['series_folder'] . '/' . $series->name . '/Season ' . intval($matches[2]) . '/' . $newFileName);
        }

        $files = scandir($baseDir);
        foreach ($files as $file) {
            if (preg_match('/\.srt/i', $file)) {
                echo 'rinominato!';
                $fs->rename($baseDir . '/' . $file, $newFolder . '/' . $series->name . ' S' . $matches[2] . 'E' . $matches[3] . ' - ' . ucfirst(strtolower($episodeTitle)) . '.srt');
            }
        }

        return $newFileName;
    }
}