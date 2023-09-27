<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureService
{
    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function add(UploadedFile $picture, ?string $folder = '', ?int $width = 250, ?int $height = 250)
    {
        // Donner un nouveau nom à l'image
        $fichier = md5(uniqid(rand(), true)) . '.webp';

        //Récupèrer les infos de l'image
        $pictureInfo = getimagesize($picture);

        if ($pictureInfo === false){
            throw new \Exception('Format de l\'image est incorrect');
        }

        // Verifier le format de l'image
        switch($pictureInfo['mime']){
            case 'image/png':
                $pictureSource = imagecreatefrompng($picture);
                break;
            case 'image/jpeg':
                $pictureSource = imagecreatefromjpeg($picture);
                break;
            case 'image/webp':
                $pictureSource = imagecreatefromwebp($picture);
                break;
            case 'image/wbmp':
                $pictureSource = imagecreatefromwbmp($picture);
                break;
            default:
                throw new \Exception('Format de l\'image est incorrect');
        }

        // Recadrer l'image et récupèrer les dimentions
        $pictureWidth = $pictureInfo[0];
        $pictureHeidth = $pictureInfo[1];

        // Vérifier l'orientation de l'image
        switch ($pictureWidth <=> $pictureHeidth){
            case -1: // Portrait
                $squareSize = $pictureWidth;
                $srcX = 0;
                $srcY = ($pictureHeidth - $squareSize) / 2;
                break;
            case 0: // Carré
                $squareSize = $pictureWidth;
                $srcX = 0;
                $srcY = 0;
                break;
            case 1: // Paysage
                $squareSize = $pictureHeidth;
                $srcX = ($pictureWidth - $squareSize) / 2;
                $srcY = 0;
                break;
        }

        // Création d'une nouvelle image vierge
        $resizedPicture = imagecreatetruecolor($width, $height);

        imagecopyresampled($resizedPicture, $pictureSource, 0, 0, $srcX, $srcY, $width, $height, $squareSize, $squareSize);

        // Création du chemin
        $path = $this->parameterBag->get('pictures_directory') . $folder;

        // Création du dossier de destination, s'il n'existe pas
        if (!file_exists($path . '/mini/')){
            mkdir($path . '/mini/', 0755, true);
        }

        // Stocker l'image recadrée
        imagewebp($resizedPicture, $path . '/mini/' . $width . 'x' . $height . '-' . $fichier);

        $picture->move($path . '/', $fichier);

        return $fichier;
    }

    public function delete(string $fichier, ?string $folder = '', ?int $width = 250, ?int $height = 250): bool
    {
        if ($fichier !== 'default.webp'){
            $success = false;
            $path = $this->parameterBag->get('pictures_directory') . $folder;

            $mini = $path . '/mini/' . $width . 'x' . $height . '-' . $fichier;

            if (file_exists($mini)){
                unlink($mini);
                $success = true;
            }

            $original = $path . '/' .$fichier;

            if (file_exists($original)){
                unlink($original);
                $success = true;
            }

            return $success;
        }

        return false;
    }
}