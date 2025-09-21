<?php
/**
 * This file is part of the it-quasar/atol-online library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ItQuasar\AtolOnline;

use InvalidArgumentException;
use ItQuasar\AtolOnline\Exception\SdkException;
use function array_map;
use function count;

/**
 * Коррекция.
 */
class CorrectionV5 implements RequestPart
{
    /****************************
     * Атрибуты чека коррекции *
     ****************************/

    /** @var null|Client $client */
    private $client = null;

    /** @var null|Company $company */
    private $company = null;

    /** @var null|CorrectionInfo $correctionInfo */
    private $correctionInfo = null;

    /** @var ItemV5[] $items */
    private $items = [];

    /** @var Payment[] $payments */
    private $payments = [];

    /** @var Vat[] $vats */
    private $vats = [];

    /** @var bool $internet */
    private $internet = true;

    /** @var null|string $cashier */
    private $cashier = null;

    /** @var null|string $cashier */
    private $cashierInn = null;

    /**
     * Дополнительный реквизит чека.
     * Обычно используется для передачи ФПД чека, содержащего ошибку.
     *
     * @var null|string $additionalCheckProps
     */
    private $additionalCheckProps = null;

    /** @var float $total*/
    private $total = null;

    /** @var null|AdditionalUserProps $additionalUserProps */
    private $additionalUserProps = null;

    /** @var bool $isCheckItemsCount */
    private $isCheckItemsCount = true;


    /**********************************
     * Методы для работы с атрибутами *
     **********************************/

    /**
     * Возвращает атрибуты клиента.
     *
     * @return Client|null
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Устанавливает атрибуты клиента.
     *
     * @param Client $client
     *
     * @return $this
     */
    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Возвращает атрибуты компании.
     *
     * @return Company
     */
    public function getCompany(): Company
    {
        return $this->company;
    }

