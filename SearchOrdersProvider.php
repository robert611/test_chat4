<?php

namespace managerBundle\BaseLinker\EasyItem;

use Doctrine\ORM\EntityManagerInterface;
use managerBundle\Entity\BaseLinkerCatalogs;
use managerBundle\Entity\BaseLinkerCatalogUsers;
use managerBundle\Entity\BaseLinkerClients;
use managerBundle\Entity\BaseLinkerOrders;
use managerBundle\Entity\BaseLinkerOrdersProducts;
use managerBundle\Entity\BaseLinkerShipments;
use managerBundle\Entity\User;
use managerBundle\Repository\BaseLinkerOrdersProductsRepository;
use managerBundle\Repository\BaseLinkerOrdersRepository;
use managerBundle\Services\Company\CompanyService;

/** @codingStandardsIgnoreStart */
class SearchOrdersProvider
{
    protected int $limit = 25;
    protected string $orderField = 'send_datetime';
    protected string $orderDirection = 'DESC';

    protected EntityManagerInterface $entityManager;
    protected BaseLinkerOrdersRepository $baseLinkerOrdersRepository;
    protected BaseLinkerOrdersProductsRepository $productsRepository;
    private CompanyService $companyService;
    private array $parameters = [];
    private array $ordersIds = [];
    private array $result = [];
    private bool $onlyPreBuffer = false;
    private ?User $user;
    private array $catalogs = [];
    private int $recordsNumber;

