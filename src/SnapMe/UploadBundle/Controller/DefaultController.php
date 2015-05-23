<?php

namespace SnapMe\UploadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use SnapMe\UploadBundle\Models\Document;
use SnapMe\UploadBundle\Entity\Upload;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $file = $request->files->get('img');

            $valid_filetypes = array('jpg', 'png');
            
            if ($file instanceof UploadedFile) {
                
                if($file->getError()=='0'){

                $originalName = explode('.', $file->getClientOriginalName());
                //print_r(' Size : '.$file->getSize());


                if (!($file->getSize() < 2000000)) {
                    //print_r('Size Exceeds Limit');
                    die();
                }

                if (!(in_array(strtolower($originalName[sizeof($originalName) - 1]), $valid_filetypes))) {
                    //print_r('Invalid File Type');
                    die();
                }
            }
            else{
                //print_r('Upload Error Check File Size and Type');
                die();
            }
            }
            else{
                //print_r('Upload Error');
                die();
            }
           
            $document = new Document();
            $document->setFile($file);
            $document->setSubDirectory('myuploads');
            $document->processFile();
            $uploadedURL = $document->getUploadDirectory() . DIRECTORY_SEPARATOR . $document->getSubDirectory() . DIRECTORY_SEPARATOR . $file->getBasename();

            return $this->render('SnapMeUploadBundle:Default:success.html.twig');
        } else {
            return $this->render('SnapMeUploadBundle:Default:index.html.twig');
        }
    }
}