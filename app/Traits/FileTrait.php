<?php

/**
 * Created by VS Code.
 * User: Araf
 * Date: 03//01/2023
 * Time: 12:13 PM
 */

namespace App\Traits;


use Illuminate\Support\Facades\Storage;

trait FileTrait
{
    protected $disk = 'internal';

    /**
     * Uplaod a file
     * @param file - The file instance
     * @param directoryPath - Directory path relative to base upload path
     * @return file path
     */

    protected function uploadDocument($file, $directoryPath, $key=null)
    {
        if($key)
        {
            $originalFilename = auth()->user()->username . "_" . $key . "." . $file->getClientOriginalExtension();
            $path = $file->storeAs(
                $directoryPath,
                $originalFilename,
                ['disk' => $this->disk]
            );
        }else {
            $path = $file->store(
                $directoryPath,
                ['disk' => $this->disk]
            );
        }


        return $path;
    }
    protected function uploadImage($file, $directoryPath, $key)
    {
        $originalFilename = $key . "." . $file->getClientOriginalExtension();
        $path = $file->storeAs(
            $directoryPath,
            $originalFilename,
            ['disk' => $this->disk]
        );
        return $path;
    }
    protected function upload($file, $directoryPath)
    {
        $path = $directoryPath;

        if (!file_exists($path)) {
            mkdir($path);
        }

        $path = $file->store(
            $directoryPath,
            ['disk' => $this->disk]
        );

        return $path;
    }
    /**
     * Download the attachments
     * @param filePath full file path including folder name and file name with extension relative to base path
     * @param displayName name of the downloaded file
     * @return file
     */
    protected function download($filePath, $displayName)
    {
        return Storage::disk($this->disk)->download($filePath, $displayName);
    }

    /**
     * View the file in browser like image or pdf
     * @param filePath full file path including folder name and file name with extension relative to base path
     * @return file
     */
    protected function view($filePath)
    {
        $headers = array(
            'Content-Disposition' => 'inline',
        );
        return Storage::disk($this->disk)->download($filePath, 'file-name', $headers);
    }

    /**
     * @param filePath full file path including folder name and file name with extension relative to base path
     * @return bool
     */
    protected function deleteFile($filePath)
    {
        return Storage::disk($this->disk)->delete($filePath);
    }
    protected function deleteAssetFile($url)
    {
        $documentPath = str_replace(asset('') . config('filesystems.disks.internal.root') . "/", '', $url);
        $this->deleteFile($documentPath);
    }
    

    protected function newUpload($file, $directoryPath, $customFileName = null)
    {
        $path = $directoryPath;

        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $fileName = $customFileName ?: $file->hashName(); // Use custom name if provided, else use the hashed name

        $path = $file->storeAs(
            $directoryPath,
            $fileName,
            ['disk' => $this->disk]
        );

        return $path;
    }
    protected function AssetFileToOriginal($url)
    {
        return  str_replace(asset('') . config('filesystems.disks.internal.root') . "/", '', $url);
    }

     protected function FileCopy($url,$directoryPath)
     {
        $originalPath = $this->AssetFileToOriginal($url);
        $newPath = $directoryPath . "/" . basename($url);
        Storage::disk($this->disk)->copy($originalPath, $newPath);
        return $newPath;
     }
}
