<?php

namespace App;

class Reader
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("File not found: $filePath");
        }
        $this->filePath = $filePath;
    }

    public function getOperations(): \Generator
    {
        if (($handle = fopen($this->filePath, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                yield $data;
            }
            fclose($handle);
        }
    }
}