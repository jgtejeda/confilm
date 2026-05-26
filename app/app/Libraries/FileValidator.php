<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;

class FileValidator
{
    private array $magicBytes = [
        'pdf' => '%PDF-',
        'png' => "\x89PNG",
        'jpg' => "\xFF\xD8\xFF",
        'docx' => "PK\x03\x04",
        'xlsx' => "PK\x03\x04",
        'pptx' => "PK\x03\x04",
    ];

    private array $mimeTypes = [
        'pdf' => 'application/pdf',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
    ];

    /**
     * Valida extensión y tamaño de un archivo subido.
     *
     * $allowedExtensions debe ser un array de extensiones sin punto: ['pdf', 'jpg', 'png']
     * Si el array llega vacío (configuración incorrecta) se permite el archivo.
     */
    public function validate(UploadedFile $file, array $allowedExtensions, int $maxSizeMb): array
    {
        $errors = [];

        // Normalizar: si vienen MIME types (formato legado), convertir a extensión
        $allowedExtensions = $this->normalizeToExtensions($allowedExtensions);

        if (!empty($allowedExtensions)) {
            $ext = strtolower($file->getClientExtension());
            if (!in_array($ext, $allowedExtensions, true)) {
                $errors[] = 'Tipo de archivo no permitido. Tipos aceptados: ' . strtoupper(implode(', ', $allowedExtensions));
            }
        }

        if ($file->getSize() > $maxSizeMb * 1024 * 1024) {
            $errors[] = 'El archivo excede el límite de ' . $maxSizeMb . ' MB';
        }

        return $errors;
    }

    /**
     * Convierte MIME types a extensiones si el array contiene formato legado.
     * ['application/pdf'] → ['pdf']
     * ['pdf']             → ['pdf'] (sin cambio)
     */
    private function normalizeToExtensions(array $types): array
    {
        $mimeToExt = [
            'application/pdf' => 'pdf',
            'image/jpeg'      => 'jpg',
            'image/png'       => 'png',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'       => 'xlsx',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
        ];

        return array_map(function ($t) use ($mimeToExt) {
            $t = trim($t);
            return $mimeToExt[$t] ?? $t;
        }, $types);
    }

    public function checkMagicBytes(string $tempPath, string $ext): bool
    {
        $handle = fopen($tempPath, 'rb');
        if ($handle === false) {
            return false;
        }
        $header = fread($handle, 5);
        fclose($handle);

        return isset($this->magicBytes[$ext]) && str_starts_with($header, $this->magicBytes[$ext]);
    }
}
