<?php

namespace App\Service;

use Symfony\Component\Process\Process;

class Youtube
{
    protected $folder = 'youtube';

    protected $id;

    protected $minio;

    public function __construct(Minio $minio)
    {
        $this->minio = $minio;
    }

    public function setUrl($url)
    {
        $id = $this->getIdFromUrl($url);
        $this->setId($id);

        return $this;
    }

    public function getIdFromUrl($url)
    {
        preg_match('/[a-zA-Z0-9\-_]{11}/m', $url, $match);
        if ([] !== $match && 1 === count($match)) {
            return $match[0];
        }

        parse_str(parse_url($url, PHP_URL_QUERY), $params);
        print_r($params);

        if (!array_key_exists('v', $params)) {
            throw new \Exception("Unable to find video id in link", 1);
        }

        return $params['v'];
    }

    public function setId($id)
    {
        $this->id = str_replace(['_', '-'], '', $id);

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function import()
    {
        $path = $this->check();
        if (false !== $path) {
            return $path;
        }

        $process = new Process("/var/www/bin/youtube-download {$this->getId()}");
        $process->run();
        if (!$process->isSuccessful()) {
            return false;
        }

        $file = $process->getOutput();
        if ("" === $file) {
            return false;
        }

        $this->minio->upload($file, "{$this->folder}/{$file}");

        return $this->check();
    }

    public function stream()
    {
        $path = $this->check();
        if (false === $path) {
            return false;
        }


        return $this->minio->stream($path);
    }

    public function fetchAll()
    {
        $return = [];
        $files = $this->minio->listContents($this->folder, true);
        foreach ($files as $file) {
            $file = $file + [
                'src' => $this->getSourceLink($file['basename'])
            ];

            $return[] = $file;
        }

        return $return;
    }

    protected function getSourceLink($basename)
    {
        return "/stream/youtube/{$basename}";
    }

    private function check()
    {
        $extensions = ['opus', 'ogg'];
        $path = "{$this->folder}/{$this->getId()}";
        foreach ($extensions as $extension) {
            $check = "${path}.{$extension}";
            if ($this->minio->has($check)) {
                return $check;
            }
        }

        if ($this->minio->has($path)) {
            return $path;
        }

        return false;
    }
}
