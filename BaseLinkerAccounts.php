<?php

namespace managerBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use managerBundle\Repository\BaseLinkerAccountsRepository;

#[Entity(repositoryClass: BaseLinkerAccountsRepository::class)]
#[Table(name: 'base_linker_accounts')]
class BaseLinkerAccounts
{
    #[Id]
    #[GeneratedValue]
    #[Column(name: 'id', type: 'integer')]
    private $id;

    #[ManyToOne(targetEntity: Companies::class)]
    #[JoinColumn(name: 'company_id')]
    private Companies $company;

    #[Column(name: 'base_linker_api_key', type: 'string', length: 100)]
    private $baseLinkerApiKey;

    #[Column(name: 'base_linker_name', type: 'string', length: 50)]
    private $baseLinkerName;

    #[Column(name: 'infact_api_key', type: 'string', length: 100)]
    private $inFactApiKey;

    #[Column(name: 'last_catalogs_update', type: 'datetime', nullable: true)]
    private $lastCatalogsUpdate;

    #[Column(name: 'last_journal_log_id', type: 'bigint', nullable: true, options: ['unsigned' => true])]
    private $lastJournalLogId;

    #[Column(name: 'last_journal_log_date', type: 'datetime', nullable: true)]
    private $lastJournalLogDate;

    #[Column(name: 'refund_money_limit', type: 'json', nullable: true)]
    private $refundMoneyLimit;

    #[Column(name: 'refund_order_duration', type: 'integer', nullable: true)]
    private $refundOrderDuration;

    #[Column(name: 'firm_data', type: 'string', nullable: true)]
    private ?string $firmData;

    #[Column(name: 'refund_allow_all', type: 'json', nullable: true)]
    private array $refundAllowAll;

    #[Column(name: 'infakt_additional_config', type: 'json', nullable: true)]
    private ?array $inFaktAdditionalConfig;

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getBaseLinkerApiKey(): string
    {
        return $this->baseLinkerApiKey;
    }

    /**
     * @param string $baseLinkerApiKey
     */
    public function setBaseLinkerApiKey(string $baseLinkerApiKey): void
    {
        $this->baseLinkerApiKey = $baseLinkerApiKey;
    }

    /**
     * @return string
     */
    public function getBaseLinkerName(): string
    {
        return $this->baseLinkerName;
    }

    /**
     * @param string $baseLinkerName
     */
    public function setBaseLinkerName(string $baseLinkerName): void
    {
        $this->baseLinkerName = $baseLinkerName;
    }

    /**
     * @return string
     */
    public function getInFactApiKey(): string
    {
        return $this->inFactApiKey;
    }

    /**
     * @param string $inFactApiKey
     */
    public function setInFactApiKey(string $inFactApiKey): void
    {
        $this->inFactApiKey = $inFactApiKey;
    }

    /**
     * @return DateTime
     */
    public function getLastCatalogsUpdate(): ?DateTime
    {
        return $this->lastCatalogsUpdate;
    }

    /**
     * @param DateTime|null $lastCatalogsUpdate
     */
    public function setLastCatalogsUpdate(DateTime $lastCatalogsUpdate = null): void
    {
        $this->lastCatalogsUpdate = $lastCatalogsUpdate;
    }

    /**
     * @return int
     */
    public function getLastJournalLogId(): ?int
    {
        return $this->lastJournalLogId;
    }

    /**
     * @param int $lastJournalLogId
     */
    public function setLastJournalLogId(int $lastJournalLogId = 0): void
    {
        $this->lastJournalLogId = $lastJournalLogId;
    }

    /**
     * @return DateTime
     */
    public function getLastJournalLogDate(): ?DateTime
    {
        return $this->lastJournalLogDate;
    }

    /**
     * @param DateTime|null $lastJournalLogDate
     */
    public function setLastJournalLogDate(DateTime $lastJournalLogDate = null): void
    {
        $this->lastJournalLogDate = $lastJournalLogDate;
    }

    /**
     * @return null|array
     */
    public function getRefundMoneyLimit(): ?array
    {
        return $this->refundMoneyLimit;
    }

    /**
     * @param array $refundMoneyLimit
     */
    public function setRefundMoneyLimit(array $refundMoneyLimit = []): void
    {
        $this->refundMoneyLimit = $refundMoneyLimit;
    }

    /**
     * @return null|int
     */
    public function getRefundOrderDuration(): ?int
    {
        return $this->refundOrderDuration;
    }

    /**
     * @param int $refundOrderDuration
     */
    public function setRefundOrderDuration(int $refundOrderDuration): void
    {
        $this->refundOrderDuration = $refundOrderDuration;
    }

    /**
     * @return string|null
     */
    public function getFirmData(): ?string
    {
        return $this->firmData;
    }

    /**
     * @param string|null $firmData
     */
    public function setFirmData(?string $firmData): void
    {
        $this->firmData = $firmData;
    }

    /**
     * @return null|array
     */
    public function getRefundAllowAll(): ?array
    {
        return $this->refundAllowAll;
    }

    /**
     * @param array $refundAllowAll
     */
    public function setRefundAllowAll(array $refundAllowAll = []): void
    {
        $this->refundAllowAll = $refundAllowAll;
    }

    /**
     * @return array
     */
    public function getInFaktAdditionalConfig(): ?array
    {
        return $this->inFaktAdditionalConfig;
    }

    /**
     * @param array $inFaktAdditionalConfig
     */
    public function setInFaktAdditionalConfig(array $inFaktAdditionalConfig = []): void
    {
        $this->inFaktAdditionalConfig = $inFaktAdditionalConfig;
    }

    /**
     * @return Companies
     */
    public function getCompany(): Companies
    {
        return $this->company;
    }

    /**
     * @param Companies $company
     */
    public function setCompany(Companies $company): void
    {
        $this->company = $company;
    }
}
