<?php

namespace managerBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use managerBundle\Repository\BaseLinkerOrdersRepository;

#[Entity(repositoryClass: BaseLinkerOrdersRepository::class)]
#[Table(name: 'base_linker_orders')]
#[Index(columns: ['directory_id'])]
#[Index(columns: ['external_order_id'])]
#[Index(columns: ['order_id'])]
#[Index(columns: ['source_id'])]
class BaseLinkerOrders
{
    #[Id]
    #[GeneratedValue]
    #[Column(name: 'id', type: 'integer')]
    private $id;

    #[Column(name: 'order_id', type: 'integer')]
    private $orderId;

    #[ManyToOne(targetEntity: BaseLinkerClients::class)]
    #[JoinColumn(name: 'client')]
    private $client;

    #[Column(name: 'order_datetime', type: 'datetime')]
    private $orderDatetime;

    #[Column(name: 'send_datetime', type: 'datetime', nullable: true)]
    private $sendDatetime;

    #[Column(name: 'status_datetime', type: 'datetime', nullable: true)]
    private $statusDatetime;

    #[Column(name: 'insert_datetime', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private $insertDatetime;

    #[Column(name: 'user_comments', type: 'text', nullable: true)]
    private $userComments;

    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(name: 'assigned_user')]
    private $assigned_user;

    #[Column(name: 'directory_id', type: 'integer')]
    private $directory_id;

    #[ManyToOne(targetEntity: BaseLinkerOrdersStatuses::class)]
    #[JoinColumn(name: 'status')]
    private $status;

    #[Column(name: 'admin_comments', type: 'text')]
    private $adminComments;

    #[Column(name: 'extra_field1', type: 'text')]
    private $extraField1;

    #[Column(name: 'extra_field2', type: 'text')]
    private $extraField2;

    #[Column(name: 'payment_method', type: 'string', nullable: true)]
    private $paymentMethod;

    #[Column(name: 'invoice_number', type: 'string', nullable: true)]
    private $invoiceNumber;

    #[Column(name: 'want_invoice', type: 'boolean', nullable: true)]
    private $wantInvoice;

    #[Column(name: 'invoice_nip', type: 'string', length: 20, nullable: true)]
    private $invoiceNip;

    #[Column(name: 'external_order_id', type: 'string', length: 50, nullable: true)]
    private $externalOrderId;

    #[ManyToOne(targetEntity: BaseLinkerAccounts::class)]
    #[JoinColumn(name: 'base_linker_account_id')]
    private $baseLinkerAccount;

    #[OneToMany(mappedBy: 'order_id', targetEntity: BaseLinkerOrdersProducts::class)]
    private $products;

    #[Column(name: 'courier_code', type: 'string', length: 20, nullable: true)]
    private $courierCode;
    #[Column(name: 'source_id', type: 'integer')]
    private int $sourceId;
    private $shipment;
    private $catalog;


    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Get orderId
     *
     * @return integer
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set orderId
     *
     * @param integer $orderId
     *
     * @return BaseLinkerOrders
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get client
     *
     * @return BaseLinkerClients
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set client
     *
     * @param BaseLinkerClients $client
     *
     * @return BaseLinkerOrders
     */
    public function setClient(BaseLinkerClients $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get orderDatetime
     *
     * @return DateTime
     */
    public function getOrderDatetime()
    {
        return $this->orderDatetime;
    }

    /**
     * Set orderDatetime
     *
     * @param DateTime $orderDatetime
     *
     * @return BaseLinkerOrders
     */
    public function setOrderDatetime($orderDatetime)
    {
        $this->orderDatetime = $orderDatetime;

        return $this;
    }

    /**
     * Get directoryId
     *
     * @return integer
     */
    public function getDirectoryId()
    {
        return $this->directory_id;
    }

    /**
     * Set directoryId
     *
     * @param integer $directoryId
     *
     * @return BaseLinkerOrders
     */
    public function setDirectoryId($directoryId)
    {
        $this->directory_id = $directoryId;

        return $this;
    }

    /**
     * Get status
     *
     * @return BaseLinkerOrdersStatuses
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param null $status
     * @return BaseLinkerOrders
     */
    public function setStatus($status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getShipment()
    {
        return $this->shipment;
    }

    /**
     * @param $shipment
     * @return $this
     */
    public function setShipment(BaseLinkerShipments $shipment = null)
    {
        $this->shipment = $shipment;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCourierCode()
    {
        return $this->courierCode;
    }

    /**
     * @param $courierCode
     * @return $this
     */
    public function setCourierCode($courierCode = null)
    {
        $this->courierCode = $courierCode;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCatalog()
    {
        return $this->catalog;
    }

    /**
     * @param $catalog
     * @return $this
     */
    public function setCatalog(BaseLinkerCatalogs $catalog = null)
    {
        $this->catalog = $catalog;

        return $this;
    }

    /**
     * Get assignedUser
     *
     * @return User
     */
    public function getAssignedUser()
    {
        return $this->assigned_user;
    }

    /**
     * Set assignedUser
     *
     * @param User $assignedUser
     *
     * @return BaseLinkerOrders
     */
    public function setAssignedUser(User $assignedUser = null)
    {
        $this->assigned_user = $assignedUser;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getSendDatetime()
    {
        return $this->sendDatetime;
    }

    /**
     * @param DateTime $sendDatetime
     * @return BaseLinkerOrders
     */
    public function setSendDatetime(DateTime $sendDatetime): self
    {
        $this->sendDatetime = $sendDatetime;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStatusDatetime()
    {
        return $this->statusDatetime;
    }

    /**
     * @param DateTime $statusDatetime
     * @return BaseLinkerOrders
     */
    public function setStatusDatetime(DateTime $statusDatetime): self
    {
        $this->statusDatetime = $statusDatetime;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getInsertDatetime()
    {
        return $this->insertDatetime;
    }

    /**
     * @param DateTime $insertDatetime
     * @return BaseLinkerOrders
     */
    public function setInsertDatetime(DateTime $insertDatetime): self
    {
        $this->insertDatetime = $insertDatetime;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserComments()
    {
        return $this->userComments;
    }

    /**
     * @param $userComments
     * @return $this
     */
    public function setUserComments($userComments = null)
    {
        $this->userComments = $userComments;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getAdminComments()
    {
        return $this->adminComments;
    }

    /**
     * @param $adminComments
     * @return $this
     */
    public function setAdminComments($adminComments = null)
    {
        $this->adminComments = $adminComments;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getExtraField1()
    {
        return $this->extraField1;
    }

    /**
     * @param $extraField1
     * @return $this
     */
    public function setExtraField1($extraField1 = null)
    {
        $this->extraField1 = $extraField1;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getExtraField2()
    {
        return $this->extraField2;
    }

    /**
     * @param $extraField2
     * @return $this
     */
    public function setExtraField2($extraField2 = null)
    {
        $this->extraField2 = $extraField2;
        return $this;
    }

    public function addProducts(BaseLinkerOrdersProducts $product): void
    {
        $this->products->add($product);
    }

    public function removeProducts(BaseLinkerOrdersProducts $product): void
    {
        $this->products->remove($product);
    }

    public function getProducts()
    {
        return $this->products;
    }

    public function setProducts(ArrayCollection $products): void
    {
        $this->products = $products;
    }

    public function getTotalPrice()
    {
        $totalPrice = 0;

        foreach ($this->products as $product) {
            $totalPrice += $product->getPrice();
        }

        return $totalPrice;
    }

    /**
     * Add product
     *
     * @param BaseLinkerOrdersProducts $product
     *
     * @return BaseLinkerOrders
     */
    public function addProduct(BaseLinkerOrdersProducts $product)
    {
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param BaseLinkerOrdersProducts $product
     */
    public function removeProduct(BaseLinkerOrdersProducts $product)
    {
        $this->products->removeElement($product);
    }

    /**
     * @return string
     */
    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    /**
     * @param string $paymentMethod
     * @return $this
     */
    public function setPaymentMethod($paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * @return string
     */
    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    /**
     * @param string $invoiceNumber
     */
    public function setInvoiceNumber(string $invoiceNumber): void
    {
        $this->invoiceNumber = $invoiceNumber;
    }

    /**
     * @return BaseLinkerAccounts
     */
    public function getBaseLinkerAccount(): BaseLinkerAccounts
    {
        return $this->baseLinkerAccount;
    }

    /**
     * @param BaseLinkerAccounts $baseLinkerAccount
     */
    public function setBaseLinkerAccount(BaseLinkerAccounts $baseLinkerAccount): void
    {
        $this->baseLinkerAccount = $baseLinkerAccount;
    }

    public function orderDatetimePl(): string
    {
        return
            $this->orderDatetime->format('d') . ' ' .
            $this->getMonthPl($this->orderDatetime->format('m')) . ' ' .
            $this->orderDatetime->format('Y');
    }

    /**
     * @param int $monthNumber
     * @return string
     */
    private function getMonthPl(int $monthNumber): string
    {
        $moth = [
            'stycznia',
            'lutego',
            'marca',
            'kwietnia',
            'maja',
            'czerwca',
            'lipca',
            'sierpnia',
            'września',
            'października',
            'listopada',
            'grudnia'
        ];
        return $moth[$monthNumber];
    }

    /**
     * @return bool|null
     */
    public function getWantInvoice(): ?bool
    {
        return $this->wantInvoice;
    }

    /**
     * @param bool $wantInvoice
     * @return $this
     */
    public function setWantInvoice(bool $wantInvoice): self
    {
        $this->wantInvoice = $wantInvoice;
        return $this;
    }

    /**
     * @return string
     */
    public function getInvoiceNip(): ?string
    {
        return $this->invoiceNip;
    }

    /**
     * @param string|null $invoiceNip
     */
    public function setInvoiceNip(?string $invoiceNip): void
    {
        $this->invoiceNip = $invoiceNip;
    }

    /**
     * @return string
     */
    public function getExternalOrderId(): ?string
    {
        return $this->externalOrderId;
    }

    /**
     * @param string|null $externalOrderId
     */
    public function setExternalOrderId(?string $externalOrderId): void
    {
        $this->externalOrderId = $externalOrderId;
    }

    public function getSourceId(): int
    {
        return $this->sourceId;
    }

    public function setSourceId(int $sourceId): void
    {
        $this->sourceId = $sourceId;
    }

    public function fillBaseLinkerOrderObject(
        array $data,
        BaseLinkerAccounts $account
    ): void {
        $this->setOrderDatetime(new DateTime(date('Y-m-d H:i:s', $data['date_add'])));
        $this->setStatusDatetime(new DateTime(date('Y-m-d H:i:s', $data['date_in_status'])));
        $this->setOrderId($data['order_id']);
        $this->setCourierCode($data['delivery_package_module']);
        $this->setUserComments($data['user_comments']);
        $this->setAdminComments($data['admin_comments']);
        $this->setExtraField1($data['extra_field_1']);
        $this->setExtraField2($data['extra_field_2']);
        $this->setWantInvoice($data['want_invoice']);
        $this->setInvoiceNip(empty($data['invoice_nip']) ? null : $data['invoice_nip']);
        $this->setExternalOrderId($data['external_order_id']);
        $this->setDirectoryId($data['order_status_id']);
        $this->setBaseLinkerAccount($account);
        $this->setSourceId($data['order_source_id']);
    }
}
