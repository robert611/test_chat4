<?php

namespace managerBundle\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;
use managerBundle\Repository\BaseLinkerClientsRepository;

#[Entity(repositoryClass: BaseLinkerClientsRepository::class)]
#[Table(name: 'base_linker_clients')]
#[Index(columns: ['phone'])]
#[Index(columns: ['fullname'])]
class BaseLinkerClients
{
    #[Id]
    #[GeneratedValue]
    #[Column(name: 'id', type: 'integer')]
    private $id;

    #[Column(name: 'email', type: 'string')]
    private $email;

    #[Column(name: 'phone', type: 'string')]
    private $phone;

    #[Column(name: 'login', type: 'string')]
    private $login;

    #[Column(name: 'fullname', type: 'string')]
    private $fullname;

    #[Column(name: 'city', type: 'string')]
    private $city;

    #[Column(name: 'post_code', type: 'string', length: 16)]
    private $postCode;

    #[Column(name: 'country', type: 'string', length: 64)]
    private $country;

    #[Column(name: 'street', type: 'string')]
    private $street;

    #[OneToOne(targetEntity: BaseLinkerClientInvoice::class, fetch: 'EAGER')]
    #[JoinColumn(name: 'base_linker_client_invoice_id')]
    private $baseLinkerClientInvoice;

    /** @var integer */
    private $clientId;

    public function getId()
    {
        return $this->id;
    }

    /**
     * Get clientId
     *
     * @return integer
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Set clientId
     *
     * @param integer $clientId
     *
     * @return BaseLinkerClients
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return BaseLinkerClients
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return BaseLinkerClients
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get fullname
     *
     * @return string
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Set fullname
     *
     * @param string $fullname
     *
     * @return BaseLinkerClients
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return BaseLinkerClients
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get postCode
     *
     * @return string
     */
    public function getPostCode()
    {
        return $this->postCode;
    }

    /**
     * Set postCode
     *
     * @param string $postCode
     *
     * @return BaseLinkerClients
     */
    public function setPostCode($postCode)
    {
        $this->postCode = $postCode;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return BaseLinkerClients
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set street
     *
     * @param string $street
     *
     * @return BaseLinkerClients
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getLogin(): ?string
    {
        return $this->login;
    }

    /**
     * Set login
     *
     * @param string $login
     *
     * @return BaseLinkerClients
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * @return BaseLinkerClientInvoice
     */
    public function getInvoice(): ?BaseLinkerClientInvoice
    {
        return $this->baseLinkerClientInvoice;
    }

    /**
     * @param BaseLinkerClientInvoice|null $baseLinkerClientInvoice
     */
    public function setInvoice(?BaseLinkerClientInvoice $baseLinkerClientInvoice): void
    {
        $this->baseLinkerClientInvoice = $baseLinkerClientInvoice;
    }

    public function fillBaseLinkerClientObject(array $data, BaseLinkerClientInvoice $clientInvoice = null): void
    {
        $this->setEmail($data['email']);
        $this->setFullname($data['delivery_fullname']);
        $this->setPhone($data['phone']);
        $this->setCity($data['delivery_city']);
        $this->setPostCode($data['delivery_postcode']);
        $this->setCountry($data['delivery_country']);
        $this->setStreet($data['delivery_address']);
        $this->setLogin($data['user_login']);
        $this->setInvoice($clientInvoice);
    }
}
