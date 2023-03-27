<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'stock')]
#[ORM\Entity(repositoryClass: 'App\Repository\StockRepository')]
#[UniqueEntity('symbol')]
class Stock
{
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(name: 'name', type: 'string', length: 50)]
    private string $name;

    #[ORM\Column(name: 'symbol', type: 'string', length: 20, unique: true)]
    private string $symbol;

    #[ORM\Column(name: 'currency', type: 'string', length: 3)]
    private string $currency;

    #[ORM\Column(name: 'quantity', type: 'decimal', precision: 8, scale: 2, nullable: true)]
    private ?float $quantity;

    #[ORM\Column(name: 'initialPrice', type: 'decimal', precision: 8, scale: 2, nullable: true)]
    private ?float $initialPrice;

    #[ORM\Column(name: 'currentPrice', type: 'decimal', precision: 8, scale: 2, nullable: true)]
    private ?float $currentPrice;

    #[ORM\Column(name: 'currentChange', type: 'decimal', precision: 8, scale: 2, nullable: true)]
    private ?float $currentChange;

    #[ORM\Column(name: 'createdAt', type: 'datetime')]
    private DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updatedAt', type: 'datetime', nullable: true)]
    private ?DateTimeInterface $updatedAt;

    #[ORM\Column(name: 'displayChart', type: 'boolean')]
    private bool $displayChart = true;

    /**
     * Init the object
     */
    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    ////////////////////////////////////////////////////////////////////////////////////////// CONVENIENCE

    /**
     * Return invested money
     * @return float|null
     */
    public function getInvestment(): float|null
    {
        if ($this->quantity && $this->initialPrice) {
            return $this->quantity * $this->initialPrice;
        } else {
            return null;
        }
    }

    /**
     * Get current value of investment
     */
    public function getCurrentValue(): float|null
    {
        if ($this->quantity && $this->initialPrice && $this->currentPrice) {
            return $this->quantity * $this->currentPrice;
        } else {
            return null;
        }
    }

    /**
     * Return profit
     * @return float|null
     */
    public function getProfit(): ?float
    {
        if ($this->quantity && $this->initialPrice && $this->currentPrice) {
            return $this->getCurrentValue() - $this->getInvestment();
        } else {
            return null;
        }
    }

    /**
     * Return profit percentage
     * @return float
     */
    public function getProfitPercent(): float
    {
        return $this->getProfit() / $this->getInvestment();
    }

    /**
     * Get percent of current change
     * @return float
     */
    public function getCurrentChangePercent(): float
    {
        $oldPrice = $this->currentPrice - $this->currentChange;
        if ($oldPrice) {
            return $this->currentChange / $oldPrice;
        }
        return 0;
    }

    ////////////////////////////////////////////////////////////////////////////////////////// GETTER / SETTER

    /**
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Stock
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $symbol
     * @return Stock
     */
    public function setSymbol(string $symbol): static
    {
        $this->symbol = $symbol;
        return $this;
    }

    /**
     * @return string
     */
    public function getSymbol(): string
    {
        return $this->symbol;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return Stock
     */
    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @param float $quantity
     * @return Stock
     */
    public function setQuantity(float $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    /**
     * @param float $initialPrice
     * @return Stock
     */
    public function setInitialPrice(float $initialPrice): static
    {
        $this->initialPrice = $initialPrice;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getInitialPrice(): ?float
    {
        return $this->initialPrice;
    }

    /**
     * @return float|null
     */
    public function getCurrentPrice(): ?float
    {
        return $this->currentPrice;
    }

    /**
     * @param float $currentPrice
     * @return Stock
     */
    public function setCurrentPrice(float $currentPrice): static
    {
        $this->currentPrice = $currentPrice;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getCurrentChange(): ?float
    {
        return $this->currentChange;
    }

    /**
     * @param float $currentChange
     * @return Stock
     */
    public function setCurrentChange(float $currentChange): static
    {
        $this->currentChange = $currentChange;
        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     * @return Stock
     */
    public function setUpdatedAt(DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return DateTime|DateTimeInterface
     */
    public function getCreatedAt(): DateTime|DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     * @return Stock
     */
    public function setCreatedAt(DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isDisplayChart(): bool
    {
        return $this->displayChart;
    }

    /**
     * @param boolean $displayChart
     *
     * @return Stock
     */
    public function setDisplayChart(bool $displayChart): static
    {
        $this->displayChart = $displayChart;
        return $this;
    }
}
