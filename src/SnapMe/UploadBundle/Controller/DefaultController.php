<?php

namespace SnapMe\UploadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use SnapMe\UploadBundle\Models\Document;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

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

    public function listImageAction()
    {
        $dir_nom = 'uploads'; // dossier listé (pour lister le répertoir courant : $dir_nom = '.'  --> ('point')
        $dir     = opendir($dir_nom) or die('Erreur de listage : le répertoire n\'existe pas'); // on ouvre le contenu du dossier courant
        $fichier = array(); // on déclare le tableau contenant le nom des fichiers
         
        while($element = readdir($dir)) {
            if($element != '.' && $element != '..' && $element != '.gitkeep') {
                if (!is_dir($dir_nom.'/'.$element)) {
                    $fichier[] = $element;
                }
            }
        }

        return $this->render('SnapMeUploadBundle:Default:list_image.html.twig', array(
            'images' => $fichier,
        ));
    }
    
    public function listImageAdminAction()
    {
        $dir_nom = 'uploads'; // dossier listé (pour lister le répertoir courant : $dir_nom = '.'  --> ('point')
        $dir     = opendir($dir_nom) or die('Erreur de listage : le répertoire n\'existe pas'); // on ouvre le contenu du dossier courant
        $fichier = array(); // on déclare le tableau contenant le nom des fichiers
         
        while($element = readdir($dir)) {
            if($element != '.' && $element != '..' && $element != '.gitkeep') {
                if (!is_dir($dir_nom.'/'.$element)) {
                    $fichier[] = $element;
                }
            }
        }

        return $this->render('SnapMeUploadBundle:Default:list_image_admin.html.twig', array(
            'images' => $fichier,
        ));
    }
    
    public function deleteImageAction($imageName) {
        // supprimer le fichier
        $dir_nom = 'uploads';
        unlink($dir_nom . '/' . $imageName);
        
        // mettre un message de confirmation dans le flashbag
        $this->addFlash('notice', 'We should have deleted your image. But we are not sure.');
        
        // rediriger vers la liste des images ou whatever
        return $this->redirectToRoute('snap_me_upload_list_image_admin');
    }
    
    public function loginAction(Request $request){
        $pwd = $request->request->get('pwd');
        
        if (!empty($pwd)) {
            if ($pwd === 'sécure') {
                $token = new UsernamePasswordToken(
                    'admin',
                    null,
                    'snapme_admin',
                    array('ROLE_ADMIN')
                );
                
                $this->container->get('security.context')->setToken($token);
                $this->get('session')->set('_security_snapme_admin', serialize($token));
                
                return $this->redirectToRoute('snap_me_upload_list_image_admin');
            }
        }
        
        return $this->render('SnapMeUploadBundle:Default:login.html.twig');
    }
    
}