    /**
     * Устанавливает атрибуты компании.
     *
     * @param Company $company
     *
     * @return $this
     */
    public function setCompany(Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Возвращает коррекцию.
     *
     * @return CorrectionInfo
     */
    public function getCorrectionInfo(): CorrectionInfo
    {
        return $this->correctionInfo;
    }

    /**
     * Устанавливает атрибуты ин.
     *
     * @param CorrectionInfo $correctionInfo
     *
     * @return $this
     */
    public function setCorrectionInfo(CorrectionInfo $correctionInfo): self
    {
        $this->correctionInfo = $correctionInfo;

        return $this;
    }

    /**
     * Возвращает позиции чека.
     *
     * @return ItemV5[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Устанавливает позиции чека.
     *
     * Ограничение по количеству от 1 до 100.
     *
     * @param ItemV5[] $items
     *
     * @return $this
     */
    public function setItems(array $items): self
    {
        $count = count($items);
        if ($this->isCheckItemsCount && (0 == $count || $count > 100)) {
            throw new InvalidArgumentException('Items count must be >= 1 and <= 100');
        }

        $this->items = $items;

        return $this;
    }

    /**
     * Добавляет позицию чека.
     *
     * Ограничение по количеству от 1 до 100.
     *
     * @param ItemV5 $item
     */
    public function addItem(ItemV5 $item): void
    {
        if ($this->isCheckItemsCount && count($this->items) >= 100) {
            throw new InvalidArgumentException('Items full. Max items count = 100');
        }

        $this->items[] = $item;
    }

    /**
     * Возвращает оплату.
     *
     * @return Payment[]
     */
    public function getPayments(): array
    {
        return $this->payments;
    }

    /**
     * Устанавливает оплату.
     *
     * Ограничение по количеству от 1 до 10.
     *
     * @param Payment[] $payments
     *
     * @return $this
     */
    public function setPayments(array $payments): self
    {
        if (0 == count($payments) || count($payments) > 10) {
            throw new InvalidArgumentException('Payments count must be > 1 and < 10');
        }

        $this->payments = $payments;

        return $this;
    }

    /**
     * Добавляет оплату.
     *
     * Ограничение по количеству от 1 до 10.
     *
     * @param Payment $payment
     */
    public function addPayment(Payment $payment): void
    {
        if (10 == count($this->payments)) {
            throw new InvalidArgumentException('Payments full. Max payments count = 10');
        }

        $this->payments[] = $payment;
    }

    /**
     * Возвращает значение признака применения ККТ при осуществлении
     * расчета в безналичном порядке в сети "Интернет"
     *
     * @return bool
     */
    public function getInternet(): bool
    {
        return $this->internet;
    }

    /**
     * Устанавливает признак применения ККТ при осуществлении расчета
     * в безналичном порядке в сети "Интернет".
     *
     * @param bool $internet
     * @return $this
     */
    public function setInternet(bool $internet): self
    {
        $this->internet = $internet;

        return $this;
    }

    /**
     * Возвращает атрибуты налогов на чек коррекции.
     *
     * @return Vat[]
     */
    public function getVats(): array
    {
        return $this->vats;
    }

    /**
     * Устанавлиает атрибуты налога на чек коррекции.
     *
     * Ограничение по количеству от 1 до 6.
     *
     * Необходимо передать либо сумму налога на позицию, либо сумму налога на чек. Если будет переданы и сумма налога
     * на позицию и сумма налога на чек, сервис учтет только сумму налога на чек.
     *
     * @param array $vats
     *
     * @return $this
     */
    public function setVats(array $vats): self
    {
        if (0 === count($vats) || count($vats) > 6) {
            throw new InvalidArgumentException('Vats count must be less then 7');
        }

        $this->vats = $vats;

        return $this;
    }

    /**
     * Добавляет атрибут налога на чек коррекции.
     *
     * Ограничение по количеству от 1 до 6.
     *
     * Необходимо передать либо сумму налога на позицию, либо сумму налога на чек. Если будет переданы и сумма налога
     * на позицию и сумма налога на чек, сервис учтет только сумму налога на чек.
     *
     * @param Vat $vat
     *
     * @return $this
     */
    public function addVat(Vat $vat): self
    {
        if (count($this->vats) === 6) {
            throw new InvalidArgumentException('Vats full. Max vats count = 6');
        }

        $this->vats[] = $vat;

        return $this;
    }

    /**
     * Возвращает ФИО кассира.
     *
     * @return string|null
     */
    public function getCashier(): ?string
    {
        return $this->cashier;
    }

    /**
     * Устанавливает ФИО кассира.
     *
     * Максимальная длина строки – 64 символа.
     *
     * @param string|null $cashier
     *
     * @return $this
     */
    public function setCashier(?string $cashier): self
    {
        if (mb_strlen($cashier) > 64) {
            throw new InvalidArgumentException('Cashier too big. Max length size = 64');
        }

        $this->cashier = $cashier;

        return $this;
    }

    /**
     * Возвращает ИНН кассира.
     *
     * @return string|null
     */
    public function getCashierInn(): ?string
    {
        return $this->cashier;
    }

    /**
     * Устанавливает ИНН кассира.
     *
     * Длина строки ровно 12 символов.
     *
     * @param string|null $cashier
     *
     * @return $this
     */
    public function setCashierInn(?string $cashier): self
    {
        if (mb_strlen($cashier) != 12) {
            throw new InvalidArgumentException('CashierInn must be 12 characters');
        }

        $this->cashier = $cashier;

        return $this;
    }

    /**
     * Возвращает дополнительный реквизит чека.
     *
     * @return string|null
     */
    public function getAdditionalCheckProps(): ?string
    {
        return $this->additionalCheckProps;
    }

    /**
     * Устанавливает дополнительный реквизит чека.
     *
     * Максимальная длина строки – 16 символов.
     *
     * @param string|null $additionalCheckProps
     *
     * @return $this
     */
    public function setAdditionalCheckProps(?string $additionalCheckProps): self
    {
        if (mb_strlen($additionalCheckProps) > 16) {
            throw new InvalidArgumentException('AdditionalCheckProps too big. Max length size = 16');
        }

        $this->additionalCheckProps = $additionalCheckProps;

        return $this;
    }

    /**
     * Возвращает итоговую сумму чека в рублях.
     *
     * @return float
     */
    public function getTotal(): float
    {
        return $this->total;
    }

    /**
     * Устанавливает итоговую сумму чека в рублях:
     * - целая часть не более 8 знаков;
     * - дробная часть не более 2 знаков.
     *
     * @param float $total
     *
     * @return $this
     */
    public function setTotal(float $total): self
    {
        if ($total > 99_999_999) {
            throw new InvalidArgumentException('Total too big. Max = 99_999_999');
        }

        $this->total = $total;

        return $this;
    }

    /**
     * Возвращает дополнительный реквизит пользователя.
     *
     * @return AdditionalUserProps|null
     */
    public function getAdditionalUserProps(): ?AdditionalUserProps
    {
        return $this->additionalUserProps;
    }

    /**
     * Устанавлиает дополнительный реквизит пользователя.
     *
     * @param AdditionalUserProps|null $additionalUserProps
     *
     * @return $this
     */
    public function setAdditionalUserProps(?AdditionalUserProps $additionalUserProps): self
    {
        $this->additionalUserProps = $additionalUserProps;

        return $this;
    }

    public function toArray(): array
    {
        if ($this->internet && is_null($this->client)) {
            throw new SdkException('Client required');
        }

        if (is_null($this->company)) {
            throw new SdkException('Company required');
        }

        if (is_null($this->correctionInfo)) {
            throw new SdkException('Correction info required');
        }

        if (count($this->items) == 0) {
            throw new SdkException('More then one item required');
        }

        if (0 == count($this->payments)) {
            throw new SdkException('More then one payment required');
        }

        if (0 == count($this->vats)) {
            throw new SdkException('More then one vat required');
        }

        $result = [
            'company' => $this->company->toArray(),
            'correction_info' => $this->correctionInfo->toArray(),
            'items' => array_map(function (ItemV5 $item) {
                return $item->toArray();
            }, $this->items),
            'payments' => array_map(function (Payment $payment) {
                return $payment->toArray();
            }, $this->payments),
            'vats' => array_map(function (Vat $vat) {
                return $vat->toArray();
            }, $this->vats),
            'internet' => $this->internet,
            'total' => round($this->total, 2),
        ];

        if (!is_null($this->client)) {
            $result['client'] = $this->client->toArray();
        }

        if (!is_null($this->cashier)) {
            $result['cashier'] = $this->cashier;
        }

        if (!is_null($this->cashierInn)) {
            $result['cashier_inn'] = $this->cashierInn;
        }

        if (!is_null($this->additionalCheckProps)) {
            $result['additional_check_props'] = $this->additionalCheckProps;
        }

        if (!is_null($this->additionalUserProps)) {
            $result['additional_user_props'] = $this->additionalUserProps->toArray();
        }

        return $result;
    }
}
