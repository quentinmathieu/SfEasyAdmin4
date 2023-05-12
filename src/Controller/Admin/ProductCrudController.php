<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ProductCrudController extends AbstractCrudController
{
    public const ACTION_DUPLICATE = "duplicate";
    public const PRODUCT_BASE_PATH = "upload/images/products";
    public const PRODUCT_UPLOAD_DIR = "public/upload/images/products";

    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $duplicate = Action::new(self::ACTION_DUPLICATE)->linkToCrudAction('duplicateProduct');
        return $actions->add(Crud::PAGE_EDIT, $duplicate
    );
    }

    //return the add form for product
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Label')->setRequired(true),
            TextEditorField::new('description'),
            MoneyField::new('price')->setCurrency('EUR'),
            ImageField::new('image')
            ->setBasePath(self::PRODUCT_BASE_PATH)
            ->setUploadDir(self::PRODUCT_UPLOAD_DIR)
            ->setSortable(false),

            BooleanField::new('active'),
            AssociationField::new('category'),
            DateTimeField::new('updatedAt')->hideOnForm(),
            DateTimeField::new('createdAt')->hideOnForm(),

        ];
    }
    

    // add a product to BDD, needed because we have to setCreatedAt
    public function persistEntity(EntityManagerInterface $em, $entityInstance): void
    {

        if(!$entityInstance instanceof Product) return;


        //set the create date
        $entityInstance->setCreatedAt(new \DateTimeImmutable);
        
        parent::persistEntity($em, $entityInstance);
    }


    public function duplicateProduct(AdminContext $context, EntityManagerInterface $em, AdminUrlGenerator $adminUrlGenerator) : Response    {

        /** @var Product $product */
        $product = $context->getEntity()->getInstance();

        //clone the object product
        $duplicatedProduct = clone $product;


        //persist it
        parent::persistEntity($em, $duplicatedProduct);


        //redirect to the clone detail page
        $url = $adminUrlGenerator->setController(self::class)
        ->setAction(Action::DETAIL)
        ->setEntityId($duplicatedProduct->getId())
        ->generateUrl();

        return $this->redirect($url);

    }
}
