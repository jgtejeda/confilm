<?php

namespace App\Libraries;

use Aws\S3\S3Client;

class S3Service
{
    private S3Client $client;
    private string $bucket;

    public function __construct()
    {
        $awsConfig = config('AWS');
        $this->client = new S3Client([
            'version' => 'latest',
            'region' => $awsConfig->region,
            'credentials' => [
                'key' => $awsConfig->key,
                'secret' => $awsConfig->secret,
            ],
        ]);
        $this->bucket = $awsConfig->bucket;
    }

    public function upload(string $tempPath, string $s3Key, string $mimeType): bool
    {
        try {
            $this->client->putObject([
                'Bucket' => $this->bucket,
                'Key' => $s3Key,
                'SourceFile' => $tempPath,
                'ContentType' => $mimeType,
                'ServerSideEncryption' => 'AES256',
            ]);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'S3Service::upload - ' . $e->getMessage());
            return false;
        }
    }

    public function presignedUrl(string $s3Key, int $minutes = 15): string
    {
        try {
            $cmd = $this->client->getCommand('GetObject', [
                'Bucket' => $this->bucket,
                'Key'    => $s3Key,
            ]);
            // '+15 minutes' → strtotime() relativo al momento de firma
            // NO usar $minutes * 60: sería timestamp absoluto epoch 900 (año 1970) → URL expirada
            $presignedRequest = $this->client->createPresignedRequest($cmd, "+{$minutes} minutes");
            return (string) $presignedRequest->getUri();
        } catch (\Exception $e) {
            log_message('error', 'S3Service::presignedUrl - ' . $e->getMessage() . ' | Key: ' . $s3Key);
            return '';
        }
    }

    public function archive(string $s3Key): bool
    {
        $ext = pathinfo($s3Key, PATHINFO_EXTENSION);
        $timestamp = time();
        $basePath = pathinfo($s3Key, PATHINFO_DIRNAME);
        $baseName = pathinfo($s3Key, PATHINFO_FILENAME);
        $archivedKey = $basePath . '/' . $baseName . '_archived_' . $timestamp . '.' . $ext;

        try {
            $this->client->copyObject([
                'Bucket' => $this->bucket,
                'Key' => $archivedKey,
                'CopySource' => $this->bucket . '/' . $s3Key,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'S3Service::archive copy - ' . $e->getMessage() . ' | Key: ' . $s3Key);
            return false;
        }

        try {
            $this->client->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $s3Key,
            ]);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'S3Service::archive delete - ' . $e->getMessage() . ' | Original: ' . $s3Key . ' | Archived: ' . $archivedKey);
            return false;
        }
    }

    public function delete(string $s3Key): bool
    {
        try {
            $this->client->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $s3Key,
            ]);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'S3Service::delete - ' . $e->getMessage());
            return false;
        }
    }
}
