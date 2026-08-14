<?php

namespace App\Controller\Supplier;

use App\Entity\Supplier\Supplier;
use App\Form\Supplier\SupplierForm;
use App\Service\Core\CoreUtils;
use App\Form\Supplier\SupplierSearchForm;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\Supplier\SupplierRepository;
use App\Service\Supplier\SupplierService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/suppliers', name: 'supplier_')]
class SupplierController extends AbstractController
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    
    #[Route(path: '/', name: 'list')]
    #[IsGranted('ROLE_SUPPLIER_VIEW')] 
    public function list(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $page = $request->get('page', 1);
        $coreRedirect = CoreUtils::setDefaultOrderInList($request, ['name'=>'ASC']);
        if($coreRedirect !== null) {
            return $coreRedirect;
        }

        $searchFormData = [];

        $form = $this->createForm(SupplierSearchForm::class, null, ['csrf_protection' => false]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $searchFormData = $form->getData();
        }

        $allGetParams = $request->query->all();
        $sortQuery = [];
        if(!empty($allGetParams[CoreUtils::$SORT_LIST_QUERY_NAME])) {
            $sortQuery = $allGetParams[CoreUtils::$SORT_LIST_QUERY_NAME];
        }
        if($sortQuery) {
            $searchFormData[CoreUtils::$SORT_LIST_QUERY_NAME] = $sortQuery;
        }

        /** @var SupplierRepository $mainRepository */
        $mainRepository = $em->getRepository(Supplier::class);

        $rows = $paginator->paginate(
            $mainRepository->getPaginatedQuery($searchFormData),
            $page,
            $request->query->get('ipp') ?: $this->getParameter('datatable_ipp_default')
        );

        return $this->render('supplier/index.html.twig', [
            'rows' => $rows,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/create', name: 'create')]
    #[IsGranted('ROLE_SUPPLIER_CREATE')] 
    public function create(Request $request, EntityManagerInterface $em)
    {
        $row = new Supplier();

        $form = $this->createForm(SupplierForm::class, $row);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $em->persist($row);
                    $em->flush();
                    $this->addFlash('success', $this->translator->trans('core.layout.successCreateFlashMsg'));
                    if ($form->get('save')->isClicked()) {
                        return CoreUtils::redirect($request, $this->redirectToRoute('supplier_list'));
                    }
                } catch (\Exception $e) {
                    throw $e;
                }
            }
        }
        return $this->render('supplier/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}/edit', name: 'edit')]
    #[IsGranted('ROLE_SUPPLIER_EDIT')] 
    public function edit(int $id, Request $request, EntityManagerInterface $em)
    {
        /** @var Supplier $row */
        $row = $em->getRepository(Supplier::class)->find($id);

        if ($row == null) {
            throw $this->createNotFoundException('The record does not exist');
        }

        $form = $this->createForm(SupplierForm::class, $row);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('delete')->isClicked()) {
                $em->remove($row);
                try {
                    $em->flush();
                    $this->addFlash('warning', $this->translator->trans('layout.deleteFlashMsg'));
                } catch (ForeignKeyConstraintViolationException $exception) {
                    $this->addFlash('warning', $this->translator->trans('layout.errorDeleteFlashMsg'));
                }
                return $this->redirectToRoute('supplier_list');
            }
            $em->persist($form->getData());
            $em->flush();
            $this->addFlash('success', $this->translator->trans('layout.successEditFlashMsg'));
            if($form->get('save')->isClicked()) {
                return CoreUtils::redirect($request, $this->redirectToRoute('supplier_list'));
            }
        }

        return $this->render('supplier/edit.html.twig', [
            'row' => $row,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/api/form-add-supplier', name: 'api_form_add_supplier')]
    #[IsGranted('ROLE_SUPPLIER_CREATE')] 
    public function getForm(Request $request, EntityManagerInterface $em)
    {
        if($request->get('supplierId')) {
            $row = $em->getRepository(Supplier::class)->find($request->get('supplierId'));
            if(!$row) {
                throw $this->createNotFoundException();
            }
        } else {
            $row = new Supplier();
        }

        $form = $this->createForm(SupplierForm::class, $row);

        return $this->render('supplier/api_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/api/save-add-supplier', name: 'api_save_add_supplier', methods: ["POST"])]
    #[IsGranted('ROLE_SUPPLIER_EDIT')] 
    public function save(Request $request, EntityManagerInterface $em)
    {
        $requestData = $request->request->all();
        $statusCode = 201;
        $formData = [];
        if (isset($requestData['formData'])) {
            parse_str($requestData['formData'], $formData);
        }

        if($request->get('supplierId')) {
            $obj = $em->getRepository(Supplier::class)->find($request->get('supplierId'));
            if(!$obj) {
                throw $this->createNotFoundException();
            }
        } else {
            $obj = new Supplier();
        }

        $form = $this->createForm(SupplierForm::class, $obj);

        if (!empty($formData)) {
            $em->persist($obj);
            $form->submit($formData[$form->getName()]);
            if ($form->isValid()) {
                $em->persist($obj);
                $em->flush();
            } else {
                $statusCode = 400;
            }
        }

        return $this->render('supplier/api_form.html.twig', [
            'form' => $form->createView()
        ], new JsonResponse([], $statusCode));
    }

    #[Route(path: '/api/supplier-list', name: 'api_supplier_list')]
    #[IsGranted('ROLE_SUPPLIER_VIEW')] 
    public function apiSupplierList(EntityManagerInterface $em, Request $request, SessionInterface $session)
    {
        $searchTerm = $request->get('search');
        $page = $request->get('page', 1);
        $limit = 10;

        $qb = $em->createQueryBuilder();
        $qb->select('s')
           ->from(Supplier::class, 's')
           ->where('s.name LIKE :search')
           ->setParameter('search', '%' . $searchTerm . '%')
           ->orderBy('s.name', 'ASC')
           ->setFirstResult(($page - 1) * $limit)
           ->setMaxResults($limit);

        $suppliers = $qb->getQuery()->getResult();

        $results = [];
        foreach ($suppliers as $supplier) {
            $results[] = [
                'id' => $supplier->getId(),
                'text' => $supplier->getName() . ' (' . $supplier->getEek() . ')'
            ];
        }

        return new JsonResponse($results);
    }

    #[Route(path: '/api/supplier/get-company-data', name: 'api_supplier_get_company_data')]
    #[IsGranted('ROLE_SUPPLIER_VIEW')] 
    public function getCompanyData(EntityManagerInterface $em, Request $request, SupplierService $supplierService)
    {
        $eek = $request->get('eek');
        $countryCode = $request->get('countryCode', 'BG');

        return new JsonResponse($this->getCompanyDataFrom3thSystems($eek, $countryCode, $supplierService));
    }

    function getCompanyDataFrom3thSystems($eek, $countryCode, $supplierService)
    {
        try {
            $result = $supplierService->getEek($eek, $countryCode);
            
            if ($result && isset($result->valid) && $result->valid) {
                return [
                    'success' => true,
                    'data' => [
                        'name' => $result->name ?? '',
                        'address' => $result->address ?? '',
                        'countryCode' => $result->countryCode ?? $countryCode,
                        'vatNumber' => $result->vatNumber ?? $eek,
                        'requestDate' => $result->requestDate ?? '',
                        'valid' => $result->valid ?? false
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Invalid VAT number or company not found'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error fetching company data: ' . $e->getMessage()
            ];
        }
    }

    function removeSpecialChar($str)
    {
        $str = preg_replace('/[^A-Za-z0-9\-]/', '', $str);
        return $str;
    }

    #[Route(path: '/api/supplier/compare-company/{id}', name: 'api_supplier_get_compare_company')]
    #[IsGranted('ROLE_SUPPLIER_VIEW')] 
    public function getCompareCompany(EntityManagerInterface $em, Request $request, SupplierService $supplierService, $id)
    {
        $supplier = $em->getRepository(Supplier::class)->find($id);
        
        if (!$supplier) {
            return new JsonResponse(['error' => 'Supplier not found'], 404);
        }

        $eek = $supplier->getEek();
        $countryCode = $supplier->getCountryCode() ?: 'BG';

        if (!$eek) {
            return new JsonResponse(['error' => 'No EEK found for this supplier'], 400);
        }

        try {
            $result = $supplierService->getEek($eek, $countryCode);
            
            if ($result && isset($result->valid) && $result->valid) {
                $comparison = [
                    'supplier' => [
                        'name' => $supplier->getName(),
                        'address' => $supplier->getAddress(),
                        'vat' => $supplier->getVat(),
                        'eek' => $supplier->getEek(),
                        'countryCode' => $supplier->getCountryCode()
                    ],
                    'api' => [
                        'name' => $result->name ?? '',
                        'address' => $result->address ?? '',
                        'vatNumber' => $result->vatNumber ?? '',
                        'countryCode' => $result->countryCode ?? ''
                    ],
                    'differences' => []
                ];

                // Compare fields
                if ($supplier->getName() !== ($result->name ?? '')) {
                    $comparison['differences']['name'] = [
                        'supplier' => $supplier->getName(),
                        'api' => $result->name ?? ''
                    ];
                }

                if ($supplier->getAddress() !== ($result->address ?? '')) {
                    $comparison['differences']['address'] = [
                        'supplier' => $supplier->getAddress(),
                        'api' => $result->address ?? ''
                    ];
                }

                return new JsonResponse($comparison);
            } else {
                return new JsonResponse(['error' => 'Invalid VAT number or company not found'], 400);
            }
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Error fetching company data: ' . $e->getMessage()], 500);
        }
    }

    #[Route(path: '/api/supplier-data-for-invoice/{supplierId}', name: 'api_supplier_get_supplier_data_for_invoice')]
    #[IsGranted('ROLE_SUPPLIER_VIEW')] 
    public function getSupplierDataForInvoice(EntityManagerInterface $em, Request $request, SupplierService $supplierService, $supplierId)
    {
        $supplier = $em->getRepository(Supplier::class)->find($supplierId);
        
        if (!$supplier) {
            return new JsonResponse(['error' => 'Supplier not found'], 404);
        }

        return new JsonResponse([
            'id' => $supplier->getId(),
            'name' => $supplier->getName(),
            'vat' => $supplier->getVat(),
            'eek' => $supplier->getEek(),
            'address' => $supplier->getAddress(),
            'countryCode' => $supplier->getCountryCode(),
            'responsiblePerson' => $supplier->getResponsiblePerson(),
            'email' => $supplier->getEmail(),
            'phone' => $supplier->getPhone()
        ]);
    }
} 