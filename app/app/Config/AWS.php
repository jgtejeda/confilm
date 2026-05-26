<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class AWS extends BaseConfig
{
    public string $region;
    public string $bucket;
    public string $key;
    public string $secret;
    public string $prefix;   // carpeta raíz dentro del bucket (ej: comisionfilm)

    public function __construct()
    {
        parent::__construct();
        $this->region = (string) env('AWS_REGION', '');
        $this->bucket = (string) env('AWS_S3_BUCKET', '');
        $this->key    = (string) env('AWS_ACCESS_KEY_ID', '');
        $this->secret = (string) env('AWS_SECRET_ACCESS_KEY', '');
        $this->prefix = rtrim((string) env('AWS_S3_PREFIX', 'comisionfilm'), '/');
    }
}
