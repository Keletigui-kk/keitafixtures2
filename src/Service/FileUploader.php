<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private SluggerInterface $slugger;

    private string $uploadsDirectory;

    public function __construct(SluggerInterface $slugger, string $uploadsDirectory)
    {
        $this->slugger = $slugger;
        $this->uploadsDirectory = $uploadsDirectory;
    }

    /**
     * Upload a file and its's filename and filepath.
     * 
     * @param UploadedFile $file
     * @param array{fileName: string, filePath: sting}
     */
    public function upload(UploadedFile $file): array
    {
        $fileName = $this->generateUniqFileName($file);

        try{
            $file->move($this->uploadsDirectory, $fileName);
        }catch(FileException $fileException){
            # s'il ya un soucis avec le fichier
            throw $fileException;
        }
        return [
            'fileName' => $fileName,
            'filePath' => $this->uploadsDirectory .$fileName
        ];
    }

    /**
     * Generate a uniq filename a uniq filename for the uploaded file
     * @param UploadedFile $file The uploaded file.
     * @param string The unique filename slugged.
     */
    public function generateUniqFileName(UploadedFile $file): string
    {
        $originaleFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $originaleFileNameSlugged = $this->slugger->slug(strtolower($originaleFileName));
        $randomID = uniqid();
            # retoune les noms de fichiers plus l'extension
        return "{$originaleFileNameSlugged}-{$randomID}-{$file->guessExtension()}";
    }


}