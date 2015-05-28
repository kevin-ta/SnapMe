<?php

namespace SnapMe\UploadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use SnapMe\UploadBundle\Models\Document;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $file = $request->files->get('img');
            $valid_filetypes = array('jpg', 'png','jpeg');
            
            if ($file instanceof UploadedFile) {
                
                if($file->getError()=='0'){

                    $originalName = explode('.', $file->getClientOriginalName());

                    if (!($file->getSize() < 2000000)) {
                        return $this->render('SnapMeUploadBundle:Default:size.html.twig');

                    }

                    if (!(in_array(strtolower($originalName[sizeof($originalName) - 1]), $valid_filetypes))) {
                        return $this->render('SnapMeUploadBundle:Default:extension.html.twig');
                    }

                    if(!($file->getMimeType()=="image/jpeg" or $file->getMimeType()=="image/png")){
                        return $this->render('SnapMeUploadBundle:Default:mime.html.twig');
                    }

                }
                else{
                    return $this->render('SnapMeUploadBundle:Default:errorupload.html.twig');
                }
            }
            else{
                return $this->render('SnapMeUploadBundle:Default:error.html.twig');
            }
           
            $document = new Document();
            $document->setFile($file);
            $document->setSubDirectory('snapme');
            $document->processFile();
            $uploadedURL = $document->getUploadDirectory() . DIRECTORY_SEPARATOR . $document->getSubDirectory() . DIRECTORY_SEPARATOR . $file->getBasename();

            return $this->render('SnapMeUploadBundle:Default:success.html.twig');

        } else {
            return $this->render('SnapMeUploadBundle:Default:index.html.twig');
        }
    }
}