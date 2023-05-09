<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator)
    {
        
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {

        $url = $this->adminUrlGenerator
        ->setController(ProductCrudController::class)
        ->generateUrl();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($url);

     
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('SfEasyAdmin4');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('E-commerce');
        yield MenuItem::section('Products');
        // yield MenuItem::subMenu('Action', 'fas fa-bars')->setSubItems([
        //     MenuItem::linkToCrud('Add product', 'fa fa-plus', Product::class)->setAction(Crud::PAGE_NEW),
        //     MenuItem::linkToCrud('Show products', 'fa fa-eye', Product::class)
        // ]);

        yield MenuItem::linkToCrud('Show products', 'fa fa-eye', Product::class);
        yield MenuItem::linkToCrud('Add product', 'fa fa-plus', Product::class)->setAction(Crud::PAGE_NEW);
        
        // yield MenuItem::subMenu('Action', 'fas fa-bars')->setSubItems([
            yield MenuItem::linkToCrud('Show categories', 'fa fa-eye', Category::class);
            yield MenuItem::linkToCrud('Add category', 'fa fa-plus', Category::class)->setAction(Crud::PAGE_NEW);
        // ]);
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
