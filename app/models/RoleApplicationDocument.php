<?php
/**
 * RoleApplicationDocument Model
 * Stores supporting documents uploaded with employee role applications.
 */
class RoleApplicationDocument extends Model
{
    protected string $table = 'role_application_documents';

    /** Human-readable labels for each document type */
    public const TYPES = [
        'resume'            => 'Resume / CV',
        'biodata'           => 'Biodata (Philippine Format)',
        'birth_certificate' => 'Birth Certificate (PSA)',
        'government_id'     => 'Government-Issued ID',
        'certificate'       => 'Certificate / Diploma',
        'other'             => 'Other Supporting Document',
    ];

    /** Allowed MIME types for uploaded documents */
    public const ALLOWED_MIME = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    /** Max file size: 10 MB */
    public const MAX_SIZE = 10 * 1024 * 1024;

    /**
     * Get all documents for a given application.
     */
    public function getByApplication(int $applicationId): array
    {
        return $this->query(
            "SELECT * FROM role_application_documents
             WHERE application_id = ?
             ORDER BY created_at ASC",
            [$applicationId]
        )->fetchAll();
    }

    /**
     * Attach a document record to an application.
     */
    public function attach(int $applicationId, string $docType, string $fileName, string $originalName, int $fileSize, string $fileType): int|false
    {
        if (!array_key_exists($docType, self::TYPES)) {
            $docType = 'other';
        }
        return $this->insert([
            'application_id' => $applicationId,
            'document_type'  => $docType,
            'file_name'      => $fileName,
            'file_original'  => htmlspecialchars($originalName, ENT_QUOTES, 'UTF-8'),
            'file_size'      => $fileSize,
            'file_type'      => $fileType,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);
    }
}