    public function __construct(
        EntityManagerInterface $entityManager,
        BaseLinkerOrdersRepository $baseLinkerOrdersRepository,
        BaseLinkerOrdersProductsRepository $productsRepository,
        CompanyService $companyService,
    ) {
        $this->entityManager = $entityManager;
        $this->baseLinkerOrdersRepository = $baseLinkerOrdersRepository;
        $this->productsRepository = $productsRepository;
        $this->companyService = $companyService;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    public function setOnlyPreBuffer(): static
    {
        $this->onlyPreBuffer = true;

        return $this;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function search(array $parameters): array
    {
        $this->map($parameters)->reduce();

        return [
            'result' => $this->result,
            'number' => $this->recordsNumber,
            'limit' => $this->limit,
            'page' => $this->parameters['page_number'] ?? 1
        ];
    }

    private function reduce(): void
    {
        // Inside of this function is done, you fetch order from $this->ordersIds and once again check all of these filters
        sort($this->ordersIds);
        foreach ($this->ordersIds as $key => $orderId) {
            if (count($this->result) >= $this->limit) {
                break;
            }

            if ($this->parameters['page_number'] > 1 && $key <= ($this->parameters['page_number'] - 2) * $this->limit) {
                continue;
            }

            $order = $this->baseLinkerOrdersRepository->findWithRelatives($orderId);

            if (
                isset($this->parameters['clientEmail'])
                && $this->parameters['clientEmail']
                && !str_contains(strtolower($order->getClient()->getEmail()),
                    strtolower($this->parameters['clientEmail']))
            ) {
                continue;
            }
            if (
                isset($this->parameters['clientFullname'])
                && $this->parameters['clientFullname']
                && !str_contains(strtolower($order->getClient()->getFullname()),
                    strtolower($this->parameters['clientFullname']))
            ) {
                continue;
            }
            if (
                isset($this->parameters['clientCity'])
                && $this->parameters['clientCity']
                && !str_contains(strtolower($order->getClient()->getCity()),
                    strtolower($this->parameters['clientCity']))
            ) {
                continue;
            }
            if (
                isset($this->parameters['clientPostCode'])
                && $this->parameters['clientPostCode']
                && !str_contains(strtolower($order->getClient()->getPostCode()),
                    strtolower($this->parameters['clientPostCode']))
            ) {
                continue;
            }
            if (
                isset($this->parameters['clientPhoneNumber'])
                && $this->parameters['clientPhoneNumber']
                && !str_contains(strtolower($order->getClient()->getPhone()),
                    strtolower($this->parameters['clientPhoneNumber']))
            ) {
                continue;
            }
            if (
                isset($this->parameters['orderId'])
                && $this->parameters['orderId']
                && !str_contains(strtolower($order->getOrderId()), strtolower($this->parameters['orderId']))
            ) {
                continue;
            }
            if (
                isset($this->parameters['shipmentNumber'])
                && $this->parameters['shipmentNumber']
                && !str_contains(strtolower($order->getShipment()->getNumber()),
                    strtolower($this->parameters['shipmentNumber']))
            ) {
                continue;
            }
            if (
                isset($this->parameters['dateFrom']) && $this->parameters['dateFrom']
                && $this->parameters['dateFrom']
                < $order->getOrderDatetime()->format('Y-m-d')
            ) {
                continue;
            }
            if (
                isset($this->parameters['dateTo']) && $this->parameters['dateTo']
                && $this->parameters['dateTo']
                > $order->getOrderDatetime()->format('Y-m-d')
            ) {
                continue;
            }
            if (
                isset($this->parameters['sendDateFrom'])
                && $this->parameters['sendDateFrom']
                && $order->getSendDatetime()
                && $this->parameters['sendDateFrom'] < $order->getSendDatetime()->format('Y-m-d')
            ) {
                continue;
            }
            if (
                isset($this->parameters['sendDateTo'])
                && $this->parameters['sendDateTo']
                && $this->parameters['sendDateTo'] > $order->getSendDatetime()->format('Y-m-d')
            ) {
                continue;
            }
            if (isset($this->parameters['productName']) && $this->parameters['productName']) {
                $continue = true;

                foreach ($order->getProducts() as $product) {
                    if (str_contains(strtolower($product->getTitle()), strtolower($this->parameters['productName']))) {
                        $continue = false;
                    }
                }

                if ($continue) {
                    continue;
                }
            }
            if (isset($this->parameters['status']) && $this->parameters['status']) {
                if ($order->getStatus() && $order->getDirectoryId() !== $this->parameters['status']->getCatalogId()) {
                    continue;
                } else {
                    if (!$order->getStatus() && $this->parameters['status']) {
                        continue;
                    }
                }
            }
            if ($this->onlyPreBuffer && $this->user) {
                $preBuffers = [];

                foreach (
                    $this->entityManager->getRepository(BaseLinkerCatalogUsers::class)->findBy([
                        'user' => $this->user->getId(),
                        'type' => 'pre_buffer'
                    ]) as $preBuffer
                ) {
                    $preBuffers[] = $preBuffer->getCatalog();
                }

                if (!in_array($order->getDirectoryId(), $preBuffers)) {
                    continue;
                }
            }
            if (
                isset($this->parameters['assigned_user'])
                && $this->parameters['assigned_user']
                && $order->getAssignedUser()->getId() !== $this->parameters['assigned_user']->getId()
            ) {
                continue;
            }

            $this->result[] = $order;
        }

        $this->recordsNumber = count($this->ordersIds);
    }

    private function map(array $parameters): SearchOrdersProvider
    {
        $this->parameters = $parameters;

        if (isset($this->parameters['findAll']) && $this->parameters['findAll']) {
            $this->addAllUserPreBuffer();
        }
        if (isset($parameters['clientEmail']) && $parameters['clientEmail']) {
            $this->addOrdersByClientEmail($parameters['clientEmail']);
        }
        if (isset($parameters['clientFullname']) && $parameters['clientFullname']) {
            $this->addOrdersByClientFullname($parameters['clientFullname']);
        }
        if (isset($parameters['login']) && $parameters['login']) {
            $this->addOrdersByClientLogin($parameters['login']);
        }
        if (isset($parameters['clientCity']) && $parameters['clientCity']) {
            $this->addOrdersByClientCity($parameters['clientCity']);
        }
        if (isset($parameters['clientPostCode']) && $parameters['clientPostCode']) {
            $this->addOrdersByClientPostCode($parameters['clientPostCode']);
        }
        if (isset($parameters['clientPhoneNumber']) && $parameters['clientPhoneNumber']) {
            $this->addOrdersByClientPhoneNumber($parameters['clientPhoneNumber']);
        }
        if (isset($parameters['orderId']) && $parameters['orderId']) {
            $this->addOrdersByOrderId($parameters['orderId']);
        }
        if (isset($parameters['shipmentNumber']) && $parameters['shipmentNumber']) {
            $this->addOrdersByShipmentNumber($parameters['shipmentNumber']);
        }
        if (isset($parameters['productName']) && $parameters['productName']) {
            $this->addOrdersByProductName($parameters['productName']);
        }
        if (isset($parameters['status']) && $parameters['status']) {
            $this->addOrdersByStatus($parameters['status']);
        }
        if (isset($parameters['assigned_user']) && $parameters['assigned_user']) {
            $this->addOrdersByAssignedUser($parameters['assigned_user']);
        }
        if (isset($parameters['dateFrom'])) {
            $parameters['dateTo'] = $parameters['dateTo'] ?? date('Y-m-d');
            $this->addOrdersByOrderDates($parameters['dateFrom'], $parameters['dateTo']);
        }
        if (isset($parameters['dateTo'])) {
            $parameters['dateFrom'] = $parameters['dateFrom'] ?? '1970-01-01';
            $this->addOrdersByOrderDates($parameters['dateFrom'], $parameters['dateTo']);
        }
        if (isset($parameters['sendDateFrom'])) {
            $parameters['sendDateTo'] = $parameters['sendDateTo'] ?? date('Y-m-d');
            $this->addOrdersBySendDates($parameters['sendDateFrom'], $parameters['sendDateTo']);
        }
        if (isset($parameters['sendDateTo'])) {
            $parameters['sendDateFrom'] = $parameters['sendDateFrom'] ?? '1970-01-01';
            $this->addOrdersBySendDates($parameters['sendDateFrom'], $parameters['sendDateTo']);
        }
        if (isset($parameters['statusDateFrom'])) {
            $parameters['statusDateTo'] = $parameters['statusDateTo'] ?? date('Y-m-d');
            $this->addOrdersByStatusDates($parameters['statusDateFrom'], $parameters['statusDateTo']);
        }
        if (isset($parameters['statusDateTo'])) {
            $parameters['statusDateFrom'] = $parameters['statusDateFrom'] ?? '1970-01-01';
            $this->addOrdersByStatusDates($parameters['statusDateFrom'], $parameters['statusDateTo']);
        }

        return $this;
    }

    private function addAllUserPreBuffer(): void
    {
        // DONE
        if ($this->setCatalogs($this->user)) {
            $ordersIds = $this->baseLinkerOrdersRepository->getOrdersIdsByCatalogs(
                $this->catalogs,
                $this->orderDirection
            );

            foreach ($ordersIds as $result) {
                $this->addOrderId($result['id']);
            }
        }
    }

    public function setCatalogs(User $user): SearchOrdersProvider
    {
        // DONE, because of user variable from function argument, which is actually $this->getUser() from controller
        if ($this->onlyPreBuffer && $this->user) {
            $preBuffers = $this->entityManager->getRepository(BaseLinkerCatalogUsers::class)->findBy([
                'user' => $user->getId(),
                'type' => 'pre_buffer'
            ]);

            foreach ($preBuffers as $preBuffer) {
                $this->catalogs[] = $preBuffer->getCatalog();
            }
        }

        return $this;
    }

    private function addOrderId(int $orderId): void
    {
        if (!in_array($orderId, $this->ordersIds)) {
            $this->ordersIds[] = $orderId;
        }
    }

    private function addOrdersByClientEmail(string $email): void
    {
        // DONE
        $orders = $this->entityManager->createQueryBuilder()
            ->from(BaseLinkerOrders::class, 'o')
            ->select('o.id')
            ->leftJoin('o.baseLinkerAccount', 'a')
            ->innerJoin(BaseLinkerClients::class, 'c', 'WITH', 'o.client = c.id')
            ->where('a.company = :company')
            ->andWhere('c.email like :email')
            ->setParameter('company', $this->companyService->getLoggedUserCompany())
            ->setParameter('email', '%' . $email . '%')
            ->orderBy('o.sendDatetime', $this->orderDirection)
            ->getQuery()
            ->getResult();

        foreach ($orders as $result) {
            $this->addOrderId($result['id']);
        }
    }

    private function addOrdersByClientFullname(string $fullname): void
    {
        // DONE
        $orders = $this->entityManager->createQueryBuilder()
            ->from(BaseLinkerOrders::class, 'o')
            ->select('o.id')
            ->leftJoin('o.baseLinkerAccount', 'a')
            ->innerJoin(BaseLinkerClients::class, 'c', 'WITH', 'o.client = c.id')
            ->where('a.company = :company')
            ->andWhere('c.fullname like :fullname')
            ->setParameter('company', $this->companyService->getLoggedUserCompany())
            ->setParameter('fullname', '%' . $fullname . '%')
            ->orderBy('o.sendDatetime', $this->orderDirection)
            ->getQuery()
            ->getResult();

        foreach ($orders as $result) {
            $this->addOrderId($result['id']);
        }
    }

    private function addOrdersByClientLogin(string $login): void
    {
        // DONE
        $orders = $this->entityManager->createQueryBuilder()
            ->from(BaseLinkerOrders::class, 'o')
            ->select('o.id')
            ->leftJoin('o.baseLinkerAccount', 'a')
            ->innerJoin(BaseLinkerClients::class, 'c', 'WITH', 'o.client = c.id')
            ->where('a.company = :company')
            ->andWhere('c.login like :login')
            ->setParameter('company', $this->companyService->getLoggedUserCompany())
            ->setParameter('login', '%' . $login . '%')
            ->orderBy('o.sendDatetime', $this->orderDirection)
            ->getQuery()
            ->getResult();

        foreach ($orders as $result) {
            $this->addOrderId($result['id']);
        }
    }

    public function addOrdersByClientCity(string $city): void
    {
        // DONE
        $orders = $this->entityManager->createQueryBuilder()
            ->from(BaseLinkerOrders::class, 'o')
            ->select('o.id')
            ->leftJoin('o.baseLinkerAccount', 'a')
            ->innerJoin(BaseLinkerClients::class, 'c', 'WITH', 'o.client = c.id')
            ->where('a.company = :company')
            ->andWhere('c.city like :city')
            ->setParameter('company', $this->companyService->getLoggedUserCompany())
            ->setParameter('city', '%' . $city . '%')
            ->orderBy('o.sendDatetime', $this->orderDirection)
            ->getQuery()
            ->getResult();

        foreach ($orders as $result) {
            $this->addOrderId($result['id']);
        }
    }

    private function addOrdersByClientPostCode(string $postCode): void
    {
        // DONE
        $orders = $this->entityManager->createQueryBuilder()
            ->from(BaseLinkerOrders::class, 'o')
            ->select('o.id')
            ->leftJoin('o.baseLinkerAccount', 'a')
            ->innerJoin(BaseLinkerClients::class, 'c', 'WITH', 'o.client = c.id')
            ->where('a.company = :company')
            ->andWhere('c.postCode like :postCode')
            ->setParameter('company', $this->companyService->getLoggedUserCompany())
            ->setParameter('postCode', '%' . $postCode . '%')
            ->orderBy('o.sendDatetime', $this->orderDirection)
            ->getQuery()
            ->getResult();

        foreach ($orders as $result) {
            $this->addOrderId($result['id']);
        }
    }

    private function addOrdersByClientPhoneNumber(string $phoneNumber): void
    {
        // DONE
        $orders = $this->entityManager->createQueryBuilder()
            ->from(BaseLinkerOrders::class, 'o')
            ->select('o.id')
            ->leftJoin('o.baseLinkerAccount', 'a')
            ->innerJoin(BaseLinkerClients::class, 'c', 'WITH', 'o.client = c.id')
            ->where('a.company = :company')
            ->andWhere('c.phone like :phoneNumber')
            ->setParameter('company', $this->companyService->getLoggedUserCompany())
            ->setParameter('phoneNumber', '%' . $phoneNumber . '%')
            ->orderBy('o.sendDatetime', $this->orderDirection)
            ->getQuery()
            ->getResult();

        foreach ($orders as $result) {
            $this->addOrderId($result['id']);
        }
    }

    private function addOrdersByOrderId(int $orderId): void
    {
        // DONE
        $orders = $this->entityManager->createQueryBuilder()
            ->from(BaseLinkerOrders::class, 'o')
            ->select('o.id')
            ->leftJoin('o.baseLinkerAccount', 'a')
            ->where('a.company = :company')
            ->andWhere('o.orderId = :orderId')
            ->setParameter('company', $this->companyService->getLoggedUserCompany())
            ->setParameter('orderId', $orderId)
            ->getQuery()
            ->getResult();

        foreach ($orders as $result) {
            $this->addOrderId($result['id']);
        }
    }

    private function addOrdersByShipmentNumber(string $shipmentNumber): void
    {
        // DONE
        $orders = $this->entityManager->createQueryBuilder()
            ->from(BaseLinkerOrders::class, 'o')
            ->select('o.id')
            ->leftJoin('o.baseLinkerAccount', 'a')
            ->innerJoin(BaseLinkerShipments::class, 's', 'WITH', 's.order_id = o.id')
            ->where('a.company = :company')
            ->andWhere('s.number like :shipmentNumber')
            ->setParameter('company', $this->companyService->getLoggedUserCompany())
            ->setParameter('shipmentNumber', '%' . $shipmentNumber . '%')
            ->getQuery()
            ->getResult();

        foreach ($orders as $result) {
            $this->addOrderId($result['id']);
        }
    }

    private function addOrdersByProductName(string $productName): void
    {
        $orders = $this->entityManager->createQueryBuilder()
            ->from(BaseLinkerOrders::class, 'o')
            ->select('o.id')
            ->leftJoin('o.baseLinkerAccount', 'a')
            ->innerJoin(BaseLinkerOrdersProducts::class, 'p', 'WITH', 'o.id = p.order_id')
            ->where('a.company = :company')
            ->andWhere('p.title like :title')
            ->setParameter('company', $this->companyService->getLoggedUserCompany())
            ->setParameter('title', '%' . $productName . '%')
            ->getQuery()
            ->getResult();

        foreach ($orders as $result) {
            $this->addOrderId($result['id']);
        }
    }

    private function addOrdersByStatus(BaseLinkerCatalogs $status): void
    {
        // DONE
        $orders = $this->entityManager->createQueryBuilder()
            ->from(BaseLinkerOrders::class, 'o')
            ->select('o.id')
            ->leftJoin('o.baseLinkerAccount', 'a')
            ->innerJoin(BaseLinkerCatalogs::class, 'c', 'WITH', 'o.directory_id = c.catalogId')
            ->where('a.company = :company')
            ->andWhere('c.id = :code')
            ->setParameter('company', $this->companyService->getLoggedUserCompany())
            ->setParameter('code', $status->getId())
            ->orderBy('o.sendDatetime', $this->orderDirection)
            ->getQuery()
            ->getResult();

        foreach ($orders as $result) {
            $this->addOrderId($result['id']);
        }
    }

    private function addOrdersByAssignedUser(User $user): void
    {
        $orders = $this->entityManager->createQueryBuilder()
            ->from(BaseLinkerOrders::class, 'o')
            ->select('o.id')
            ->leftJoin('o.baseLinkerAccount', 'a')
            ->where('a.company = :company')
            ->andWhere('o.assigned_user = :user')
            ->setParameter('company', $this->companyService->getLoggedUserCompany())
            ->setParameter('user', $user)
            ->orderBy('o.sendDatetime', $this->orderDirection)
            ->getQuery()
            ->getResult();

        foreach ($orders as $result) {
            $this->addOrderId($result['id']);
        }
    }

    private function addOrdersByOrderDates(string $dateFrom, string $dateTo): void
    {
        // DONE
        $orders = $this->entityManager->createQueryBuilder()
            ->from(BaseLinkerOrders::class, 'o')
            ->select('o.id')
            ->leftJoin('o.baseLinkerAccount', 'a')
            ->where('a.company = :company')
            ->andWhere('date(o.orderDatetime) between :dateFrom and :dateTo')
            ->setParameter('company', $this->companyService->getLoggedUserCompany())
            ->setParameter('dateFrom', $dateFrom)
            ->setParameter('dateTo', $dateTo)
            ->orderBy('o.sendDatetime', $this->orderDirection)
            ->getQuery()
            ->getResult();

        foreach ($orders as $result) {
            $this->addOrderId($result['id']);
        }
    }

    private function addOrdersBySendDates(string $dateFrom, string $dateTo): void
    {
        // DONE
        $orders = $this->entityManager->createQueryBuilder()
            ->from(BaseLinkerOrders::class, 'o')
            ->select('o.id')
            ->leftJoin('o.baseLinkerAccount', 'a')
            ->where('a.company = :company')
            ->andWhere('date(o.sendDatetime) between :dateFrom and :dateTo')
            ->setParameter('company', $this->companyService->getLoggedUserCompany())
            ->setParameter('dateFrom', $dateFrom)
            ->setParameter('dateTo', $dateTo)
            ->orderBy('o.sendDatetime', $this->orderDirection)
            ->getQuery()
            ->getResult();

        foreach ($orders as $result) {
            $this->addOrderId($result['id']);
        }
    }

    private function addOrdersByStatusDates(string $dateFrom, string $dateTo): void
    {
        $orders = $this->entityManager->createQueryBuilder()
            ->from(BaseLinkerOrders::class, 'o')
            ->select('o.id')
            ->leftJoin('o.baseLinkerAccount', 'a')
            ->where('a.company = :company')
            ->andWhere('date(o.statusDatetime) between :dateFrom and :dateTo')
            ->setParameter('company', $this->companyService->getLoggedUserCompany())
            ->setParameter('dateFrom', $dateFrom)
            ->setParameter('dateTo', $dateTo)
            ->orderBy('o.sendDatetime', $this->orderDirection)
            ->getQuery()
            ->getResult();

        foreach ($orders as $result) {
            $this->addOrderId($result['id']);
        }
    }

    public function getOrderIds(): array
    {
        return $this->ordersIds;
    }
}
