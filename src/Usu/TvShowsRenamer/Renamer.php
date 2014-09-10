<?php

namespace Usu\TvShowsRenamer;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Process\Process;

class Renamer {

    static public function rename($absolutePath)
    {
        $seriesName = null;
        $seriesSeason = null;
        $seriesEpisode = null;

        if (is_file($absolutePath)) {

        } else {
            $files = scandir($absolutePath);
            foreach ($files as $file) {
                if (preg_match('/\.rar/i', $file)) {
                    $process = new Process('unrar e -o- ' . escapeshellarg($absolutePath . '/' . $file) . ' ' . escapeshellarg($absolutePath));
                    $process->run(function ($type, $buffer) {
                        if (Process::ERR === $type) {
                            echo 'ERR > '.$buffer;
                        } else {
                            echo $buffer;
                        }
                    });
                    $files2 = scandir($absolutePath);
                    foreach ($files2 as $f) {
                        if (preg_match('/\.(avi|mp4|mkv)/i', $f)) {
                            $absolutePath = $absolutePath . '/' . $f;
                        }
                    }
                    break;
                }
            }
        }

        $dirName = basename(dirname($absolutePath));

        if (preg_match('/([\w\.\s]+?)(?:S(\d{1,3})E(\d{1,3})|(\d{1,3})x(\d{1,3})|(\d{3,5})).+/', $dirName, $matches)) {
            //var_dump($matches);
            $seriesName = preg_replace('/[\s\.]+/', ' ', $matches[1]);
            $mediaInfo = new MediaInfo();
            $series = $mediaInfo->getSeries($seriesName);
            $episodeTitle = $mediaInfo->getEpisodeTitle($series->id, intval($matches[2]), intval($matches[3]));
        } else {
            throw new \UnexpectedValueException('Unrecognized format! ' . $dirName);
        }

        $newFileName = $series->name . ' S' . $matches[2] . 'E' . $matches[3] . ' - ' . ucfirst(strtolower($episodeTitle)) . '.' . pathinfo($absolutePath, PATHINFO_EXTENSION);

        $yaml = new Parser();

        $config = $yaml->parse(file_get_contents(__DIR__ . '/../../../config/parameters.yml'));
        $destinationFolder = $config['fs']['series_folder'] . '/' . $series->name . '/Season ' . intval($matches[2]);

        if (file_exists($destinationFolder . '/' . $newFileName)) {
            throw new \Exception($destinationFolder . '/' . $newFileName . ' already exists');
        }

        $baseDir = pathinfo($absolutePath, PATHINFO_DIRNAME);
        $fs = new \Symfony\Component\Filesystem\Filesystem();

        if ($fs->exists($config['fs']['series_folder'])) {
            if (!file_exists($destinationFolder)) {
                mkdir($destinationFolder, 0777, true);
            }
            $fs->rename($absolutePath, $config['fs']['series_folder'] . '/' . $series->name . '/Season ' . intval($matches[2]) . '/' . $newFileName);
        }

        $files = scandir($baseDir);
        foreach ($files as $file) {
            if (preg_match('/\.srt/i', $file)) {
                $fs->rename($baseDir . '/' . $file, $destinationFolder . '/' . $series->name . ' S' . $matches[2] . 'E' . $matches[3] . ' - ' . ucfirst(strtolower($episodeTitle)) . '.srt');
            }
        }

        return $newFileName;
    }
}