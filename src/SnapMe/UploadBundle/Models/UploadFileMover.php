<?php

namespace SnapMe\UploadBundle\Models;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadFileMover {

    public function moveUploadedFile(UploadedFile $file, $uploadBasePath,$relativePath) {
        $originalName = $file->getFilename();
        // use filemtime() to have a more determenistic way to determine the subpath, otherwise its hard to test.
       // $relativePath = date('Y-m', filemtime($file->getPath()));
        $targetFileName = $relativePath . DIRECTORY_SEPARATOR . $originalName;
        $targetFilePath = $uploadBasePath . DIRECTORY_SEPARATOR . $targetFileName;
        $ext = $file->getExtension();
        $i=1;
        while (file_exists($targetFilePath) && md5_file($file->getPath()) != md5_file($targetFilePath)) {
            if ($ext) {
                $prev = $i == 1 ? "" : $i;
                $targetFilePath = $targetFilePath . str_replace($prev . $ext, $i++ . $ext, $targetFilePath);
                
            } else {
                $targetFilePath = $targetFilePath . $i++;
            }
        }


        $targetDir = '/home/cover77/uploads';
        if (!is_dir($targetDir)) {
            $ret = mkdir($targetDir, 777, true);
            if (!$ret) {
                throw new \RuntimeException("Could not create target directory to move temporary file into.");
            }
        }
        $file->move($targetDir, basename($targetFilePath));

        return str_replace($uploadBasePath . DIRECTORY_SEPARATOR, "", $targetFilePath);
    }

}

?>