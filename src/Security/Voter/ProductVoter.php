<?php

namespace App\Security\Voter;

use App\Entity\Product;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductVoter extends Voter
{
    const EDIT = 'PRODUCT_EDIT';
    const DELETE = 'PRODUCT_DELETE';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
//        if (!in_array($attribute, [self::EDIT, self::DELETE])){
//            return false;
//        }
//
//        if (!$attribute instanceof Product){
//            return false;
//        }

        return in_array($attribute, [self::EDIT, self::DELETE]) && $subject instanceof Product;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // Recuperation de l'utilisateur a partir du token
        $user = $token->getUser();

//        if (!$user instanceof UserInterface) return false;

        // Verifier si l'utilisateur est Admin
//        if ($this->security->isGranted('ROLE_ADMIN')) return true;

        // Vérifier les permission
        switch ($attribute){
            case self::EDIT:
                //Vérifier si l'utilisateur peur éditer
                return $this->canEdit();
                break;
            case self::DELETE:
                //Vérifier si l'utilisateur peur suppreimer
                return $this->canDelete();
                break;

        }
        return $user instanceof UserInterface && $this->security->isGranted('ROLE_ADMIN');
    }

    private function canEdit()
    {
        return $this->security->isGranted('ROLE_PRODUCT_ADMIN');
    }
    private function canDelete()
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }
}